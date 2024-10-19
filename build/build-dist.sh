#!/bin/bash

# Remove old folder if exist
if [ -d "./dist" ]; then
	rm -r ./dist
fi

# Create dist folder
mkdir ./dist

# Copy folders
cp -r ./docs ./dist/docs
cp -r ./src ./dist/src
cp -r ./vendor ./dist/vendor

# Remove files used only for documentation site
rm ./dist/docs/licenses.md
rm ./dist/docs/changelog.md
rm -r ./dist/docs/img/logo

# Copy files
cp ./composer.json ./dist/composer.json
cp ./composer.lock ./dist/composer.lock
cp ./config.example.php ./dist/config.example.php
cp ./feeds.example.yaml ./dist/feeds.example.yaml
cp ./vigilant.php ./dist/vigilant.php
cp ./README.md ./dist/README.md
cp ./CHANGELOG.md ./dist/CHANGELOG.md
cp ./LICENSE ./dist/LICENSE.md
cp ./LOGO_LICENSE.txt ./dist/LOGO_LICENSE.md
