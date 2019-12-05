<?php


namespace controllers;


use models\Session;
use models\User;

class UserController extends Controller
{
    protected $data = [
        'admin_can_do_it' => null,
        'page' => 'user',
        'title' => 'User',
        'h1' => 'User',
        'template' => 'user_index',
        'err' => ''
    ];


    public function actionIndex()
    {
        if (!User::isLogged()) {
            $this->actionLogin();
        } else {
            $this->actionAdmin();
        }
    }

    public function actionLogin()
    {
        $this->data['template'] = 'user_form';
        $this->data['title'] = 'Authorization';
        $this->data['h1'] = 'Authorization';
        $this->data['text'] = 'Fill the form, please';
    }

    public function actionAuth()
    {
        if (User::login()) $this->actionAdmin();
        else $this->getErrors('Authorisation error');
    }


    public function actionAdmin()
    {
        $this->data['template'] = 'ok';
        $this->data['title'] = 'Admin';
        $this->data['h1'] = 'Admin';
        $this->data['ok'] = "Hello, Admin! \n\r You can redact tasks";
        if (User::isLogged()) {
            $_SESSION['admin_can_do_it'] = 'yes';
        }

    }

    public function actionLogout()
    {
        $this->data['template'] = 'ok';
        $this->data['title'] = 'Guest';
        $this->data['h1'] = 'Guest';
        $this->data['ok'] = "Hello, Guest! \n\r You can make and view tasks";
        User::logout();
        header("Location: /user/index");
    }


    public function getData()
    {
        // TODO: Implement getData() method.
        return $this->data;
    }
}