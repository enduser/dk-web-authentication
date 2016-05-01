<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 9:36 PM
 */

namespace N3vrax\DkWebAuthentication;

use Zend\Stdlib\AbstractOptions;

class WebAuthOptions extends AbstractOptions
{
    protected $loginRoute;

    protected $logoutRoute;

    protected $preAuthCallback;

    protected $afterLoginRoute;

    protected $afterLogoutRoute;

    protected $loginTemplateName;

    protected $unauthorizedTemplateName;

    public function __construct(array $options = [])
    {
        $error = null;
        if(!isset($options['login_route']) || !is_string($options['login_route'])) {
            $error = 'Web auth login route must be specified as a valid string';
        }
        if(!isset($options['logout_route']) || !is_string($options['logout_route'])) {
            $error = 'Web auth logout route must be specified as a valid string';
        }
        if(!isset($options['login_template_name']) || !is_string($options['login_template_name'])) {
            $error = 'Web auth login template name is required';
        }
        if(!isset($options['after_login_route']) || !is_string($options['after_login_route'])) {
            $error = 'Web auth after login route is required';
        }
        if(!isset($options['after_logout_route']) || !is_string($options['after_logout_route'])) {
            $error = 'Web auth after logout route is required';
        }

        if($error) {
            throw new \Exception($error);
        }

        parent::__construct($options);
    }

    /**
     * @return mixed
     */
    public function getLoginRoute()
    {
        return $this->loginRoute;
    }

    /**
     * @param mixed $loginRoute
     */
    public function setLoginRoute($loginRoute)
    {
        $this->loginRoute = $loginRoute;
    }

    /**
     * @return mixed
     */
    public function getLogoutRoute()
    {
        return $this->logoutRoute;
    }

    /**
     * @param mixed $logoutRoute
     */
    public function setLogoutRoute($logoutRoute)
    {
        $this->logoutRoute = $logoutRoute;
    }

    /**
     * @return mixed
     */
    public function getPreAuthCallback()
    {
        return $this->preAuthCallback;
    }

    /**
     * @param mixed $preAuthCallback
     */
    public function setPreAuthCallback($preAuthCallback)
    {
        $this->preAuthCallback = $preAuthCallback;
    }

    /**
     * @return mixed
     */
    public function getAfterLoginRoute()
    {
        return $this->afterLoginRoute;
    }

    /**
     * @param mixed $afterLoginRoute
     */
    public function setAfterLoginRoute($afterLoginRoute)
    {
        $this->afterLoginRoute = $afterLoginRoute;
    }

    /**
     * @return mixed
     */
    public function getAfterLogoutRoute()
    {
        return $this->afterLogoutRoute;
    }

    /**
     * @param mixed $afterLogoutRoute
     */
    public function setAfterLogoutRoute($afterLogoutRoute)
    {
        $this->afterLogoutRoute = $afterLogoutRoute;
    }

    /**
     * @return mixed
     */
    public function getLoginTemplateName()
    {
        return $this->loginTemplateName;
    }

    /**
     * @param mixed $loginTemplateName
     */
    public function setLoginTemplateName($loginTemplateName)
    {
        $this->loginTemplateName = $loginTemplateName;
    }

    /**
     * @return mixed
     */
    public function getUnauthorizedTemplateName()
    {
        return $this->unauthorizedTemplateName;
    }

    /**
     * @param mixed $unauthorizedTemplateName
     */
    public function setUnauthorizedTemplateName($unauthorizedTemplateName)
    {
        $this->unauthorizedTemplateName = $unauthorizedTemplateName;
    }


}