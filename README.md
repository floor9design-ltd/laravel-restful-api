# laravel-restful-api

[![Latest Version](https://img.shields.io/github/v/release/floor9design-ltd/laravel-restful-api?include_prereleases&style=plastic)](https://github.com/floor9design-ltd/laravel-restful-api/releases)
[![Packagist](https://img.shields.io/packagist/v/floor9design/laravel-restful-api?style=plastic)](https://packagist.org/packages/floor9design/laravel-restful-api)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=plastic)](LICENCE.md)

[![Build Status](https://img.shields.io/travis/floor9design-ltd/laravel-restful-api?style=plastic)](https://travis-ci.org/github/floor9design-ltd/laravel-restful-api)
[![Build Status](https://img.shields.io/codecov/c/github/floor9design-ltd/laravel-restful-api?style=plastic)](https://codecov.io/gh/floor9design-ltd/laravel-restful-api)

[![Github Downloads](https://img.shields.io/github/downloads/floor9design-ltd/laravel-restful-api/total?style=plastic)](https://github.com/floor9design-ltd/laravel-restful-api)
[![Packagist Downloads](https://img.shields.io/packagist/dt/floor9design/laravel-restful-api?style=plastic)](https://packagist.org/packages/floor9design/laravel-restful-api)

A set of restful API classes that offer strict RESTful methods for laravel. Currently in development.

## Introduction

Laravel's out the box RESTful implementation is fairly easy to use, but it is not a strict REST api. This repository
offers classes to deliver a true REST implementation that matches the wikipedia definition.

* [Wikipedia definition](https://en.wikipedia.org/wiki/Representational_state_transfer#Relationship_between_URI_and_HTTP_methods) 

## Features

* Simple setup/configuration
* Simple "plug-in" traits and interfaces that quickly add a full set of RESTful responses to a controller
* JSON API compliant responses
* Not implemented/not allowed/data responses available on a per method/route basis
* Easily overrideable by your own code on a per method/route basis
* Suports laravel validation

## Install

Via Composer

``` bash
composer require floor9design/laravel-restful-api
```

## Usage

It is recommended you read the background information section:

* [background](docs/project/background.md)

This is defined in detail in the usage section:

* [usage](docs/project/usage.md)



## Setup

This is defined in detail in the setup section:

* [setup](docs/project/setup.md)

## Testing

Tests under development and are underway.

To run the existing tests: 

* `./vendor/phpunit/phpunit/phpunit`

Documentation and coverage can be generated as follows:

* `./vendor/phpunit/phpunit/phpunit --coverage-html docs/tests/`

## Credits

- [Rick](https://github.com/elb98rm)

## Changelog

A changelog is generated here:

* [Change log](CHANGELOG.md)

## License

This software is available under the MIT licence. 

* [License File](LICENSE.md)
