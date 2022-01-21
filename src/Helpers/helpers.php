<?php
if (! function_exists('createOrRandomFactory')) {
    /**
     * Get a random item from the given model $class_name or create a new one by factory
     *
     * @param string $class_name
     * @return object|stdClass instance of a $class_name or stdClass
     */
    function createOrRandomFactory(string $class_name): object
    {
        if (is_subclass_of($class_name, 'Illuminate\Database\Eloquent\Model')) {
            $class = new $class_name();
            if ($class::count()) {
                return $class::inRandomOrder()->first();
            }
            if (class_exists($class_name . 'Factory')) {
                return $class_name::factory()->create();
            }
        }
        return new stdClass();
    }
}
