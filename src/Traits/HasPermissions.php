<?php

namespace Essa\APIToolKit\Traits;

trait HasPermissions
{
    protected function authorizeFor(string $type): void
    {
        $middleware = [];

        foreach (config("permissions-map.{$type}") as $method => $ability) {
            $middleware["permission:{$type}.{$ability}"][] = $method;
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName)->only($methods);
        }
    }
}
