<?php

namespace App;

use Twig\Environment;
use Twig\TwigFunction;
use \ParagonIE\AntiCSRF\AntiCSRF;
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
        $this->loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->environment = new Environment($this->loader, ['debug' => true]);
        $this->environment->addExtension(new \Twig\Extension\DebugExtension());
        $this->loader->addPath(__DIR__ . '/../public', 'public');
    }

    /**
     * Allows us to call a 'form_token' function from Twig templates to prevent CSRF
     */
    public function addAntiCsrf()
    {
        $this->environment->addFunction(
            new TwigFunction(
                'form_token',
                function ($lock_to = null) {
                    static $csrf;
                    if ($csrf === null) {
                        $csrf = new AntiCSRF;
                    }
                    return $csrf->insertToken($lock_to, false);
                },
                ['is_safe' => ['html']]
            )
        );
    }

    public function getTwig()
    {
        $this->addAntiCsrf();
        return $this->environment;
    }
}
