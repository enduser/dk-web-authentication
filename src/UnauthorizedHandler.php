<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 10:45 PM
 */

namespace N3vrax\DkWebAuthentication;

use N3vrax\DkAuthentication\AuthenticationError;
use N3vrax\DkError\AbstractErrorHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class UnauthorizedHandler extends AbstractErrorHandler
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
        //we dont need a response strategy
        //we'll handle 401 error and leave others to reach the final handler with the correct status code
        parent::__construct(null);
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
        if($error instanceof AuthenticationError) {
            $message = empty($error->getMessage()) ? 'Authorization failure. Check your credentials and try again'
                : $error->getMessage();

            //add a flash message in case the login page displays errors
            if ($this->flashMessages) {
                $this->flashMessages->addMessage('error', $message);
            }

            $uri = new Uri($this->router->generateUri($this->options->getLoginRoute()));
            if($this->options->getAllowRedirects())
                $uri = $uri->withQuery('redirect=' . urlencode($request->getUri()));
            
            return new RedirectResponse($uri);
        }

        //the parent will check for other types of Error class
        //in this case, as we don't have a response strategy defined, it will call next with the modified response status code
        //for other types of errors, it will call next with nothing modified
        return parent::__invoke($error, $request, $response, $next);
    }

}