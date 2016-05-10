<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:58 PM
 */

namespace N3vraxTest\DkWebAuthentication;

use N3vrax\DkAuthentication\Authentication\AuthenticationInterface;
use N3vrax\DkWebAuthentication\LoginAction;
use N3vrax\DkWebAuthentication\WebAuthOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoginActionTest extends \PHPUnit_Framework_TestCase
{
    private $router;

    private $template;

    private $authentication;

    private $options;

    private $request;

    private $response;

    public function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->template = $this->prophesize(TemplateRendererInterface::class);
        $this->authentication = $this->prophesize(AuthenticationInterface::class);
        $this->options = $this->prophesize(WebAuthOptions::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);
    }

    public function getLoginAction()
    {
        return new LoginAction(
            $this->router->reveal(),
            $this->template->reveal(),
            $this->authentication->reveal(),
            $this->options->reveal()
        );
    }

    public function testLoginActionRedirectsIfAuthenticated()
    {
        $this->options->getAfterLoginRoute()->willReturn('login');
        $this->router->generateUri('login')->willReturn('http://localhost/login');

        $this->authentication->hasIdentity()->willReturn(true);
        $action = $this->getLoginAction();

        $response = $action($this->request->reveal(), $this->response->reveal());
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testLoginMethodNotPost()
    {
        $this->authentication->hasIdentity()->willReturn(false);
        $this->request->getMethod()->willReturn('GET');
        $this->options->getLoginTemplateName()->willReturn('login');
        $this->template->render('login', [])->willReturn('some html');
        $action = $this->getLoginAction();

        $response = $action($this->request->reveal(), $this->response->reveal());
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    public function testLoginPreAuthCallbackReturnsResponse()
    {
        $this->authentication->hasIdentity()->willReturn(false);
        $this->request->getMethod()->willReturn('POST');
        $this->options->getPreAuthCallback()->willReturn(
            function(){return new JsonResponse('');});

        $action = $this->getLoginAction();

        $response = $action($this->request->reveal(), $this->response->reveal());
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @expectedException \Exception
     */
    public function testAuthenticationResultIsFalsy()
    {
        $this->authentication->hasIdentity()->willReturn(false);
        $this->request->getMethod()->willReturn('POST');
        $this->options->getPreAuthCallback()->willReturn(null);
        $this->request->getParsedBody()->willReturn([]);
        $request = $this->request->reveal();
        $response = $this->response->reveal();
        $this->authentication->authenticate($request, $response)->willReturn(null);

        $action = $this->getLoginAction();

        $action($request, $response);
    }
}