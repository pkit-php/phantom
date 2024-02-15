<?php

namespace Pkit;

class View
{
    private ?string $extends = null;
    private ?array $extendsVars = null;
    private array $sections = [];
    private array $slots = [];

    function __construct(private string $path)
    {
    }

    public function render(string $view, array $vars)
    {
        $file = "$this->path/$view.phtml";
        if (!file_exists($file)) {
            throw new \Exception("file '$file' not exists", 500);
        }
        ob_start();

        extract($vars);
        include $file;

        return ob_get_clean();
    }

    public function getSlot($id): string {
        $slot = $this->slots[$id] ?? "";
        unset($this->slots[$id]);
        return $slot;
    }

    public function section(string $id)
    {
        $this->sections[] = $id;
        ob_start();
    }

    function stop() {
        if (empty($this->sections) == false) {
            $actualSlot = array_pop($this->sections);
            
            if (key_exists($actualSlot, $this->slots) == false) {
                $this->slots[$actualSlot] = "";
            }
            $this->slots[$actualSlot] .= ob_get_clean();
        }
    }

    public function extend(string $view, array $vars = [])
    {
        $this->extends = $view;
        $this->extendsVars = $vars;
    }

    

    public function renderExtend()
    {
        $extends = $this->extends;
        $extendsVars = $this->extendsVars;
        $this->extends = null;
        $this->extendsVars = null;
        return Phantom::renderView($this,$extends, $extendsVars);
    }

    public function hasExtends()
    {
        return !!$this->extends;
    }

}