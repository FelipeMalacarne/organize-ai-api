#!/bin/bash

# Path to the .env.production file
ENV_FILE=".env.production"

# Check if .env.production file exists
if [[ ! -f "$ENV_FILE" ]]; then
  echo "Error: $ENV_FILE file not found."
  exit 1
fi

# Function to create secret and add latest version
create_secret() {
  local secret_name=$1
  local secret_value=$2

  echo "Creating secret: $secret_name"

  # Check if secret already exists
  gcloud secrets describe "$secret_name" --project="$PROJECT_ID" &>/dev/null
  if [ $? -ne 0 ]; then
    # Create the secret if it doesn't exist
    gcloud secrets create "$secret_name" --project="$PROJECT_ID"
  fi

  # Add the secret version
  echo -n "$secret_value" | gcloud secrets versions add "$secret_name" --data-file=- --project="$PROJECT_ID"
}

# Read the .env.production file and create secrets in parallel
while IFS='=' read -r key value; do
  # Ignore lines that are comments or empty
  if [[ "$key" =~ ^#.*$ ]] || [[ -z "$key" ]]; then
    continue
  fi

  # Remove quotes from the value if they exist
  value=$(echo "$value" | sed -e 's/^"//' -e 's/"$//')

  # Create a secret for each environment variable in parallel
  create_secret "$key" "$value" &

done < "$ENV_FILE"

# Wait for all background jobs to complete
wait

echo "All secrets from $ENV_FILE have been created or updated successfully."
