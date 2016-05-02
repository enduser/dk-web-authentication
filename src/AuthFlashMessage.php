<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 8:43 PM
 */

namespace N3vrax\DkWebAuthentication;

class AuthFlashMessage
{
    protected $storage;

    protected $storageKey = 'dk_auth_messages';

    protected $fromPrevious = [];

    protected $fromNext = [];

    public function __construct()
    {
        if(!isset($_SESSION)) {
            throw new \Exception("AuthFlashMessage requires the session to be started");
        }

        $this->storage = &$_SESSION;

        // Load messages from previous request
        if (isset($this->storage[$this->storageKey]) && is_array($this->storage[$this->storageKey])) {
            $this->fromPrevious = $this->storage[$this->storageKey];
        }
        $this->storage[$this->storageKey] = [];
    }

    /**
     * Add flash message
     *
     * @param string $key The key to store the message under
     * @param mixed  $message Message to show on next request
     */
    public function addMessage($key, $message)
    {
        //Create Array for this key
        if (!isset($this->storage[$this->storageKey][$key])) {
            $this->storage[$this->storageKey][$key] = array();
        }
        //Push onto the array
        $this->storage[$this->storageKey][$key][] = $message;
    }
    /**
     * Get flash messages
     *
     * @return array Messages to show for current request
     */
    public function getMessages()
    {
        return $this->fromPrevious;
    }
    /**
     * Get Flash Message
     *
     * @param string $key The key to get the message from
     * @return mixed|null Returns the message
     */
    public function getMessage($key)
    {
        //If the key exists then return all messages or null
        return (isset($this->fromPrevious[$key])) ? $this->fromPrevious[$key] : null;
    }
}