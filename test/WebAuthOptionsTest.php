<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 6:09 PM
 */

namespace N3vraxTest\DkWebAuthentication;

use N3vrax\DkWebAuthentication\WebAuthOptions;

class WebAuthOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testMissingLoginRoute()
    {
        new WebAuthOptions([
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
        new WebAuthOptions([
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
        new WebAuthOptions([
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
        new WebAuthOptions([
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
        new WebAuthOptions([
            'login_route' => 'login',
            'logout_route' => 'logout',
            'login_template_name' => 'login',
            'after_login_route' => 'home',
            'after_logout_route' => ''
        ]);
    }

    public function testInitialization()
    {
        $options = new WebAuthOptions([
            'login_route' => 'login',
            'logout_route' => 'logout',
            'login_template_name' => 'login',
            'after_login_route' => 'home',
            'after_logout_route' => 'login',
            'pre_auth_callback' => 'foo'
        ]);

        $this->assertEquals('login', $options->getLoginRoute());
        $this->assertEquals('logout', $options->getLogoutRoute());
        $this->assertEquals('login', $options->getLoginTemplateName());
        $this->assertEquals('home', $options->getAfterLoginRoute());
        $this->assertEquals('login', $options->getAfterLogoutRoute());
        $this->assertEquals('foo', $options->getPreAuthCallback());
        $this->assertEquals(true, $options->getAllowRedirects());
    }
}