<?php

namespace essa\APIGenerator\Generator;

use Illuminate\Support\Str;

class RouteGenerator
{
    public static function routeDefinition($model)
    {
        return "
/*===========================
=           " . Str::plural(Str::snake($model)) . "       =
=============================*/

Route::apiResource('/" . Str::plural(Str::snake($model)) . "', \App\Http\Controllers\API\\" . $model . "Controller::class);

Route::group([
   'prefix' => '" . Str::plural(Str::snake($model)) . "',
], function() {
    Route::get('{id}/restore', [\App\Http\Controllers\API\\" . $model . "Controller::class, 'restore']);
    Route::delete('{id}/permanent-delete', [\App\Http\Controllers\API\\" . $model . "Controller::class, 'permanentDelete']);
});
/*=====  End of " . Str::plural(Str::snake($model)) . "   ======*/
   ";
    }
}