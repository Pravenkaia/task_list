<?php


namespace models;


use controllers\Err404Controller;

use PDO;

/**
 * Class Task
 * @package models
 * $countOfTasks - number, quantity of task
 * $resultTaskId  - array, task data by id
 * $resultTasksList - array, tasks list data on one of the pages
 * $resultPagingTasks - array, tasks list data one one page + pag
 * $validData - validated form data
 * $validateErrMess - array of messages about error data
 *
 * $sentEmailConfirmMessage - message about sending confirm email
 *  ;
 */
class Task
{
    private $countOfTasks = 0;
    private $resultTaskId = [];
    private $resultTasksList = [];
    private $resultPagingTasks = [];
    private $validData = [];

    private $sentEmailConfirmMessage;
    private $validateErrMess = [];


    /**
     * @return array
     */
    public function getValidData()
    {
        return $this->validData;
    }


    /**
     * @return array
     */
    public function getValidateErrMess(): array
    {
        return $this->validateErrMess;
    }


    public function getTaskId($id)
    {
        $this->taskId($id);
        return $this->resultTaskId;
    }


    /**
     * @return bool
     */
    public function toSave()
    {
        if ($this->save($this->validData)) return true;
        return false;
    }


    public function getCountTasks()
    {
        $this->countTasks();
        return $this->countOfTasks;
    }


    /**
     * @return mixed
     */
    public function getSaveConfirmEmailMessage()
    {
        return $this->sentEmailConfirmMessage;
    }


