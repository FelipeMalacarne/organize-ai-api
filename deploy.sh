#!/bin/bash

REGION="southamerica-east1"
PROJECT_ID="organize-ai-app"
REPO_NAME="default"
REGISTRY_NAME="${REGION}-docker.pkg.dev/${PROJECT_ID}/${REPO_NAME}"
INSTANCE_NAME="pgsql"

gcloud builds submit --tag=${REGION}-docker.pkg.dev/${PROJECT_ID}/${REPO_NAME}/laravel .

echo "Builds has completed successfully."

gcloud run jobs update migrate --image ${REGISTRY_NAME}/laravel --region ${REGION} &
gcloud run services update laravel \
    --image ${REGISTRY_NAME}/laravel \
    --region ${REGION} &

wait

gcloud run jobs execute migrate --region ${REGION} --wait &

wait

echo "Cloud Run service has been replaced successfully."
