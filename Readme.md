## Installation
```
composer require essa/api_generator:dev-master
```



### Exceptions:

`App\Providers\Handler.php`:

```php



use Throwable;
use essa\APIGenerator\Http\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    
/**
* Render an exception into an HTTP response.
*
* @param \Illuminate\Http\Request $request
* @param \Throwable               $exception
* @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
* @throws \Throwable
*/
public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return  $this->ResponseValidationError($exception);
        }

        if ($exception instanceof QueryException || $exception instanceof ModelNotFoundException) {
            return $this->responseNotFound("Record not found!", $exception->getMessage());
        }

        if ($exception instanceof AuthorizationException) {
            return $this->responseUnauthorized();
        }

        return parent::render($request, $exception);
    }
}


```
 
### Controller:

`App\Http\Controllers\Controller.php`:

```php
use essa\APIGenerator\Http\ApiResponse;

class Controller extends BaseController
{
    use ApiResponse;
}
```



## Usage

Create component
```
php artisan make:module Admin
```

```
php artisan make:module Admin --with-image
```