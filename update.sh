#!/usr/bin/env bash

git pull

echo Updating version file...

git log --oneline --format=%B -n 1 HEAD | head -n 1 > ./www/.version
git log --oneline --format="%at" -n 1 HEAD | xargs -I{} date -d @{} +%Y-%m-%d >> ./www/.version
git rev-parse --short HEAD >> ./www/.version

echo Updated.

chown www-data:www-data -R *
