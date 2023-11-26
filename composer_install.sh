#!/usr/bin/env bash

set -o errexit

readonly PROJECT_DIR=$(dirname "$(readlink -f "$0")")
readonly USER_ID=$(id -u)
readonly MOUNTED_PROJECT_DIR="/app"

docker run --rm --interactive --tty \
  --volume $PROJECT_DIR:$MOUNTED_PROJECT_DIR \
  --workdir $MOUNTED_PROJECT_DIR \
  --user $USER_ID:$USER_ID \
  composer:2.6.5 \
  composer install
