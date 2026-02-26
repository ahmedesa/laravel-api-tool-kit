<h1><center> Laravel API tool kit</center></h1>

<p align="center">
    <img src="laravel-api-tool-kit.png" style="width:70%;">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)
![Test Status](https://img.shields.io/github/actions/workflow/status/ahmedesa/laravel-api-tool-kit/test.yml?label=tests&branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/essa/api-tool-kit.svg?style=flat-square)](https://packagist.org/packages/essa/api-tool-kit)

## Introduction

Build production-grade Laravel APIs with standardized responses, dynamic pagination, advanced filtering, and **built-in AI architectural rules** that teach your coding assistant to follow your standards from the first draft.

## Installation
```
composer require essa/api-tool-kit
```

## 🤖 AI Skill — Teach Your AI Assistant Your Architecture

Ship production-grade APIs faster by giving your AI coding assistant a shared understanding of your architecture. One command installs **20 rule files** and **5 guided workflows** that ensure every AI-generated file fits your codebase from the first draft — no manual corrections needed.

Supports **Claude Code**, **Cursor**, **GitHub Copilot**, and **Antigravity**.

```bash
php artisan api-skill:install
```

### What's included

| Category | Contents |
|----------|----------|
| **Rules** | Controllers, Models, Actions, DTOs, Services, Repositories, Filters, Enums, Events, Requests, Resources, Responses, Exceptions, Authorization, Testing, Database, Pagination, Anti-patterns, Code Quality, Media |
| **Workflows** | Scan project conventions, new endpoint step-by-step, add filtering, code review checklist, update knowledge base |
| **DDD Support** | Works with both standard Laravel and Domain-Driven Design layouts |
| **Project Defaults** | Configure primary key type, auth guard, and test base class once — the AI applies them everywhere |

> The AI Skill is the recommended way to generate code for projects using this package. It replaces the need for scaffolding generators by teaching your AI assistant the full architecture.

[Read the full AI Skill documentation →](https://laravelapitoolkit.com/#/v3/ai-skill)

---

## Why Choose the Laravel API Toolkit?

### 🤖 Built for AI-Assisted Development
The built-in AI Skill teaches your coding assistant to generate code that follows your architecture out of the box — no more fixing AI output to match your standards.

### Consistent Responses, Less Hassle
The API Response feature simplifies generating consistent JSON responses. It provides a standardized format for your API responses:
```json
{
  "message": "your resource successfully",
  "data": [
    ...
  ]
}
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

### Logic Made Clear
Tackle complex business logic with Actions. These gems follow the command pattern, boosting readability and maintenance for your code.

```php
class CarController extends Controller
{
    public function __construct(
        private readonly CreateCarAction $createCar,
    ) {}

    public function store(CreateCarRequest $request): JsonResponse
    {
        $car = $this->createCar->execute($request->validated());
        return $this->responseCreated(trans('car.created'), new CarResource($car));
    }
}
```

### Media? Handled.
Handle file uploads and deletions like a pro. The Media Helper streamlines media management, leaving you with clean and organized file handling.
```php
$filePath = MediaHelper::uploadFile($file, $path);
```

### Enums for Clarity
The Enum class provides a way to work with enumerations, eliminating hardcoded values in your code:
```php
namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
}
```

---

### API Generator

The API Generator automates file setup, creating key files from migrations to controllers. Use one command to kickstart your API development.

> **Note:** For AI-assisted development, the [AI Skill](#-ai-skill--teach-your-ai-assistant-your-architecture) is the recommended approach. It teaches your AI assistant the full architecture so generated code fits your project from the start. The API Generator remains available for developers who prefer traditional scaffolding.

```
php artisan api:generate ModelName --all
```

<p align="center">
    <img src="api-generator.png">
</p>

## :film_strip: Video Tour
If you'd prefer a more visual review of this package, please watch this video on Laravel Package Tutorial.

[<img src="https://img.youtube.com/vi/lZ9PPW-5utw/0.jpg" width="450">](https://youtu.be/lZ9PPW-5utw)

## Official Documentation
Access our documentation to unlock the full potential of the Laravel API Toolkit:

[Explore the Documentation](https://laravelapitoolkit.com/)

## Contributing
We welcome your contributions to help make this package even better. Please refer to our [CONTRIBUTING.md](CONTRIBUTING.md) file for contribution guidelines.

## License

By contributing to the Laravel API Toolkit, you agree that your contributions will be licensed under the project's [MIT License](LICENSE.md).
