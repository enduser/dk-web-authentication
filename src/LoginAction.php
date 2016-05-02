<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:37 PM
 */

namespace N3vrax\DkWebAuthentication;

use N3vrax\DkAuthentication\AuthenticationResult;
use N3vrax\DkAuthentication\Exceptions\RuntimeException;
use N3vrax\DkAuthentication\Interfaces\AuthenticationInterface;
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
    protected $message;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        AuthenticationInterface $authentication,
        WebAuthOptions $options,
        AuthFlashMessage $message = null)
    {
        $this->router = $router;
        $this->template = $template;
        $this->authentication = $authentication;
        $this->options = $options;
        $this->message = $message;
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
            if(!$result || ($result && !$result instanceof AuthenticationResult)) {
                throw new RuntimeException(sprintf("Authentication result must be an instance of %s",
                    AuthenticationResult::class));
            }

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
                $data['message'] = $result->getMessage();
            }
        }
        //set any session messages if any
        if($this->message) {
            $message = $this->message->getMessage('error');
            if($message && !isset($data['message']))
                $data['message'] = $message;
        }

        return new HtmlResponse($this->template->render($this->options->getLoginTemplateName(), $data));
    }
}