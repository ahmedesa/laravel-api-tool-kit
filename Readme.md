# Laravel API tool kit and best API practices

![](laravel-api-tool-kit.png)

Laravel api tool kit is a set of tools that will help you to build a fast and well-organized API using laravel best practices.

## Contents

[Installation](#installation)

[Api Generator module](#api-generator-module)

[Api response](#api-response)

[Filters](#filters)

[comment]: <> ([Out of the box permissions]&#40;#out-of-the-box-permissions&#41;)

[Actions](#actions)

[Media Helper](#media-helper)

[Enum](#enum)

[General tips](#general-tips)


### **Installation**

```
composer require essa/api-tool-kit
```
to publish config
```
php artisan vendor:publish --provider="essa\APIToolKit\APIToolKitServiceProvider" --tag="config"
```

use exception handler to standardize the error response

```php

namespace App\Exceptions;

use essa\APIToolKit\Exceptions\Handler;
use essa\APIToolKit\Exceptions\Handler as APIHandler;

class Handler extends ExceptionHandler
{
}

```

use API Response Trait in Controller

`App\Http\Controllers\Controller.php`:

```php
use essa\APIToolKit\Http\ApiResponse;

class Controller extends BaseController
{
    use ApiResponse;
}
```
check : [API response](#api-response)

[🔝 Back to contents](#contents)

### **API Generator module**

#### Usage :

```
php artisan api:generate Car
```

if you write the command it will ask you if you want to generate all default options which you can select in config/api-tool-kit file if you decide not to change the default option and decide to select all option it will generate the following files :
```
-app/Models/Car.php
-app/Http/Controllers/API/CarController.php
-app/Http/Resources/CarResource.php
-app/Http/Requests/Car/CreateCarRequest.php
-app/Http/Requests/Car/UpdateCarRequest.php
-app/Filters/CarFilters.php
-database/seeders/CarSeeder.php
-database/factories/CarFactory.php
-Tests/Feature/CarTest.php
-database/migrations/x_x_x_x_create_cars_table.php
```
and will create api.php in routes file and will add the routes to it

if you choose not to select all options it will ask you which files you want to generate

[🔝 Back to contents](#contents)

### **API Response**

it is used to format your response to standard format and status codes
for success responses, it will be

```json
{
  "message": "your message",
  "data": "your date"
}
```
for errors responses

```json
{
  "errors": [
    {
      "status": 403,
      "title": "unauthenticated!",
      "detail": "Unauthenticated."
    }
  ]
}
```

usage:
you can use the trait inside the class you want to return the response form

and use it like this

```php
$this->responseSuccess('car created successfully' , $car);
```
Available Methods
```php

responseSuccess($message , $data)  // returns a 200 HTTP status code
responseCreated($message,$data)  // returns a 201 HTTP status code 
responseDeleted()  // returns empty response with a 204 HTTP status code
responseNotFound($details,$message)  // returns a 404 HTTP status code
responseBadRequest($details,$message)  // returns a 400 HTTP status code
responseUnAuthorized($details,$message)  // returns a 403 HTTP status code
responseConflictError($details,$message)  // returns a 409 HTTP status code
responseUnprocessable($details,$message)  // returns a 422 HTTP status code
responseUnAuthenticated ($details,$message) // returns a 401 HTTP status code
responseWithCustomError($title, $details, $status_code) //send custom error 
```
[🔝 Back to contents](#contents)

### **Filters**

usage:

to create a filter class:
```
php artisan make:action CarFilters
```
to set default filters to the Car model , in Car model you will add
```php
protected $default_filters = CarFilters::class;
```
to use it

```php
Car::useFilters()->get();
```
if you want to override the default filters

```php
Car::useFilters(SpecialCaseCarFilters::class)->get();
```
options in Filter class

```php
    //to add the attributes to filter by =>> /cars?color=red&model_id=1
    protected array $allowedFilters  = ['color' , 'model_id']; 
    //to add the attributes to filter by =>> desc : ?sorts=created_at asc : ?sorts=-created_at
    protected array $allowedSorts    = ['created_at'];
    //allowed relationships to be loaded 
    protected array $allowedIncludes = ['model'];
    //column that will be included in search =>> ?search=tesla
    protected array $columnSearch    = ['name','descriptions']; 
    //relation that will be included in search =>> ?search=ahmed
    protected array $relationSearch = [
        'user' => ['first_name', 'last_name']
    ]; 
```

to create a custom query you will just create a new function in the class and add your query
example filter by year:
```php
    public function year($term)
    {
        $this->builder->whereYear('created_At', $term);
    }
```
filter by relationship :
```php 
    public function option($term)
    {
        $this->builder->whereHas('options', fn($query) => $query->where('option_id', $term));
    }
```


[🔝 Back to contents](#contents)

### **Actions**
action is a laravel implementation of command design pattern where can add the business logic in https://en.wikipedia.org/wiki/Command_pattern

usage:

```
php artisan make:action CreateCar
```

```php
<?php
namespace App\Actions;

class CreateCar
{
    public function execute(array $data)
    {
      //add business logic to create a car
    }
}
```

The best practice to use the action class is to use dependency injection
you have many options
1-use laravel app class
```php
app(CreateCar::class)->execute($data);
```
2-inject it in class construct

```php
    private $create_car_action ;
    public function __construct(CreateCar $create_car_action)
    {
        $this->create_car_action=$create_car_action;
    }
    
    public function doSomething()
    {
        $this->create_car_action->execute($data);
    }
```
3-use it in laravel controller function

```php
    public function doSomething(CreateCar $create_car_action)
    {
        $create_car_action->execute($data);
    }
```
[🔝 Back to contents](#contents)


### **Media Helper**

it is used to upload and delete an image

```php
// to upload image
$image_path = MediaHelper::uploadImage($file ,$path); 
//to delete an image
MediaHelper::deleteImage($path); //to delete image
```
Available Methods

[🔝 Back to contents](#contents)

### **Enum
bad practice :
if I have two types of users admin and student instead of hard coding the name of user type every time using it you can simply use the enum class

usage :
```
php artisan make:enum UserTypes
```
it will generate classes like this
```php
namespace App\Enums;

class UserTypes extends Enum
{
    public const ADMIN = 'admin';
    public const STUDENT = 'student';
}
```
methods:
```php
getAll() //get all types 
isValid($value) //to check if this value exist in the enum
toArray() //to get all enums as key and value
```

[🔝 Back to contents](#contents)

### **General Tips**

### **throw error instead of return json response**

A class and a method should have only one responsibility.

Bad:

```php
public function index()
{
    if (auth()->user()->not_active ) {
        $this->responseUnAuthorized('you cant preform this action'');
    } 
}
```
good

```php
public function index()
{
    if (auth()->user()->not_active ) {
        throw new AuthorizationException('you cant preform this action'');
    } 
}
```

[🔝 Back to contents](#contents)
