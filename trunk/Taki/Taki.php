<?php
include_once('model.php');
include_once('view.php');
include_once('controller.php');

$model = new Model();
$controller = new Controller($model);
$view = new View($controller, $model);
echo $view->output();