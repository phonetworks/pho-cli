[![Build Status](https://travis-ci.org/phonetworks/pho-cli.svg?branch=master)](https://travis-ci.org/phonetworks/pho-cli)

## Requirements

* PHP 5.5+
* [Box](https://github.com/box-project/box2)

## How to release

```
box build
mv pho.phar ~
git checkout gh-pages
mv ~/pho.phar ./
sha1sum pho.phar > pho.phar.version
git commit -am "version bump" && git push
git checkout master
```
