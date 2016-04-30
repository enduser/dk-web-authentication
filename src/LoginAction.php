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

class LoginAction
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

    /**
     * @var callable
     */
    protected $preAuthCallback;

    /**
     * @var WebAuthOptions
     */
    protected $options;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationInterface $authentication,
        WebAuthOptions $options,
        callable $preAuth = null)
    {
        $this->router = $router;
        $this->template = $template;
        $this->authentication = $authentication;
        $this->preAuthCallback = $preAuth;
        $this->options = $options;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = [];

        if($this->authentication->hasIdentity()) {

        }

        if($request->getMethod() === 'POST')
        {
            if($this->preAuthCallback && is_callable($this->preAuthCallback))
            {
                $preAuthCallbackResult = call_user_func($this->preAuthCallback, $request, $response);
                //return if pre auth returned a ResponseInterface
                if($preAuthCallbackResult && $preAuthCallbackResult instanceof ResponseInterface)
                {
                    return $preAuthCallbackResult;
                }
                //get modified request and response if its a PreAuthCallbackResult
                if($preAuthCallbackResult && $preAuthCallbackResult instanceof PreAuthCallbackResult)
                {
                    $request = $preAuthCallbackResult->getRequest();
                    $response = $preAuthCallbackResult->getResponse();
                }
            }

            $result = $this->authentication->authenticate($request, $response);

        }

    }
}