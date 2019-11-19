<?php

use models\Router;
use models\Session;
use models\User;

include_once('../config/config.php');
include_once('../app.php');

User::check();
if (User::isLogged()) {
    $_SESSION['admin_can_do_it'] = 'yes';
}
else {
    Session::destroy();
}

$rout = new Router();
// path of parameters is:
// $controllerName / $methodName/.... param1/param2/.....
// for a example: index/index/  or /index/index/name/desk/6
// $paramName =  param1
// $routParameters = array(param1, param2, ....);

$controllerName = $rout->getControllerName();
$methodName = $rout->getMethodName();
// optional parameter array
$routParameters = $rout->getDataParameters();


if (!class_exists($controllerName)) {
    $controllerName = ERROR_CONTROLLER;
    $methodName = 'index';
}
if (!$methodName) $methodName = 'index';


$controller = new $controllerName();
$controller->$methodName($routParameters);
$data = $controller->getData();

try {

    $loader = new Twig_Loader_Filesystem(PATH_TEMPLATES);

    $twig = new Twig_Environment($loader);
    $twig->addGlobal('session', $_SESSION);

    $template = $twig->loadTemplate($data['page'] . '.tmpl.html');

    echo $template->render(array(
        'data' => $data,
    ));
} catch (Exception $e) {
    // echo 'ERROR: ' . $e->getMessage();
    die ('ERROR: ' . $e->getMessage());
}


