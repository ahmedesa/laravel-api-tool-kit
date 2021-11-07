<?php

namespace essa\APIToolKit;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class MacroServiceProvider extends ServiceProvider
{
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
    }
}
