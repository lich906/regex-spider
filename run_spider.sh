#!/usr/bin/env bash

set -o errexit

readonly PROJECT_DIR=$(dirname "$(readlink -f "$0")")
readonly MOUNTED_PROJECT_DIR="/app"
readonly USER_ID=$(id -u)

if [[ -z $1 || -z $2 ]]; then
  echo "Specify URL and regular expression to search."
  echo "Syntax: $0 url regex"
  exit 1
fi

URL=$1
REGEX=$2

docker run \
  --rm \
  --volume $PROJECT_DIR:$MOUNTED_PROJECT_DIR \
  --workdir $MOUNTED_PROJECT_DIR \
  --user $USER_ID:$USER_ID \
  php:8.1-cli \
  php -f getcsv.php $URL $REGEX
