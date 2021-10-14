<?php

namespace essa\APIGenerator;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use essa\APIGenerator\Commands\GenerateComponent;

class APIGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('customPaginate', function () {

            if (request()->pagination === 'none') {
                return $this->get();
            }

            $page = Paginator::resolveCurrentPage();

            $perPage = request()->per_page ? request()->per_page : 20;

            $results = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($page, $perPage)->get(['*'])
                : $this->model->newCollection();

            return $this->paginator($results, $total, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateComponent::class,
            ]);
        }
    }
}
