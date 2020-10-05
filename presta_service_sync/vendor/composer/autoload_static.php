<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfff48dedb49df0a2653b2444194b3afb
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfff48dedb49df0a2653b2444194b3afb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfff48dedb49df0a2653b2444194b3afb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
