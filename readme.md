[![Build Status](https://travis-ci.org/pomek/path2api.svg)](https://travis-ci.org/pomek/path2api)
[![Total Downloads](https://poser.pugx.org/pomek/path2api/downloads.svg)](https://packagist.org/packages/pomek/path2api)
[![License](https://poser.pugx.org/pomek/path2api/license.svg)](https://packagist.org/packages/pomek/path2api)

# Path2API

Path2API is a simple Laravel package which allows you generate API documentation based on phpDoc comments in your classes.

Package is compatible with Laravel 5.

## Installation

1. Add package to composer: `composer require "pomek/path2api:1.0.*"
2. Publish configuration: `php artisan vendor:publish`
3. Edit configuration file: `config/path2api.php`
4. Add Service Provider to `app.php`: `'Pomek\Path2API\Path2ApiServiceProvider'`
5. Artisan Command `path2api:generate` will be available now.

## Configuration file

* `prefix` - API URL prefix
* `file` - where will save generated documentation
* `before` - content will be added above the generated documentation
* `after` - content will be added below the generated documentation
* `template` - template for a single record

## Example

* Example Controller class:

```php

<?php namespace App\Http\Controllers;

use App\Http\Requests;

class TestController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param int $id
   * @return Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int $id
   * @return Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int $id
   * @return Response
   */
  public function update($id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return Response
   */
  public function destroy($id)
  {
    //
  }

}
```

* Add resource to your `routes.php`

```php
Route::group(['prefix' => 'api'], function () {
  Route::resource('test', 'TestController');
});
```

* Generate documentation by CLI command

```
$ php artisan path2api:generate
File api.md was generated.
```

* Your file `api.md` should be like:

```md
# API Documentation

Documentation generates by **Path2API** package.

---

### URL: api/test

Display a listing of the resource.


### URL: api/test/create

Show the form for creating a new resource.


### URL: api/test

Store a newly created resource in storage.


### URL: api/test/{test}

Display the specified resource.

**Params:**
 * `$id` `int`


### URL: api/test/{test}/edit

Show the form for editing the specified resource.

**Params:**
 * `$id` `int`


### URL: api/test/{test}

Update the specified resource in storage.

**Params:**
 * `$id` `int`


### URL: api/test/{test}

Update the specified resource in storage.

**Params:**
 * `$id` `int`


### URL: api/test/{test}

Remove the specified resource from storage.

**Params:**
 * `$id` `int`


---

Generates by [Path2API](//github.com/pomek/path2api)
```

