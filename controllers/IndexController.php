<?php


namespace controllers;


use models\ITaskActions;
use models\User;

class IndexController extends Controller
{
    protected $data = ['page' => 'index', 'tasks' => [], 'title' => SITE_NAME, 'h1' => SITE_NAME, 'template' => 'task_list', 'err' => ''];


    public function getData()
    {
        return $this->data;
    }

    /**
     * @param ITaskActions $model
     * @param array $params
     */
    public function actionIndex($model, $params = [])
    {
        $this->data['template'] = 'task_list';
        $this->data['tasks'] = $model->getPagingTasks($params);
        $this->data['title'] = 'Task list';
        $this->data['h1'] = 'Task list';
        $this->data['limit'] = LIMIT;

        $this->data['tasks'] = $this->ifEmpty();
    }


    /**
     * form to make task
     * page index/create
     */
    public function actionCreate()
    {
        $this->data['template'] = 'task_form';
        $this->data['title'] = 'Make a task';
        $this->data['h1'] = 'Make a task';
    }


    /**
     * @param ITaskActions $model
     * saving data task after filling of the form
     * page /index/save
     */
    public function actionSave($model)
    {
        $this->data['template'] = 'task_form';
        $this->data['title'] = 'Task data saving';
        $this->data['h1'] = 'Task data saving';

        if ($model->validate()) {
            if ($model->toSave()) {

                // send confirm mail
                $model->saveConfirmEmailMessage();
                $confirmMessage = $model->getSaveConfirmEmailMessage();

                $this->data['ok'] = "The task was successfully saved!\r\n" . $confirmMessage;
                $this->data['template'] = 'ok';
            } else {
                $this->getErrors('Error data saving');
            }
        } else {
            $this->notValid();
            $this->data['tasks'] = $model->getValidData();
        }

    }

    /**
     * @param ITaskActions $model
     * @param $param
     */
    public function actionRedact($model, $param)
    {
        $id = (int)$param[0];
        // check is admin logged, admin only can redact
        if (!User::isLogged()) {
            header('Location: /user/login');
            exit;
        }
        // set headers
        $this->updateHeaders();

        $this->data['tasks'] = $model->getTaskId($id);
        if (!$this->data['tasks']) $this->getErrors(' No Task with ID ' . $id);
    }

    /**
     * @param ITaskActions $model
     */
    public function actionUpdate($model)
    {
        // check is admin logged, admin only can update
        if (!User::isLogged()) {
            header('Location: /user/login');
            exit;
        }
        // set headers
        $this->updateHeaders();

        if ($model->validate()) {
            if ($model->toUpdate()) {
                $this->data['ok'] = "The task was successfully Updated!";
                $this->data['template'] = 'ok';
            } else {
                $this->getErrors('Error data updating');
            }
        } else {
            $this->notValid();
            $this->data['tasks'] = $model->getValidData();
        }
    }

    /**
     *
     */
    protected function updateHeaders()
    {
        $this->data['template'] = 'task_form_redact';
        $this->data['title'] = 'Task redacting';
        $this->data['h1'] = 'Task redacting';
    }

    /**
     * @param ITaskActions $model
     * set  $this->data['err'] if not valid form data
     */
    protected function notValid($model)
    {
        $err = $model->getValidateErrMess();
        //var_dump($err);
        $this->data['err'] = 'Validation error!';
        $this->data['err'] = $err;
    }

    /**
     * @return array|mixed
     * if empty data, set info data
     */
    protected function ifEmpty()
    {
        if (empty($this->data['tasks'])) {
            return ['author' => 'No data'];
        }
        return $this->data['tasks'];
    }


}