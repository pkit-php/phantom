<?php

namespace Pkit\Phantom;

use Pkit\Phantom\View\Env as ViewEnv;

class View extends ViewEnv
{
    private string $file;
    private array $vars = [];
    private readonly string $extends;
    private readonly array $extendsVars;

    private static ?View $instance = null;

    private static ?string $lastSlot = null;
    private static array $slots = [];

    function __construct(string $file)
    {
        if (!file_exists($file)) {
            throw new \Exception("file '$file' not exists", 500);
        }
        $this->file = $file;
    }

    public function assign(array $vars)
    {
        $this->vars = $vars;
        return $this;
    }

    function renderFile()
    {
        ob_start();

        extract($this->vars);
        include $this->file;

        return ob_get_clean();
    }

    public function extendView(string $view, array $vars = [])
    {
        $this->extends = $view;
        $this->extendsVars = $vars;
    }

    public function renderExtend()
    {
        return View::render($this->extends, $this->extendsVars);
    }

    public function hasExtends()
    {
        try {
            return !!$this->extends;
        }
        catch (\Throwable $th) {
            return false;
        }
    }

    public static function extend(string $view, array $vars = [])
    {
        self::$instance->extendView($view, $vars);
    }

    public static function section(string $id)
    {
        View::stop();
        self::$lastSlot = $id;
        ob_start();
    }

    public static function stop()
    {
        if (self::$lastSlot) {
            if (key_exists(self::$lastSlot, self::$slots) == false) {
                self::$slots[self::$lastSlot] = "";
            }
            self::$slots[self::$lastSlot] .= ob_get_clean();
            self::$lastSlot = null;
        }
    }

    public static function slot($id)
    {
        return self::$slots[$id] ?? "";
    }

    private static function getPathFile(string $file)
    {
        return self::getPath() . "/$file.phtml";
    }

    private static function reset()
    {
        self::$instance = null;
        self::$lastSlot = null;
        self::$slots = [];
    }

    public static function render(string $view, mixed $args = null)
    {
        $file = self::getPathFile($view);

        $lastInstance = self::$instance;
        $localInstance = new View($file);
        self::$instance = $localInstance;
        $content = $localInstance->assign($args ?? [])->renderFile();
        View::stop();

        if ($localInstance->hasExtends()) {
            self::$instance = $lastInstance;
            return $localInstance->renderExtend();
        }
        else {
            self::reset();
            return $content;
        }
    }

}