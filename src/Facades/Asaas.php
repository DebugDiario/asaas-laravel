<?php

namespace Asaas\Facades;

use Illuminate\Support\Facades\Facade;

class Asaas extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'asaas';
    }
}
