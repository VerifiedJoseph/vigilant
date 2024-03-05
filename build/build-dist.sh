#!/bin/bash
# Create dist folder
mkdir ./dist

# Copy folders
cp -r ./docs ./dist/docs
cp -r ./src ./dist/src
cp -r ./vendor ./dist/vendor

# Copy files
cp ./composer.json ./dist/composer.json
cp ./composer.lock ./dist/composer.lock
cp ./config.example.php ./dist/pconfig.example.php
cp ./feeds.example.yaml ./dist/feeds.example.yaml
cp ./vigilant.php ./dist/vigilant.php
cp ./daemon.php ./dist/daemon.php
cp ./README.md ./dist/README.md
cp ./LICENSE ./dist/LICENSE
