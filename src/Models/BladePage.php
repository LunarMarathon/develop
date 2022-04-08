<?php

namespace Hyde\Framework\Models;

/**
 * A basic wrapper for the custom Blade View compiler.
 */
class BladePage
{
    /**
     * The name of the Blade View to compile.
     *
     * @var string
     *
     * Must be a top level file relative to
     * resources\views\pages\ and ending
     * in .blade.php to be compiled.
     */
    public string $view;

    /**
     * @param  string  $view
     */
    public function __construct(string $view)
    {
        $this->view = $view;
    }

    /**
     * Since this model also acts as a Blade View compiler,
     * we implement the get method for compatability.
     */
    public function get(): BladePage
    {
        return $this;
    }
}
