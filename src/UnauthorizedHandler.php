<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 10:45 PM
 */

namespace N3vrax\DkWebAuthentication;

use N3vrax\DkAuthentication\AuthenticationError;
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
        if($error instanceof AuthenticationError)
        {
            if($error->getCode() === 401 || $response->getStatusCode() === 401)
            {
                $message = empty($error->getMessage())? 'Authorization failure. ' : $error->getMessage();
                if($this->flashMessages) {
                    $this->flashMessages->addMessage('error', $message);
                }

                $uri = new Uri($this->router->generateUri($this->options->getLoginRoute()));
                $uri = $uri->withQuery('redirect=' . urlencode($request->getUri()));
                return new RedirectResponse($uri);
            }
        }

        if($next)
            return $next($request, $response, $error);
        else return $response;
    }

}