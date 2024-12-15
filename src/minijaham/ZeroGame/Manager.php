<?php

declare(strict_types=1);

namespace minijaham\ZeroGame;

abstract class Manager
{
    /**
     * Manager Constructor.
     * 
     * @param Loader $plugin
     */
    protected function __construct(private Loader $plugin)
    {
        $this->init();
    }

    /**
     * Abstract function to implement. This function is called when a manager is initiated.
     * 
     * @return void
     */
    protected abstract function init() : void;

    /**
     * Returns Loader class instance.
     * 
     * @return Loader
     */
    protected function getPlugin() : Loader
    {
        return $this->plugin;
    }
}