    public function toUpdate()
    {
        if ($this->update($this->validData)) return true;
        return false;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPagingTasks($params)
    {
        $params[0] = $params[0] ?? 'id_task';

        if ($params[1] !== 'desk') $params[1] = 'ask';
        $params[2] = $params[2] ?? 0;

        // list of tasks
        $this->tasksList($params);
        $res = $this->resultTasksList;

        // count all tasks
        $this->countTasks();

        $count_pages = (int)ceil($this->getCountTasks() / LIMIT) ?? 1;

        if ($res) {
            $this->resultPagingTasks = [
                // result of select request
                'bd' => $res,
                // pagination parameters
                'pagination' => [
                    'order_by' => $params[0],
                    'direction' => $params[1],
                    'n' => $params[2],
                    'count_pages' => $count_pages
                ],
            ];
            return $this->resultPagingTasks;
        }
        return [];
    }


    /**
     * @return bool
     * set $this->countOfTasks
     * DB request
     */
    protected function countTasks()
    {
        $sql = "SELECT count(*) FROM " . TABLE_TASKS;
        $res = DB::getInstance()->querySelect($sql, []);

        if ($res[0][0]) {
            $this->countOfTasks = $res[0][0];
            return true;
        }
        return false;
    }


    /**
     * @param $id
     * set this->resultTaskId
     */
    protected function taskId($id)
    {
        $sql = "SELECT * FROM " . TABLE_TASKS . " WHERE id_task=:id_task;";
        $results = DB::getInstance()->querySelect($sql, [':id_task' => $id]);

        if ($results) {
            $result = [];
            foreach ($results[0] as $key => $value) {
                $result[$key] = $value;
            }
            $this->resultTaskId = $result;
        }
    }


    /**
     * @param array $params
     * n start row number,
     * limit => LIMIT
     * field_order_by sort field
     * direction DESC or ASC
     * @return bool
     */
    protected function tasksList($params = [])
    {
        //  field to order by
        $order_by = $params[0] ?? 'id_task';
        $order_by = mb_strtolower($order_by);

        $direction = 'DESC';
        if (mb_strtolower($params[1]) == 'ask') {
            $direction = '';
        }

        $limit = 3;
        if (LIMIT) $limit = LIMIT;

        // offset
        $n = (int)$params[2] ?? 0;

        $sql = "SELECT * FROM `" . TABLE_TASKS . "` WHERE 1 ORDER BY $order_by $direction LIMIT $n,$limit ;";
        $res = DB::getInstance()->querySelect($sql, []);

        if ($res) {
            $this->resultTasksList = $res;
            return true;
        }
        return false;
    }


    /**
     * check form data
     * @return mixed array|bool
     *
     * if not server request - return error Err404Controller
     * if form data error  - return array of errors
     * if ok! - return data ready to save into DB
     */
    public function validate()
    {
        //// http
        // if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        //     header('location: /err404/index') or die ('404 Not Found');
        //     exit;
        // }

        // error messages array
        $errMes = [];
        // valid data messages array
        $data = [];

        if ($_POST['id_task']) $data['id_task'] = $_POST['id_task'];

        // check author field
        $author = htmlspecialchars(strip_tags($_POST['author']));
        if (!$author) $errMes[] = 'The "author" field is not filled in';
        else $data['author'] = $author;

        // check task field
        $task = htmlspecialchars(strip_tags($_POST['task']));
        if (!$task) $errMes[] = 'The "Task" field is not filled in';
        else $data['task'] = $task;

        // check email field
        $mailCheckedPreg = preg_match('/^([a-z0-9_-]+\.*)*[a-z0-9_-]+@[a-z0-9_-]+\.[a-z]{2,6}$/i', $_POST['email']);
        if (!$mailCheckedPreg || !$_POST['email']) {
            $errMes[] = 'Incorrect email';
        } else $data['email'] = $_POST['email'];

        // check task field
        if ($_POST['status']) {
            $data['status'] = 1;
        }

        $this->validData = $data;

        if (!$errMes) {
            return true;
        } else {
            $this->validateErrMess = $errMes;
            return false;
        }
    }


    /**
     * @param $data
     * @return bool
     * and send confirm email
     */
    private function save($data)
    {
        if (!$data) return false;
        $execute_params = [':task' => $data['task'], ':author' => $data['author'], ':email' => $data['email']];

        $sql = "INSERT INTO `" . TABLE_TASKS . "`(`task`, `author`, `email`) VALUES ( :task, :author, :email)";

        $res = DB::getInstance()->querySave($sql, $execute_params);

        if ($res) {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     */
    public function saveConfirmEmailMessage()
    {
        if (!$this->validData) return false;

        $email = $this->validData['email'];
        $emailBody = 'Hello, ' . $this->validData['author'] . "!\r\n" . 'You have a new task!' . "\r\n" . $this->validData['task'];
        $emailSubj = 'Confirmation of task creating';

        if (!$email) return false;

        //$success = $this->sendEmail($email, $emailBody, $emailSubj);

        $mailSender = new Mailer();
        $success = $mailSender->sendEmail($email, $emailBody, $emailSubj);

        // 
        $this->sentEmailConfirmMessage = $success ?
            "Confirm message was sent to yor e-mail!"
            : "Fail to sent the confirm message to yor e-mail!";

        if ($success) return true;
        return false;
    }


    /**
     * @param $data
     * @return bool
     * updating "task" field with use trigger `tasks_after_update_task`
     * DELIMITER $$
     * CREATE TRIGGER `tasks_after_update_task` BEFORE UPDATE ON `tasks` FOR EACH ROW IF NEW.task NOT LIKE OLD.task THEN
     * SET NEW.redacted = 1;
     * END IF
     * $$
     * DELIMITER ;
     */
    private function update($data)
    {
        //echo '<br>' . __LINE__ . ' ' . __FILE__ . '<br>';
        //var_dump($data);
        if (!$data['task'] || !$data['author'] || !$data['email'] || !$data['id_task']) return false;
        $data['status'] = $data['status'] ? 1 : 0;

        $sql = "UPDATE `tasks` SET `task`= :task, `author` = :author, `email`=:email, `status`= :status  WHERE `id_task` = :id";
        $execute_params = [
            ":task" => $data['task'],
            ":author" => $data['author'],
            ":email" => $data['email'],
            ":status" => $data['status'],
            ":id" => (int)$data['id_task']
        ];

        if (DB::getInstance()->querySave($sql, $execute_params)) return true;
        return true;
    }
}