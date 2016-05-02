<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 6:09 PM
 */

namespace N3vraxTest\DkWebAuthentication;

use Interop\Container\ContainerInterface;
use N3vrax\DkWebAuthentication\WebAuthOptions;

class WebAuthOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testMissingLoginRoute()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        new WebAuthOptions($container, [
            'login_route' => '',
            'logout_route' => 'logout',
            'login_template_name' => 'login',
            'after_login_route' => 'home',
            'after_logout_route' => 'login'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingLogoutRoute()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        new WebAuthOptions($container, [
            'login_route' => 'login',
            'logout_route' => '',
            'login_template_name' => 'login',
            'after_login_route' => 'home',
            'after_logout_route' => 'login'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingLoginTemplateName()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        new WebAuthOptions($container, [
            'login_route' => 'login',
            'logout_route' => 'logout',
            'login_template_name' => '',
            'after_login_route' => 'home',
            'after_logout_route' => 'login'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingAfterLoginRoute()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        new WebAuthOptions($container, [
            'login_route' => 'login',
            'logout_route' => 'logout',
            'login_template_name' => 'login',
            'after_login_route' => '',
            'after_logout_route' => 'login'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingAfterLogoutRoute()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        new WebAuthOptions($container, [
            'login_route' => 'login',
            'logout_route' => 'logout',
            'login_template_name' => 'login',
            'after_login_route' => 'home',
            'after_logout_route' => ''
        ]);
    }

    public function testInitialization()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $options = new WebAuthOptions($container, [
            'login_route' => 'login',
            'logout_route' => 'logout',
            'login_template_name' => 'login',
            'after_login_route' => 'home',
            'after_logout_route' => 'login'
        ]);

        $this->assertEquals('login', $options->getLoginRoute());
        $this->assertEquals('logout', $options->getLogoutRoute());
        $this->assertEquals('login', $options->getLoginTemplateName());
        $this->assertEquals('home', $options->getAfterLoginRoute());
        $this->assertEquals('login', $options->getAfterLogoutRoute());
        $this->assertEquals(true, $options->getAllowRedirects());
    }
}