#!/usr/bin/env bash

# Set variables.
SECRETS_FILE="secrets.travis.yml"

# Set missing variables.
php artisan cf:set-secrets --path=$SECRETS_FILE

# Login to Cloud Foundry.
cf login -a $CF_API -u $CF_USERNAME -p $CF_PASSWORD -o $CF_ORGANIZATION -s $CF_SPACE

# Deploy
# cf push --vars-file $SECRETS_FILE

# cf map-route chariot apps.internal -hostname chariot
# cf create-service cleardb spark chariot-db
# cf create-service rediscloud 30mb my-redis