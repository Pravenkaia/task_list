<?php

use models\Router;
use models\Session;
use models\User;

include_once('../config/config.php');
include_once('../app.php');

User::check();
if (User::isLogged()) {
    $_SESSION['admin_can_do_it'] = 'yes';
} else {
    Session::destroy();
}

$rout = new Router();

$controllerName = $rout->getControllerName();
$methodName = $rout->getMethodName();
$routParameters = $rout->getDataParameters();


if (!class_exists($controllerName)) {
    $controllerName = ERROR_CONTROLLER;
    $methodName = 'index';
}
if (!$methodName) $methodName = 'index';

if (class_exists($controllerModelClassDefinitions[$controllerName])) $model = new $controllerModelClassDefinitions[$controllerName]();
else $model = new \models\EmptyModel();


$controller = new $controllerName();
$controller->$methodName($model, $routParameters);
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
    die ('ERROR: ' . $e->getMessage());
}


