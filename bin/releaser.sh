#!/bin/sh
box build
mv pho.phar ~
git checkout gh-pages
mv ~/pho.phar ./
sha1sum pho.phar > pho.phar.version
git commit -am "version bump" && git push
git checkout master