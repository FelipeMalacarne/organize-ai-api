update_secrets:
	@gcloud secrets versions add laravel_settings --data-file .env.production

update_deploy:
	@gcloud run services update laravel --image ${registry_name}/laravel --region ${region}

create_deploy:
	@gcloud run deploy laravel \
		--image ${REGISTRY_NAME}/laravel \
		--region ${REGION} \
		--set-cloudsql-instances ${PROJECT_ID}:${REGION}:${INSTANCE_NAME} \
		--set-secrets /config/.env=laravel_settings:latest \
		--allow-unauthenticated

submit:
	@gcloud builds submit --tag=${REGION}-docker.pkg.dev/${PROJECT_ID}/${REPO_NAME}/laravel .

migrate:
	@gcloud run jobs execute migrate --region ${REGION} --wait

update_migrate:
	@gcloud run jobs update migrate --image ${REGISTRY_NAME}/laravel --region ${REGION}

create_migrate:
	@gcloud run jobs create migrate \
		--image=${REGISTRY_NAME}/laravel \
		--region=${REGION} \
		--set-cloudsql-instances ${PROJECT_ID}:${REGION}:${INSTANCE_NAME} \
		--set-secrets /config/.env=laravel_settings:latest \
		--command "/bin/sh" \
		--args "-c, php artisan migrate"

sqlproxy:
	@gcloud cloud-sql-proxy ${PROJECT_ID}:${REGION}:${INSTANCE_NAME}

deploy: submit update_migrate update_deploy migrate
