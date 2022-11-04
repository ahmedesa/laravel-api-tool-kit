<?php

namespace Essa\APIToolKit;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Foundation\Application;

class MacroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var Application $request */
        $app = $this->app;

        Builder::macro('dynamicPaginate', function () use ($app) {
            /** @var Request $request */
            $request = $app->get('request');

            if ($request->get('pagination') === 'none') {
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
