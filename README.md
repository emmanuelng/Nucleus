# Nucleus

[![Build Status](https://travis-ci.com/emmanuelng/Nucleus.svg?branch=master)](https://travis-ci.com/emmanuelng/Nucleus)
[![License](https://poser.pugx.org/emmanuelng/nucleus/license)](https://packagist.org/packages/emmanuelng/nucleus)

Nucleus is a PHP library that makes it easy to create REST APIs. The main features provided are routing and automatic data sanitization. In addition, the library is provided with the NEON grammar which allows to define APIs in a way that they are easily readable by front-end developers.

## Installation

Nucleus can be installed using [Composer](https://getcomposer.org/):
```
composer require emmanuelng/nucleus
```
This will install Nucleus and all required dependencies. Nucleus requires PHP 7.2 or newer.

## Getting started

### The NEON notation

The NEON notation (NuclEus Object Notation) is a notation that allows to define APIs in an elegant way. Its main goal is to be easily readable by front-end developers, and thus to serve as a bridge between the front-end and the back-end. For this reason, the NEON notation is only descriptive and does not contain any logic.
The NEON notation defines two types of object: routes and schemas.

#### Routes

A route is an action that can be triggered through your API. Each route is associated to a unique URL and request method (GET, POST, etc.).  Here is an example of a route defined using the NEON notation:
```
route UpdateProfile
{
  method 'PUT'
  url    '/users/update'
  
  parameters {
    int userId : "The user identifier. Mandatory."
  }
  
  request {
    string   email         = null : "The new user email address. Optional."
    bool     receiveNotifs = null : "Indicates whether the user must receive notifications. Optional."
    string[] tags          = []   : "List of user tags. Optional."
  }
  
  response {
    bool success : "True on success, false otherwise"
  }
  
  callback MyApp\User\User::update
}
```
The last property `callback` points to a valid callable (either a function or a static method) that takes a request (class `Nucleus\Router\Request`) and a response (class `Nucleus\Router\Response`) as parameters.
```
<?php

namespace MyApp\User;

use Nucleus\Router\Request;
use Nucleus\Router\Response;

class User
{
  public static function update(Request $req, Response $res): void
  {
  ...
  }
}
```
The `Request` and `Response` classes provide methods for obtaining the input data and outputting the result. The Nucleus framework guarantees that all input data corresponds to the definition of the NEON file.

### Schemas

Nucleus has four base types: `int`, `bool`, `float` and `string`. The NEON notation allows to define composite types called schemas.
```
schema UserProfile
{
  string   email         = null : "The new user email address. Optional."
  bool     receiveNotifs = null : "Indicates whether the user must receive notifications. Optional."
  string[] tags          = []   : "List of user tags. Optional."
}
```
The route defined in the previous section can then be defined as follows:
```
route UpdateProfile
{
  method 'PUT'
  url    '/users/update'
  
  parameters {
    int userId : "The user identifier. Mandatory."
  }
  
  request {
    UserProfile updatedProfile : "The new user email address. Mandatory."
  }
  
  response {
    bool success : "True on success, false otherwise"
  }
  
  callback MyApp\User\User::update
}
```

## Generating a router

Once all routes and schemas were defined, it is possible to generate the corresponding router. All NEON objects must be defined in their own `.neon` file. Here is an example of an `index.php` file:
```
<?php

require_once './vendor/autoload.php'

$routeDir  = __DIR__. '/routes';
$schemaDir = __DIR__. '/schemas';

// The path is a comma-separated list of the directories
// containing the .neon files defining the API
$path = "$routeDir,$schemaDir";

// Generate the router
$router = new Neon\NeonRouter($path);

// Start the router to handle requests.
$router->start();
```
