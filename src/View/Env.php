<?php

namespace Pkit\View;

abstract class Env
{


    public static function config(string $path)
    {
        putenv("PHANTOM_PATH=$path");
    }

    public static function getPath()
    {
        if (getenv("PHANTOM_PATH") == false) {
            putenv("PHANTOM_PATH=" . getcwd() . "/view");
        }
        return getenv("PHANTOM_PATH");
    }

}