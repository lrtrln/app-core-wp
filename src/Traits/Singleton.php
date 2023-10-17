<?php

namespace App\Traits;

trait Singleton
{
    protected static $instance;

    final public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    final private function __construct()
    {
        $this->init();
    }

    abstract protected function init();
}
