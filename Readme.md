# Laravel API tool kit

![](laravel-api-tool-kit.png)

The Laravel API Toolkit is a comprehensive suite of tools designed to help you create robust, high-performance APIs using Laravel's industry-leading best practices. With this toolkit, you can streamline your development process and build APIs that are both fast and organized. Whether you're a seasoned developer or just getting started, the Laravel API Toolkit has everything you need to build world-class APIs that meet your business needs.

## Contents

[Installation](#installation)

[Api response](#api-response)

[Dynamic Pagination](#dynamic-pagination)

[Filters](#filters)

[Api Generator](#api-generator)

[comment]: <> ([Out of the box permissions]&#40;#out-of-the-box-permissions&#41;)

[Actions](#actions)

[Media Helper](#media-helper)

[Enum](#enum)

[General tips](#general-tips)


## **Installation**
To get started, install the package using Composer:
```
composer require essa/api-tool-kit
```
To publish the configuration files, run:
```
php artisan vendor:publish --provider="Essa\APIToolKit\APIToolKitServiceProvider" --tag="config"
```
For standardizing error responses, extend your exception handler from the APIHandler class:
```php

namespace App\Exceptions;

use Essa\APIToolKit\Exceptions\Handler as APIHandler;

class Handler extends APIHandler
{
}

```

Utilize the API Response Trait in your controllers:

`App\Http\Controllers\Controller.php`:

```php
use Essa\APIToolKit\Api\ApiResponse;

class Controller extends BaseController
{
    use ApiResponse;
}
```
For more details, refer to [API response](#api-response)

[🔝 Back to contents](#contents)

## **API Response**

The API Response feature provides standardized response formatting and status codes.


#### **Success Response**
```json
{
  "message": "your message",
  "data": "your date"
}
```
#### **Error Response**
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

Usage: Include the ApiResponse trait in your class, then use the provided methods such as responseSuccess, responseCreated, responseDeleted, etc., to generate appropriate responses. Refer to the documentation for a full list of available methods.

```php
$this->responseSuccess('car created successfully' , $car);
```
Available Methods
```php

responseSuccess($message , $data)  // returns a 200 HTTP status code
responseCreated($message,$data)  // returns a 201 HTTP status code 
responseDeleted()  // returns empty response with a 204 HTTP status code
responseNotFound($errorDetails,$errorTitle)  // returns a 404 HTTP status code
responseBadRequest($errorDetails,$errorTitle)  // returns a 400 HTTP status code
responseUnAuthorized($errorDetails,$errorTitle)  // returns a 403 HTTP status code
responseConflictError($errorDetails,$errorTitle)  // returns a 409 HTTP status code
responseUnprocessable($errorDetails,$errorTitle)  // returns a 422 HTTP status code
responseUnAuthenticated ($errorDetails,$errorTitle) // returns a 401 HTTP status code
responseWithCustomError($errorTitle, $errorDetails, $statusCode) //send custom error 
```
[🔝 Back to contents](#contents)

## **Dynamic Pagination**
Use dynamic pagination to manage the number of results per page in API responses.

#### usage
To paginate results with the default of 20 items per page:
```php
$users = User::dynamicPaginate();
```
To retrieve all users without pagination:
```
\users?pagination='none'
```
To paginate with a custom number of items per page:
```
\users?per_page=10
```
by default pagination is 20 element per page you can change the default value from config/api-tool-kit
## **Filters**
Filters allow you to refine API query results based on various attributes.

usage:

Generate a filter class:
```
php artisan make:filter CarFilters
```
In the Car model, set default filters:
```php
protected $default_filters = CarFilters::class;
```
Use filters:
```php
Car::useFilters()->get();
```
Override default filters:

```php
Car::useFilters(SpecialCaseCarFilters::class)->get();
```
options in Filter class

```php
//to add the attributes to filter by =>> /cars?color=red&model_id=1
protected array $allowedFilters  = ['color' , 'model_id']; 
//to add the attributes to filter by :
// desc : ?sorts=created_at
// asc  : ?sorts=-created_at
protected array $allowedSorts= ['created_at'];
// allowed relationships to be loaded 
// ?includes=model
protected array $allowedIncludes = ['model'];
//column that will be included in search =>> ?search=tesla
protected array $columnSearch= ['name','descriptions']; 
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

//usage : /cars?year=2020
```
filter by relationship :
```php 
public function option($term)
{
    $this->builder->whereHas('options', fn($query) => $query->where('option_id', $term));
}
//usage : /cars?option=1
```


[🔝 Back to contents](#contents)

## **API Generator**
Generate API-related files quickly and easily with the following command:

#### Usage :

```
php artisan api:generate Car
```

when you type the command it will ask you whether you want default options :
- (N) it will ask you which files you want to generate .
- (Y) it will generate files for all options that exists in config/api-tool-kit
##### options :
```
 ** by default it will create a model :
-app/Models/Car.php
 ** controller :
-app/Http/Controllers/API/CarController.php
 ** resource :
-app/Http/Resources/CarResource.php
 ** request :
-app/Http/Requests/Car/CreateCarRequest.php
-app/Http/Requests/Car/UpdateCarRequest.php
 ** filter :
-app/Filters/CarFilters.php
 ** seeder :
-database/seeders/CarSeeder.php
 ** factory :
-database/factories/CarFactory.php
 ** test :
-Tests/Feature/CarTest.php
 ** migration :
-database/migrations/x_x_x_x_create_cars_table.php
```
in addition, the routes will be created and added in routes/api.php files


[🔝 Back to contents](#contents)
## **Actions**
Actions implement the command design pattern, providing a structured way to encapsulate business logic.
#### Usage :

Generate an action class:

```
php artisan make:action CreateCar
```

```php
<?php
namespace App\Actions;

class CreateCar
{
    public function execute($data)
    {
      //add business logic to create a car
    }
}
```
The best practice to use the action class is to use dependency injection

you have many options
1-use laravel application container
```php
app(CreateCar::class)->execute($data);
```
2-inject it in the class in constructor

```php
private $createCarAction ;

public function __construct(CreateCar $createCarAction)
{
    $this->createCarAction=$createCarAction;
}

public function doSomething()
{
    $this->createCarAction->execute($data);
}
```
3-inject the class in laravel controller function

```php
public function doSomething(CreateCar $createCarAction)
{
    $createCarAction->execute($data);
}
```
[🔝 Back to contents](#contents)


## **Media Helper**
The Media Helper simplifies file uploading and deletion processes:
#### Usage :

Upload a file:

```php
$filePath = MediaHelper::uploadFile($file ,$path); 
```
Delete a file:
```php
MediaHelper::deleteFile($path); 
```
Upload multiple files:
```php
$filesPaths = MediaHelper::uploadMultiple($files ,$path); 
```
Upload base64 image:
```php
$imagePath = MediaHelper::uploadBase64Image($encodedImage ,$path); 
```

[🔝 Back to contents](#contents)

## **Enum**
Utilize enums to avoid hardcoding values and create clear, maintainable code.

#### Usage :
Generate an enum class:
```
php artisan make:enum UserTypes
```
Define enum values:
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
UserTypes::getAll() //get all types 
UserTypes::isValid($value) //to check if this value exist in the enum
UserTypes::toArray() //to get all enums as key and value
```

[🔝 Back to contents](#contents)

## **General Tips**

Prefer throwing exceptions instead of directly returning JSON responses for better error handling.

Bad:

```php
public function index()
{
    if (auth()->user()->not_active ) {
        $this->responseUnAuthorized('you can not preform this action');
    } 
}
```
good

```php
public function index()
{
    if (auth()->user()->not_active ) {
        throw new AuthorizationException('you can not preform this action');
    } 
}
```

[🔝 Back to contents](#contents)
