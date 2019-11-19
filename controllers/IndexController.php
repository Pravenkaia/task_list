<?php


namespace controllers;


use models\Task;
use models\User;

class IndexController extends Controller
{
    protected $model;
    protected $data = ['page' => 'index', 'tasks' => [], 'title' => SITE_NAME, 'h1' => SITE_NAME, 'template' => 'task_list', 'err' => ''];


    public function __construct()
    {
        $this->model = new Task();
    }

    public function getData()
    {
        // echo '<br>' . __LINE__ . ' ' . __FILE__ . '<br>'; echo '<pre>'; var_dump($this->data); echo '</pre>';
        return $this->data;
    }

    /**
     * @param array $params
     * set data by list of tasks
     * page index/index
     */
    public function actionIndex($params = [])
    {
        $this->data['template'] = 'task_list';
        $this->data['tasks'] = $this->model->getPagingTasks($params);
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
     * saving data task after filling of the form
     * page /index/save
     */
    public function actionSave()
    {
        $this->data['template'] = 'task_form';
        $this->data['title'] = 'Task data saving';
        $this->data['h1'] = 'Task data saving';

        // validate task form data
        if ($this->model->validate()) {
            // try to save data, swap template for 'template' = 'task_ok'
            if ($this->model->toSave()) {
                // save data success

                // send confirm mail
                $this->model->saveConfirmEmailMessage();
                $confirmMessage = $this->model->getSaveConfirmEmailMessage();
                $this->data['task_ok'] = "The task was successfully saved!\r\n" . $confirmMessage;

                // swap template for 'template' = 'task_ok'
                $this->data['template'] = 'task_ok';
            } else {
                $this->getErrors('Error data saving');
            }
        } else {
            // incorrect form data, remain the template the same ( 'template' = 'task_form' )
            $this->notValid();
            $this->data['tasks'] = $this->model->getValidData();
        }

    }

    public function actionRedact($param)
    {
        $id = (int)$param[0];
        // check is admin logged, admin only can redact
        if (!User::isLogged()) {
            header('Location: /user/login');
            exit;
        }
        // set headers
        $this->updateHeaders();

        $this->data['tasks'] = $this->model->getTaskId($id);
        if (!$this->data['tasks']) $this->getErrors(' No Task with ID ' . $id);
    }

    public function actionUpdate()
    {
        // check is admin logged, admin only can redact
        if (!User::isLogged()) {
            header('Location: /user/login');
            exit;
        }
        // set headers
        $this->updateHeaders();

        // validate task form data
        if ($this->model->validate()) {
            // if valid data
            // try to update data, swap template for 'template' = 'task_ok'
            if ($this->model->toUpdate()) {
                $this->data['task_ok'] = "The task was successfully Updated!";
                $this->data['template'] = 'task_ok';
            } else {
                $this->getErrors('Error data updating');
            }
        } else {
            // incorrect form data, remain the template the same ( 'template' = 'task_form_redact' )
            $this->notValid();
            $this->data['tasks'] = $this->model->getValidData();
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
     * set  $this->data['err'] if not valid form data
     */
    protected function notValid()
    {
        $err = $this->model->getValidateErrMess();
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