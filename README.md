# MeCms/Photos plugin

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.com/mirko-pagliai/me-cms-photos.svg?branch=master)](https://travis-ci.com/mirko-pagliai/me-cms-photos)
[![Build status](https://ci.appveyor.com/api/projects/status/rje19tp04vf7ep9g?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/me-cms-photos)
[![codecov](https://codecov.io/gh/mirko-pagliai/me-cms-photos/branch/master/graph/badge.svg?token=PQXH0Y07E6)](https://codecov.io/gh/mirko-pagliai/me-cms-photos)
[![CodeFactor](https://www.codefactor.io/repository/github/mirko-pagliai/me-cms-photos/badge)](https://www.codefactor.io/repository/github/mirko-pagliai/me-cms-photos)

*MeCms/Photos* plugin allows you to handle photos with [MeCms platform](//github.com/mirko-pagliai/cakephp-for-mecms).

To install:
```bash
$ composer require --prefer-dist mirko-pagliai/me-cms-photos
```

Then load the plugin and run migrations to create the database tables:
```bash
$ bin/cake plugin load MeCms/Photos
$ bin/cake migrations migrate -p MeCms/Photos
```


Please, refer to the CookBook for [more information on loading plugins](https://book.cakephp.org/4/en/plugins.html#loading-a-plugin).

## Versioning
For transparency and insight into our release cycle and to maintain backward compatibility, *MeCms/Photos* will be maintained under the [Semantic Versioning guidelines](http://semver.org).
