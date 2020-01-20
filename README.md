# laravel-restful-api

[![Latest Version](https://img.shields.io/github/release/elb98rm/laravel-restful-api.svg?style=plastic)](https://github.com/elb98rm/laravel-restful-api/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=plastic)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-restful-api/master.svg?style=plastic)](https://travis-ci.org/elb98rm/laravel-restful-api)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/laravel-restful-api/laravel-restful-api.svg?style=plastic)](https://scrutinizer-ci.com/g/floor9design/laravel-restful-api/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-restful-api/laravel-restful-api.svg?style=plastic)](https://scrutinizer-ci.com/g/floor9design/laravel-restful-api)
[![Total Downloads](https://img.shields.io/packagist/dt/league/laravel-restful-api.svg?style=plastic)](https://packagist.org/packages/floor9design/laravel-restful-api)

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

## Install

Via Composer

``` bash
composer require floor9design/laravel-restful-api
```

## Usage

### How the classes work

* [Wikipedia definition](https://en.wikipedia.org/wiki/Representational_state_transfer#Relationship_between_URI_and_HTTP_methods) 

As defined, a RESTful API offers public urls of a specific format and receives tightly defined requests. Responses to 
these requests are also specifically defined. 

In Laravel, routes and logic are dealt with via a routing file and a relevant controller. In the example case of a 
wanting to expose a User object, `/routes/api.php` will create routes pointing to the 
`/App/Http/Controllers/UsersController`.

The software offers the following classes to provide methods for this class:

* `ApiJsonTrait` and `ApiFilterTrait` offer supporting properties and methods
* `ApiJsonInterface` contracts the controller class into providing the required responses 
* `ApiJson501Trait` offers methods giving a valid `501: not implemented` response
* `ApiJsonDefaultTrait` offers default methods to implement complete responses 

By using a combination of these classes it is possible to implement a full API.

## Setup

How you define routes is your choice, however, the following is an example of a route definition in `routes/api.php`.

... TO BE COMPLETED.

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
