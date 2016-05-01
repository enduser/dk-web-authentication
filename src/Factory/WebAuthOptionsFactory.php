<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 2:50 PM
 */

namespace N3vrax\DkWebAuthentication\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkWebAuthentication\WebAuthOptions;

class WebAuthOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = isset($config['authentication']) ? $config['authentication'] : [];
        if(is_array($config) && isset($config['web']))
        {
            return new WebAuthOptions($config['web']);
        }

        return new WebAuthOptions();
    }
}