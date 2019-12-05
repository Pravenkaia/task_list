<?php


namespace models;


/**
 * Class Router
 * @package models
 *
 * path of parameters is:
 * $controllerName / $methodName/.... param1/param2/.....
 * for a example: index/index/1  or /index/index/name/desk/6
 * $paramName  =  param1
 * $routParameters = array(param1, param2, ....);
 *
 */
class  Router
{
    /**
     * @var string
     */
    protected $controllerName = '';

    /**
     * @var
     */
    protected $methodName;

    /**
     * optional parameter array
     * array of other (optional) parameters [ param1 / param2 /param3 ]
     * @var array
     */
    protected $otherDataParameters = [];

    /**
     * inner variable of class
     * array of path parameters  ['controller' => 'index', 'method' => 'index', 'param1' => '...', .....]
     * @var array
     */
    private $pathParamsArray = [];

    /**
     * inner variable of class
     * path from url, for example '/news/25/'
     * @var string
     */
    private $path = '';


    /**
     * Rout constructor
     */
    public function __construct()
    {
        if ($this->checkUrl()) {
            $this->setRoutData();
        } else {
            $this->error404();
        }
    }

    /**
     * check http request  or not
     * check URI
     * set $this->path as friendly URL path
     * @return bool
     */
    private function checkUrl()
    {
        if (php_sapi_name() !== 'cli' && isset($_SERVER) && (isset($_GET) || isset($_POST))) {
            $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $url_path = mb_strtolower($url_path);

            if (preg_match('%\/[\/a-z0-9_-]*$%', $url_path)) {
                $this->path = trim($url_path, '%\*/-*%');
                return true;
            }

        }
        return false;
    }


    /**
     * setting controller name
     * $this->controllerName,
     * method name $this->methodName ,
     * method parameter $this->paramName
     */
    private function setRoutData()
    {
        $this->pathParamsArray = explode("/", $this->path);

        // if empty path or a Number, for a example /  or /12
        // go to IndexController
        if (!$this->pathParamsArray[0] || !$this->pathParamsArray[1]) {
            $this->pathParamsArray[0] = 'index';
            $this->pathParamsArray[1] = 'index';
        }

        // set controller name
        $this->controllerName = ucfirst(mb_strtolower($this->pathParamsArray[0]));

        //set method name
        $this->methodName = mb_strtolower($this->pathParamsArray[1]) ?? 'index';

        // cut off controller name and method name
        // array of other (option) parameters
        $this->otherDataParameters = array_slice($this->pathParamsArray, 2);
    }

    protected function error404()
    {
        $this->pathParamsArray[0] = ERROR_404;
        $this->controllerName = 'Err404';
        $this->methodName = 'index';
    }

    public function getControllerName()
    {
        return '\\controllers\\' . $this->controllerName . 'Controller';
    }

    public function getMethodName()
    {
        return 'action' . ucfirst(mb_strtolower($this->methodName));
    }

    public function getDataParameters()
    {
        return $this->otherDataParameters;
    }


}