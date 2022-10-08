# MeCms/Photos plugin

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.com/mirko-pagliai/me-cms-photos.svg?branch=master)](https://travis-ci.com/mirko-pagliai/me-cms-photos)
[![codecov](https://codecov.io/gh/mirko-pagliai/me-cms-photos/branch/master/graph/badge.svg?token=PQXH0Y07E6)](https://codecov.io/gh/mirko-pagliai/me-cms-photos)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/f26430c611aa43c8a08278040b5d86cc)](https://www.codacy.com/gh/mirko-pagliai/me-cms-photos/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=mirko-pagliai/me-cms-photos&amp;utm_campaign=Badge_Grade)
[![CodeFactor](https://www.codefactor.io/repository/github/mirko-pagliai/me-cms-photos/badge)](https://www.codefactor.io/repository/github/mirko-pagliai/me-cms-photos)

*MeCms/Photos* plugin allows you to handle photos with [MeCms platform](https://github.com/mirko-pagliai/cakephp-for-mecms).

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

## Testing
Tests are run for only one driver at a time, by default `mysql`.
To choose another driver to use, you can set the `driver_test` environment variable before running `phpunit`.

For example:
```
driver_test=postgres vendor/bin/phpunit
```

Alternatively, you can set the `db_dsn` environment variable, indicating the connection parameters. In this case, the driver type will still be detected automatically.

For example:
```bash
db_dsn=sqlite:///' . TMP . 'example.sq3 vendor/bin/phpunit
```

## Versioning
For transparency and insight into our release cycle and to maintain backward compatibility, *MeCms/Photos* will be maintained under the [Semantic Versioning guidelines](http://semver.org).
