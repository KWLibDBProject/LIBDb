#!/usr/bin/env bash

grunt --gruntfile gruntfile-aait.js
grunt --gruntfile gruntfile-etks.js
grunt --gruntfile gruntfile-hait.js

echo Updated.

chown www-data:www-data -R *
