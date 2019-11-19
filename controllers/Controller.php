<?php


namespace controllers;


abstract class Controller {

    protected $data = ['error' => '', 'page' => MAIN_PAGE, 'title' => SITE_NAME, 'h1' => SITE_NAME];

    public function __construct() {
    }

    abstract public function getData();

    public function getErrors($err = ''){
        // echo __LINE__ . ' '. __FILE__ ; echo '<br>method getErrors()';
        $this->data['page'] = ERROR_404;
        $this->data['error'] = $err ?? 'Wrong URL';
        return false;
    }

    public function __call($author, array $params) {
        $this->getErrors('Method is not found');
    }
}