<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:37 PM
 */

namespace N3vrax\DkWebAuthentication;

use N3vrax\DkAuthentication\Interfaces\AuthenticationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LogoutAction
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationInterface $authentication)
    {
        $this->router = $router;
        $this->template = $template;
        $this->authentication = $authentication;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {

    }

}