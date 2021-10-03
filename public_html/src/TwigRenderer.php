<?php

namespace App;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
// use Twig\Extra\CssInliner\CssInlinerExtension;

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
        // $this->environment->addExtension(new CssInlinerExtension());
        $this->loader->addPath('./../public', 'public');
    }

    public function getTwig()
    {
        return $this->environment;
    }
}
