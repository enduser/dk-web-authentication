<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 3:15 PM
 */

namespace N3vrax\DkWebAuthentication\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkWebAuthentication\UnauthorizedHandler;
use N3vrax\DkWebAuthentication\WebAuthOptions;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class UnauthorizedHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $options = $container->has(WebAuthOptions::class)
            ? $container->get(WebAuthOptions::class)
            : new WebAuthOptions();

        if(!$template) {
            throw new \Exception("Unauthorized handler requires a template renderer");
        }

        return new UnauthorizedHandler($router, $template, $options);
    }
}