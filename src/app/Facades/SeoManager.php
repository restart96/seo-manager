<?php
/**
 * @author SJ
 * @date   2019.12.18
 */

namespace Restart\SeoManager\App\Facades;

use Illuminate\Support\Facades\Facade;

class SeoManager extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'seomanager';
    }
}
