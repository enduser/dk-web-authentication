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
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
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
     * UnauthorizedHandler constructor.
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param WebAuthOptions $options
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        WebAuthOptions $options
    )
    {
        $this->template = $template;
        $this->options = $options;
        $this->router = $router;
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
            $data = [];
            if($error->getCode() === 401 || $response->getStatusCode() === 401)
            {
                $message = empty($error->getMessage())? 'Authorization failure. Try again.' : $error->getMessage();
                $extra = $error->getExtra() !== null ? $error->getExtra() : null;
                if(is_array($extra))
                {
                    $data = $extra;
                }
                if(!isset($data['message'])) {
                    $data['message'] = $message;
                }

                $templateName = $this->options->getUnauthorizedTemplateName();
                //if no error template set, redirect to login route by default
                //else display the error template(which can be the same login template with the error message)
                if(!$templateName) {
                    return new RedirectResponse($this->router->generateUri($this->options->getLoginRoute()));
                }
                else {
                    return new HtmlResponse(
                        $this->template->render($templateName, $data));
                }
            }
        }

        if($next)
            return $next($request, $response, $error);
        else return $response;
    }

}