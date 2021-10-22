<?php

namespace App;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{

    /**
     * @var FilesystemLoader
     */
    private $loader;

    /**
     *
     * @var Environment
     */
    private $environment;

    public function __construct()
    {
        $this->loader = new FilesystemLoader('./../templates');
        $this->environment = new Environment($this->loader, ['debug' => true]);
        $this->environment->addExtension(new \Twig\Extension\DebugExtension());
        $this->loader->addPath('./../public', 'public');
    }

    public function getTwig()
    {
        return $this->environment;
    }
}
