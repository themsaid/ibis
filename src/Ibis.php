<?php

namespace Ibis;

use Illuminate\Support\Str;

class Ibis
{
    /**
     * @var array
     */
    public static $config;

    /**
     *
     */
    public static function title()
    {
        return self::config()['title'];
    }

    /**
     *
     */
    public static function outputFileName()
    {
        return Str::slug(self::config()['title']);
    }

    /**
     *
     */
    public static function author()
    {
        return self::config()['author'];
    }

    /**
     * @return array
     */
    private static function config()
    {
        if (static::$config) {
            return static::$config;
        }

        static::$config = require getcwd() . '/ibis.php';

        return static::$config;
    }
}
