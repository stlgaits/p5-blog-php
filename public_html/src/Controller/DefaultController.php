<?php

namespace App\Controller;

use Exception;
use App\Session;
use App\TwigRenderer;
use App\Service\Redirect;
use ParagonIE\AntiCSRF\AntiCSRF;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Parent of all controllers
 */
class DefaultController
{
    /**
     *
     * @var ServerRequest
     */
    protected $request;

    /**
     * Twig Environment
     *
     * @var TwigRenderer
     */
    protected $environment;

    /**
     * Twig Renderer
     */
    protected $renderer;

    /**
     * User session
     *
     * @var Session
     */
    protected $session;

    /**
     * Redirection service for 301/302 Responses
     *
     * @var Redirect
     */
    protected $redirect;

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        $this->antiCsrf();
        $this->redirect = new Redirect();
    }

    public function antiCsrf()
    {
        // AntiCSRF
        $csrf = new \ParagonIE\AntiCSRF\AntiCSRF;   
        $httpMethod = $this->request->getServerParams()['REQUEST_METHOD'];
        if ($httpMethod === 'POST' && !empty($_POST)) {
            if (!$csrf->validateRequest()) {
                // Log a CSRF attack attempt
                throw new Exception('CSRF attack detected');
            }
        }
    }
}
