<?php

namespace LiteFrame\Http\Request;

use LiteFrame\Utility\Inflector;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use function config;

class Session extends SymfonySession
{

    /**
     *
     * @var SymfonySession
     */
    private static $instance;
    /**
     * {@inheritdoc}
     */
    public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
    {
        parent::__construct($storage, $attributes, $flashes);
        //load session configurations here
        $this->configure();
        $this->start();
    }

    /**
     * Return singleton class instance.
     *
     * @return SymfonySession
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public static function hasValue($key)
    {
        $self = static::getInstance();
        return $self->has($key);
    }

    public static function put($name, $value)
    {
        $self = static::getInstance();
        return $self->set($name, $value);
    }

    public static function fetch($name, $default = null)
    {
        $self = static::getInstance();
        return $self->get($name, $default);
    }

    public static function destroy()
    {
        $self = static::getInstance();
        $self->clear();
        $self->invalidate();
    }

    private function configure()
    {
        //Set name
        $name = Inflector::underscore(config('app.name'));
        $this->setName(config('session.name', "{$name}_Session"));
//        //Enable cookies for storing session
//        ini_set('session.use_cookies', 1);
//        //Disabled changing session id through URL like http://example.php?PHPSESSID=<session id>
//        ini_set('session.use_only_cookies', 1);
//        ini_set('session.use_trans_sid', 0);
//        //Rejects any session ID from user that doesn't match current one and creates new one
//        ini_set('session.use_strict_mode', config('session.use_strict_mode', 1));
//
//        ini_set('session.cookie_secure', config('session.secure', Server::isHttps()));
//        ini_set('session.cookie_httponly', config('session.httponly', true));
//        ini_set('session.cookie_domain', config('session.domain', Server::getHttpHost()));
//        //session lifetime
//        ini_set('session.cookie_lifetime', config('session.lifetime', 0));
    }
}
