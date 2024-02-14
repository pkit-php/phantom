<?php

namespace Pkit;

use Pkit\View\Env as PhantomEnv;

class Phantom extends PhantomEnv
{
    private readonly string $extends;
    private readonly array $extendsVars;

    private static ?self $instance = null;
    private static array $instances = [];

    private static ?string $atualSlot = null;
    private static array $slots = [];

    function __construct(private string $file, private array $vars)
    {
        if (!file_exists($this->file)) {
            throw new \Exception("file '$file' not exists", 500);
        }
    }

    private function renderFile()
    {
        ob_start();

        extract($this->vars);
        include $this->file;

        return ob_get_clean();
    }

    private function extendView(string $view, array $vars = [])
    {
        $this->extends = $view;
        $this->extendsVars = $vars;
    }

    private function renderExtend(string $path)
    {
        return self::renderByPath($path,$this->extends, $this->extendsVars);
    }

    private function hasExtends()
    {
        try {
            return !!$this->extends;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function extend(string $view, array $vars = [])
    {
        self::$instance->extendView($view, $vars);
    }

    public static function section(string $id)
    {
        self::stop();
        self::$atualSlot = $id;
        ob_start();
    }

    public static function stop()
    {
        if (self::$atualSlot) {
            if (key_exists(self::$atualSlot, self::$slots) == false) {
                self::$slots[self::$atualSlot] = "";
            }
            self::$slots[self::$atualSlot] .= ob_get_clean();
            self::$atualSlot = null;
        }
    }

    public static function slot($id)
    {
        return self::$slots[$id] ?? "";
    }


    public static function render(string $view, mixed $args = null)
    {
        return self::renderByPath(self::getPath(),$view, $args);
    }

    public static function renderByPath(string $path,string $view, mixed $args = null) {
        $file = "$path/$view.phtml";

        $lastInstance = self::$instance;
        $localInstance = new self($file, $args ?? []);
        self::$instance = $localInstance;
        $content = $localInstance->renderFile();
        self::stop();

        if ($localInstance->hasExtends()) {
            self::$instance = $lastInstance;
            return $localInstance->renderExtend($path);
        } else {

            self::$instance = null;
            self::$atualSlot = null;
            self::$slots = [];
            return $content;
        }
    }

}