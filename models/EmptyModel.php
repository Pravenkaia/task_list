<?php


namespace models;


class EmptyModel
{
    public function __call($name, $arguments)
    {
        return false;
    }

}