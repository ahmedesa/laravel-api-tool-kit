# Laravel API tool kit
[![Latest Version on Packagist](https://img.shields.io/packagist/v/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)
![Test Status](https://img.shields.io/github/actions/workflow/status/ahmedesa/laravel-api-tool-kit/test.yml?label=tests&branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)

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
responseDeleted()  // returns an empty response with a 204 HTTP status code
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

### Creating a Filter

To create a filter, use the provided Artisan command:
Generate a filter class:
```
php artisan make:filter CarFilters
```
Using Filters in Models
```php
namespace App\Models;

use Essa\APIToolKit\Filters\Filterable;

class Car extends Model
{
    use Filterable;

    protected string $default_filters = CarFilters::class;

    // Other model code...
}

```
### Applying Filters
Filters can be applied to a query using the useFilters scope method:
```php
Car::useFilters()->get();
```
### Available Filter Options
In your filter class (CarFilters in this case), you have several options you can customize:

Allowed Filters
Define the attributes you want to allow filtering by using the $allowedFilters property:
```php
protected array $allowedFilters = ['color', 'model_id'];
```
Allowed Sorts
Specify the attributes that can be used for sorting using the $allowedSorts property:
```php
protected array $allowedSorts = ['created_at'];
```
To sort in descending or descending order . For example, to sort by created_at :
```
// descending : ?sorts=created_at
// ascending  : ?sorts=-created_at
```
Allowed Includes
For eager loading relationships, set the $allowedIncludes property:
```php
protected array $allowedIncludes = ['model'];
```
Column and Relation Search
Specify columns and relationships that can be searched using the $columnSearch and $relationSearch properties:
```php
protected array $columnSearch = ['name', 'description'];
protected array $relationSearch = [
    'user' => ['first_name', 'last_name']
];
```
### Creating Custom Filters
You can create custom filters by adding new methods to your filter class. For example, to filter cars by their manufacturing year:
```php
public function year($term)
{
    $this->builder->whereYear('manufacturing_date', $term);
}
```
### Using Filters in Requests
Filters can be applied by including query parameters in the API request. For example, to filter cars by color:
```
GET /cars?color=red
```
### Additional Tips
When searching for a value within a column or relationship, use the search query parameter.
### **DateFilter and TimeFilter**

The `DateFilter` and `TimeFilter` traits simplify querying records within a specific date or time ranges.

To use these traits, include them in your filter class:

```php
use Essa\APIToolKit\Traits\DateFilter;
use Essa\APIToolKit\Traits\TimeFilter;

class TestModelFilters extends QueryFilters
{
    use DateFilter;
    use TimeFilter;

    // ...
}
```
Example: Retrieve records created between '2023-08-01' and '2023-08-15' within a time range of '09:00:00' to '12:00:00':
```php 
/api/test-models?from_date=2023-08-01&to_date=2023-08-15&from_time=09:00:00&to_time=12:00:00
```
These traits enhance the flexibility of your API's querying capabilities and allow you to easily retrieve records based on date and time attributes.

[🔝 Back to contents](#contents)
## **API Generator**
The API Generator simplifies the process of creating API-related files for a model, making it quick and easy to set up the required components. It offers the ability to generate files such as controllers, requests, resources, migrations, factories, seeders, filters, tests, and even API routes.
#### Usage :
To generate API-related files for a specific model, use the following command:

```
php artisan api:generate ModelName
```
Upon running the command, you will be prompted with options to customize the generation process.

- If you choose to proceed with the default options, the generator will create files for all available options specified in the config/api-tool-kit configuration.
Alternatively, you can choose to customize the generation by selecting specific files to create.
- when you type the command it will ask you whether you want default options :
#### Default Options:
By default, the API Generator creates the following files for the specified ModelName:

- A model file:
   - Location: app/Models/ModelName.php 
- A controller file:
   - Location: app/Http/Controllers/API/ModelNameController.php
- A resource file:
   - Location: app/Http/Resources/ModelName/ModelNameResource.php
- A request file for creating the model:
   - Location: app/Http/Requests/ModelName/CreateModelNameRequest.php
- A request file for updating the model:
   - Location: app/Http/Requests/ModelName/UpdateModelNameRequest.php
- A filter file:
   - Location: app/Filters/ModelNameFilters.php
- A seeder file:
   - Location: database/seeders/ModelNameSeeder.php
- A factory file:
   - Location: database/factories/ModelNameFactory.php
- A test file:
    - Location: tests/Feature/ModelNameTest.php
- A migration file (created using make:migration):
    - Location: database/migrations/x_x_x_x_create_model_names_table.php

In addition, the generator automatically adds the necessary API routes to the routes/api.php file.

#### Customization
The API Generator allows you to customize the files you want to generate. You can opt to create specific components as per your project requirements. The available customization options are:

- --controller: Generate a controller file.
- --request: Generate request files for creating and updating the model.
- --resource: Generate a resource file for the model.
- --migration: Generate a migration file.
- --factory: Generate a factory file.
- --seeder: Generate a seeder file.
- --filter: Generate a filter file.
- --test: Generate a test file.
- --routes: Add the API routes to the routes/api.php file.
- --soft-delete: Enable soft delete for the model.


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
1-use Laravel application container
```php
app(CreateCar::class)->execute($data);
```
2-inject it in the class in the constructor

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
3-inject the class in Laravel controller function

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
UserTypes::isValid($value) //to check if this value exists in the enum
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
