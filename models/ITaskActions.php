<?php


namespace models;


interface ITaskActions
{
    public function getPagingTasks($params);

    public function validate():bool;

    public function saveConfirmEmailMessage():bool;

    public function getSaveConfirmEmailMessage();

    public function getValidData();

    public function getTaskId($id);

    public function toSave():bool;

    public function toUpdate():bool;
}