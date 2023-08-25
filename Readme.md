<h1><center> Laravel API tool kit</center></h1>

<p align="center">
    <img src="laravel-api-tool-kit.png" style="width:70%;">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)
![Test Status](https://img.shields.io/github/actions/workflow/status/ahmedesa/laravel-api-tool-kit/test.yml?label=tests&branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)

## In Progress 

Adding Schema Support to API Generator

**How Schema Support Works (Upcoming Feature)**

We're working on enhancing the API Generator with schema support. This feature lets you define your database table structure directly from the command line, and it will generate factory model migrations, requests, and all specified data based on this schema:

```bash
php artisan api:generate ModelName --schema="column1:string,column2:integer,column3:datetime"
```
<p align="center">
    <img src="api-generator.png">
</p>

*Note: The schema feature is inspired by the [Laravel 5 Generators Extended](https://github.com/laracasts/Laravel-5-Generators-Extended/tree/master) package.*

## Introduction
Supercharge your API development with the API Toolkit, using standardized responses, dynamic pagination, advanced filtering, one-click full CRUD setup, logic clarity, media mastery, and crystal-clear enums. Let's elevate your development journey with high-performance APIs!

## Why Choose the Laravel API Toolkit?

### Consistent Responses, Less Hassle
Crafting responses that clients can easily understand becomes a breeze. With the toolkit's standardized response formats, your communication is seamless, saving you time and effort.

```php
$this->responseSuccess('Car created successfully', $car);
```
### Pagination Done Right
Don't fuss over managing the number of results per page. The dynamic pagination feature adapts effortlessly to your needs, giving you control without complications.

```php
$users = User::dynamicPaginate();
```
### Simplified Filtering
Refine query results with simplicity. The powerful filtering system lets you filter, sort, search, and even include relationships with ease.

```php
Car::useFilters()->get();
```
### Simplify API Setup with the API Generator

Say goodbye to manual file setup! The API Generator does the hard work for you by creating key files from migrations to controllers automatically. Just use one simple command to kickstart your API development.

```
php artisan api:generate ModelName --all
```
### Logic Made Clear
Tackle complex business logic with Actions. These gems follow the command pattern, boosting readability and maintenance for your code.

```php
app(CreateCar::class)->execute($data);
```

### Media? Handled.
Handle file uploads and deletions like a pro. The Media Helper streamlines media management, leaving you with clean and organized file handling.
```php
$filePath = MediaHelper::uploadFile($file, $path);
```
### Enums for Clarity
Say goodbye to hardcoded values. Enums replace them with meaningful constants, resulting in cleaner, more understandable code.

```php
namespace App\Enums;

class UserTypes extends Enum
{
    public const ADMIN = 'admin';
    public const STUDENT = 'student';
}
```

## Official Documentation
Access our documentation to unlock the full potential of the Laravel API Toolkit:

[Explore the Documentation](https://ahmedesa.github.io/laravel-api-tool-kit-docs/)

