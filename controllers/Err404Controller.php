<?php


namespace controllers;


class Err404Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->setData();
        $this->getData();
    }

    public function getData()
    {
        //var_dump($this->data);
        return $this->data;
    }

    public function actionIndex()
    {
        $this->setData();
    }

    protected function setData()
    {
        $this->data = [
            'page' => ERROR_404,
            'error' => 'ERROR 404. Incorrect URL',
            'title' => SITE_NAME,
            'h1' => 'Page is not found!!',
            'template' => 'err404'
        ];
    }

    public function __call($name, $arguments)
    {
        $this->data = ['h1' => 'Page and Method is not found!!'];
        $this->getData();
    }
}