<?php

namespace Pkit\Phantom\View;

abstract class Env
{


    public static function config(string $path)
    {
        putenv("VIEW_PATH=$path");
    }

    public static function getPath()
    {
        if (getenv("VIEW_PATH") == false) {
            putenv("VIEW_PATH=" . $_SERVER["PWD"] . "/view");
        }
        return getenv("VIEW_PATH");
    }

}