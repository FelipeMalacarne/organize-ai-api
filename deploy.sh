#!/bin/bash

REGION="southamerica-east1"
PROJECT_ID="organize-ai-app"
REPO_NAME="default"  # Replace with your repository name

gcloud builds submit --tag=${REGION}-docker.pkg.dev/${PROJECT_ID}/${REPO_NAME}/php-fpm . &
gcloud builds submit --tag=${REGION}-docker.pkg.dev/${PROJECT_ID}/${REPO_NAME}/nginx ./infra/nginx &

wait

echo "Both builds have completed successfully."

gcloud run jobs execute migrate --region ${REGION} --wait &

gcloud run services replace infra/service.yaml &

wait

echo "Cloud Run service has been replaced successfully."
