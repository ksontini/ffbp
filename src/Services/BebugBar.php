<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/07/2017
 * Time: 12:58
 */

namespace Services;
use DebugBar\StandardDebugBar;


class BebugBar
{
    public $debugbar;
    public $debugbarRenderer;
    public $active=false;


    public function __construct()
    {
        $this->debugbar = new StandardDebugBar();
        $this->debugbarRenderer = $this->debugbar->getJavascriptRenderer("/Resources/");
    }

    public function debug($data)
    {
        $this->active=true;
        $this->debugbar['messages']->addMessage($data);
    }

    public function renderHead()
    {
        return $this->debugbarRenderer->renderHead();
    }

    public function render()
    {
        return $this->debugbarRenderer->render();
    }
}