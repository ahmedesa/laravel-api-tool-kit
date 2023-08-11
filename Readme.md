<h1><center> Laravel API tool kit</center></h1>

<p align="center">
    <img src="laravel-api-tool-kit.png" style="width:70%;">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)
![Test Status](https://img.shields.io/github/actions/workflow/status/ahmedesa/laravel-api-tool-kit/test.yml?label=tests&branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)

## Introduction
The Laravel API Toolkit is a comprehensive suite of tools designed to help you create robust, high-performance APIs using Laravel's industry-leading best practices. With this toolkit, you can streamline your development process and build APIs that are both fast and organized. Whether you're a seasoned developer or just getting started, the Laravel API Toolkit has everything you need to build world-class APIs that meet your business needs.

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
### Filtering Magic
Refine query results with simplicity. The powerful filtering system lets you sort, search, and even include relationships, making data retrieval a cinch.

```php
Car::useFilters()->get();
```
### Rapid API Setup
Accelerate your workflow with the API Generator. This nifty tool automates essential file creation, from controllers to requests, helping you start strong.

```
php artisan api:generate ModelName
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

