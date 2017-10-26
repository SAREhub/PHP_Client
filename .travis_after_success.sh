#!/bin/bash

if [ "$TRAVIS_REPO_SLUG" = "SAREhub/PHP_Client" ] && [ "$TRAVIS_BRANCH" = 'master' ] && [ "$TRAVIS_PULL_REQUEST" = "false" ] && [ "$TRAVIS_PHP_VERSION" = "7.1" ]; then

    mkdir ../gh-pages
    vendor/bin/apigen generate src -vv --destination ../gh-pages
    cd ../gh-pages

    # Set identity
    echo "setting git identity"
    git config --global user.email "travis@travis-ci.org"
    git config --global user.name "Travis"

    # Add branch
    echo "initing git repo and creating branch"
    git init
    git remote add origin https://${GH_TOKEN}@github.com/${TRAVIS_REPO_SLUG}.git > /dev/null
    git checkout -B gh-pages

    # Push generated files
    echo "pushing generated docs"
    git add .
    git commit -m "APIGEN (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
    git push origin gh-pages -fq > /dev/null
fi