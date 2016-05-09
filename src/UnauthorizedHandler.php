<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 10:45 PM
 */

namespace N3vrax\DkWebAuthentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class UnauthorizedHandler
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
     * @var WebAuthOptions
     */
    protected $options;

    /**
     * @var AuthFlashMessage
     */
    protected $flashMessages;

    protected $authenticationErrorCodes = [401, 407];

    /**
     * UnauthorizedHandler constructor.
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param WebAuthOptions $options
     * @param AuthFlashMessage $flashMessages
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        WebAuthOptions $options,
        AuthFlashMessage $flashMessages = null
    )
    {
        $this->template = $template;
        $this->options = $options;
        $this->router = $router;
        $this->flashMessages = $flashMessages;
    }

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     */

    public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    )
    {
        if(in_array($response->getStatusCode(), $this->authenticationErrorCodes))
        {
            $messages = [];
            if(is_array($error)) {
                foreach ($error as $e) {
                    if(is_string($e)) {
                        $messages[] = $e;
                    }
                }
            }
            else if(is_string($error)) {
                $messages[] = $error;
            }

            $messages = empty($messages)
                ? ['Authorization failure. Check your credentials and try again']
                : $messages;

            //add a flash message in case the login page displays errors
            if ($this->flashMessages) {
                $this->flashMessages->addMessage('error', $messages);
            }

            $uri = new Uri($this->router->generateUri($this->options->getLoginRoute()));
            if($this->options->getAllowRedirects())
                $uri = $uri->withQuery('redirect=' . urlencode($request->getUri()));

            return new RedirectResponse($uri);
        }

        if($next) {
            return $next($request, $response, $error);
        }

        return $response;
    }

}