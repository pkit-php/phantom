<?php

namespace Pkit;

use Pkit\View\Env as PhantomEnv;

class Phantom extends PhantomEnv
{
    private static ?View $instance = null;

    public static function extend(string $view, array $vars = [])
    {
        self::$instance->extend($view, $vars);
    }

    public static function include(string $view, array $vars = [])
    {
        return self::$instance->render($view, $vars);
    }

    public static function section(string $id)
    {
        self::stop();
        self::$instance->section($id);
    }

    public static function stop()
    {
        self::$instance->stop();
    }
    
    public static function slot($id)
    {
        return self::$instance->getSlot($id);
    }


    public static function render(string $view, array $args = [])
    {
        return self::renderView(new View(self::getPath()), $view, $args);
    }

    public static function renderView(View $instance, string $view, array $args = []) {
        $backupInstance = $instance;
        self::$instance = $backupInstance;
        $content = self::$instance->render($view, $args ?? []);
        self::$instance = $backupInstance;
        if (self::$instance->hasExtends()) {
            $content = self::$instance->renderExtend();
        } 
        return $content;
    }

}