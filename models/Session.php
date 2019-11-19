<?php


namespace models;


class Session
{
    private static $lifetime = 3600; // 1 час
    private static $cookieName = "cid";
    private static $started = false;

    public static function isCreated()
    {
        return (!empty($_COOKIE[self::$cookieName]) and ctype_alnum($_COOKIE[self::$cookieName])) ? true : false;
    }

    public static function start()
    {
        if (!self::$started) {

            if (!empty($_COOKIE[self::$cookieName]) and !ctype_alnum($_COOKIE[self::$cookieName])) {
                unset($_COOKIE[self::$cookieName]);
            }
            session_set_cookie_params(self::$lifetime, '/');
            session_name(self::$cookieName);
            session_start();
            self::$started = true;
        }
    }

    public static function set($name, $value)
    {
        if (self::$started) {
            $_SESSION[$name] = $value;
        }
    }

    public static function get($name)
    {
        if (self::$started) {
            return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
        }
    }


    public static function destroy()
    {
        if (self::$started) {
            self::$started = false;
            unset($_COOKIE[self::$cookieName]);
            setcookie(self::$cookieName, '', 1, '/');
            session_destroy();
        }
    }


}