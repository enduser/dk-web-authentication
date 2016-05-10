<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:40 PM
 */

namespace N3vrax\DkWebAuthentication\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\Authentication\AuthenticationInterface;
use N3vrax\DkWebAuthentication\AuthFlashMessage;
use N3vrax\DkWebAuthentication\LoginAction;
use N3vrax\DkWebAuthentication\WebAuthOptions;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoginActionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $authentication = $container->has(AuthenticationInterface::class)
            ? $container->get(AuthenticationInterface::class)
            : null;

        $options = $container->has(WebAuthOptions::class)
            ? $container->get(WebAuthOptions::class)
            : new WebAuthOptions($container);

        if(!$template || !$authentication) {
            throw new \Exception("Login action requires the template and authentication services");
        }

        return new LoginAction($router, $template, $authentication, $options, new AuthFlashMessage());
    }
}