<?php
/**
 * @author SJ
 * @date   2019.09.20
 */

return [
    /**
     * Database table name where your manager data will be stored
     */
    'database' => [
        'routes_table' => 'seo_manage_routes',
        'locales_table' => 'seo_manage_locales',
        'translates_table' => 'seo_manage_translates',
    ],

    /**
     * Set default locale,
     * It will be added as default locale
     * when locales table migrated
     */
    'locale' => 'ko',

    /**
     * Path where your eloquent models are
     * Leave this config empty if you want to look for models in whole project
     */
    'models_path' => '',

    /**
     * Route from which your Dashboard will be available
     */
    'route' => 'seo-manager',

    /**
     * Middleware array for dashboard
     * to prevent unauthorized users visit the manager
     */
    'middleware' => [
        //
    ],

    /**
     * Routes which shouldn't be imported to seo manager
     */
    'except_routes' => [
        //
    ],

    /**
     * Columns which shouldn't be used ( in mapping )
     */
    'except_columns' => [
        //
    ],

    /**
     * FileUpload disk information
     */
    'filesystems' => [
        'disk' => '',
        'path' => '',
    ],
];
