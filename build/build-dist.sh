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
cp ./config.example.php ./dist/config.example.php
cp ./feeds.example.yaml ./dist/feeds.example.yaml
cp ./vigilant.php ./dist/vigilant.php
cp ./README.md ./dist/README.md
cp ./CHANGELOG.md ./dist/CHANGELOG.md
cp ./LICENSE ./dist/LICENSE.md
