<?php

namespace Essa\APIToolKit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $app = $this->app;

        Builder::macro('dynamicPaginate', function () use ($app) {
            /** @var Request $request */
            $request = $app->get('request');

            if ('none' === $request->get('pagination')) {
                return $this->get();
            }

            $page = Paginator::resolveCurrentPage();

            $perPage = $request->get('per_page', config('api-tool-kit.default_pagination_number'));

            $results = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($page, $perPage)->get(['*'])
                : $this->model->newCollection();

            return $this->paginator($results, $total, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
        });
    }
}
