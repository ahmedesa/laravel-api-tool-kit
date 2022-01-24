<?php
if (!function_exists('createOrRandomFactory')) {
    /**
     * Get a random item from the given model $class_name or create a new one by factory
     *
     * @param string $class_name must be type model
     * @return object instance of a $class_name or stdClass
     * @throws Exception
     */
    function createOrRandomFactory(string $class_name): object
    {
        if (!is_subclass_of($class_name, 'Illuminate\Database\Eloquent\Model')) {
            throw new \Exception('parameter class_name is not a model type');
        }

        $class = new $class_name();

        if ($class::count()) {
            return $class::inRandomOrder()->first();
        }

        return $class_name::factory()->create();
    }
}
