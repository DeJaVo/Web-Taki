<?php
include_once('TakiModel.php');
include_once('view.php');
include_once('controller.php');

$model = new TakiModel();
$controller = new Controller($model);
$view = new View($controller, $model);
echo $view->output();