<?php
const DB_DRIVER = 'mysql';
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'task';
const DB_CHARSET = 'utf8';

const DB_OPTIONS = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
];

// Data base tables
const TABLE_USERS = "users";
const TABLE_TASKS = "tasks";
//  data limit on a page
const LIMIT = 3;

// default data
const SITE_NAME = 'Task list';
const MAIN_PAGE = 'index';
const ERROR_404 = 'err404';

const ERROR_CONTROLLER = '\\controllers\\' . 'Err404Controller';
const PATH_TEMPLATES = '..' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

//Mailer
const MAIL_ADMIN_BCC = 'test@test.ts';
const MAIL_FROM = 'test@test.ts';

$controllerModelClassDefinitions = [
    '\controllers\IndexController' => '\models\Task',
];
