<?php

declare(strict_types=1);

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

            $maxPerPage = config('api-tool-kit.max_pagination_limit');

            if ($maxPerPage && $perPage > $maxPerPage) {
                $perPage = $maxPerPage;
            }

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
