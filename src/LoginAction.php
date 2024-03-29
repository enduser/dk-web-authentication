<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:37 PM
 */

namespace N3vrax\DkWebAuthentication;

use N3vrax\DkAuthentication\AuthenticationResult;
use N3vrax\DkAuthentication\Authentication\AuthenticationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
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
     * @var WebAuthOptions
     */
    protected $options;

    /**
     * @var AuthFlashMessage
     */
    protected $messages;

    /**
     * LoginAction constructor.
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param AuthenticationInterface $authentication
     * @param WebAuthOptions $options
     * @param AuthFlashMessage|null $messages
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationInterface $authentication,
        WebAuthOptions $options,
        AuthFlashMessage $messages = null)
    {
        $this->router = $router;
        $this->template = $template;
        $this->authentication = $authentication;
        $this->options = $options;
        $this->messages = $messages;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = [];
        if($this->authentication->hasIdentity()) {
            return new RedirectResponse($this->router->generateUri($this->options->getAfterLoginRoute()));
        }

        if($request->getMethod() === 'POST')
        {
            $preAuthCallback = $this->options->getPreAuthCallback();
            if($preAuthCallback && is_callable($preAuthCallback))
            {
                $preAuthCallbackResult = call_user_func($preAuthCallback, $request, $response);
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

            $data = $request->getParsedBody();
            $result = $this->authentication->authenticate($request, $response);

            //don't allow false or null authentication results or other types of results
            if(!$result)
            {
                throw new \Exception("Web auth: authentication result cannot be empty. ".
                    "Make sure you have prepared the requests according to the authentication adapter needs");
            }
            else if($result && !$result instanceof AuthenticationResult) {
                throw new \Exception(sprintf("Web auth: authentication result must be an instance of %s",
                    AuthenticationResult::class));
            }
            else {
                if($result->isValid()) {
                    $redirectUri = $this->router->generateUri($this->options->getAfterLoginRoute());
                    if($this->options->getAllowRedirects() === true)
                    {
                        $params = $request->getQueryParams();
                        if(isset($params['redirect']) && !empty($params['redirect'])) {
                            $redirectUri = new Uri(urldecode($params['redirect']));
                        }
                    }
                    return new RedirectResponse($redirectUri);
                }
                else {
                    $data['messages'] = $result->getMessage();
                }
            }
        }
        //set any session messages if any
        if($this->messages) {
            $message = $this->messages->getMessage('error');
            if($message && !isset($data['messages']))
                $data['messages'] = $message;
        }

        return new HtmlResponse($this->template->render($this->options->getLoginTemplateName(), $data));
    }
}