<?php


namespace models;


class User
{
    protected static $instance = null;  // serialized User
    protected static $logged = false; // or True


    public static function check()
    {
        Session::start();
        if ($data = Session::get('User')) {

            $data = unserialize($data);
            self::$logged = $data['logged'];
            self::$instance = $data['instance'];

            if (self::$logged && self::$instance != null)
                return true;
            else {
                Session::destroy();
                return false;
            }

        } else {
            Session::destroy();
            return false;
        }
    }

    public static function isLogged()
    {
        return self::$logged;
    }


    public static function login()
    {
        if (!$validData = self::validate()) return false;

        $login = $validData['login'];
        $pass = $validData['pass'];

        if ($passDB = self::searchUser($login)) {
            // check pass
            if (password_verify($pass, $passDB)) {
                // password in DB password_hash ( '123', PASSWORD_DEFAULT);
                Session::start();
                self::$logged = true;
                self::$instance = $login;
                self::addToSession();

                return true;
            }
            return false;
        }
        return false;
    }

    private static function validate() {
        $login = htmlspecialchars(strip_tags($_POST['login']));
        $pass = htmlspecialchars(strip_tags($_POST['pass']));

        if ($login != $_POST['login'] || $pass != $_POST['pass'] || !$pass || !$login) {
            return false;
        }
        else return ['login' => $login, 'pass' => $pass];
    }

    private static function searchUser($login) {
        $sql = "SELECT pass FROM " . TABLE_USERS . " WHERE login LIKE :login";

        $results = DB::getInstance()->querySelect($sql, [':login' => $login]);
        if ($results) return $results[0]['pass'];
        return false;
    }

    private static function addToSession()
    {
        Session::set("User", serialize(array(
            "instance" => self::$instance,
            "logged" => self::$logged
        )));
    }

    public static function logout () {
        self::$instance = null;
        self::$logged  = false;
        Session::destroy();
    }

}