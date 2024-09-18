<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if (!function_exists('dump')) {
    /**
     * @param mixed $data
     * @param bool $exit
     * @return void
     */
    function dump(mixed $data, bool $exit = false): void
    {
        echo '<pre>' . PHP_EOL;

        if ($exit === true) {
            var_dump($data);
            exit('</pre>' . PHP_EOL);
        }

        var_dump($data);
        echo '</pre>' . PHP_EOL;
    }
}

if (!function_exists('get_short_class_name')) {
    /**
     * @param $class
     * @param bool $lower
     * @return bool|string
     */
    function get_short_class_name($class, bool $lower = true): bool|string
    {
        if (empty($class)) {
            return '';
        }

        $explodeClass = explode('\\', $class);
        return $lower === true ? strtolower(end($explodeClass)) : end($explodeClass);
    }
}
