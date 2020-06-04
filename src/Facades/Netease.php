<?php

namespace Xinliang\Netease\Facades;

use Illuminate\Support\Facades\Facade;
use Xinliang\Netease\Netease as Accessor;

class Netease extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Accessor::class;
    }
}