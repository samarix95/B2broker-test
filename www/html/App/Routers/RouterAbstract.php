<?php

namespace App\Routers;

abstract class RouterAbstract
{
    public function __construct()
    {
    }

    abstract public function route($method, $formData, $urlData): void;
}
