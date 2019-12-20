<?php
/**
 * @author SJ
 * @date   2019.12.19
 */

use Restart\SeoManager\App\Facades\SeoManager;

if (!function_exists('metaData')) {
    function metaData($property = null)
    {
        return str_replace('"', '\'', SeoManager::getData($property));
    }
}
