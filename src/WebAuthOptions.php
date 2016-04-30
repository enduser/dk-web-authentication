<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 9:36 PM
 */

namespace N3vrax\DkWebAuthentication;

class WebAuthOptions
{
    protected $loginRoute;

    protected $logoutRoute;

    protected $preAuthCallback;

    protected $afterLoginRoute;

    protected $afterLogoutRoute;

    protected $loginTemplateName;

    public function __construct(array $config = [])
    {

    }

    public function getLoginRoute()
    {
        return $this->loginRoute;
    }

    public function getLogoutRoute()
    {
        return $this->loginRoute;
    }

    public function getPreAuthCallback()
    {
        return $this->preAuthCallback;
    }

    public function getAfterLoginRoute()
    {
        return $this->afterLoginRoute;
    }

    public function getAfterLogoutRoute()
    {
        return $this->afterLogoutRoute;
    }

    public function getLoginTemplateName()
    {
        return $this->loginTemplateName;
    }
}