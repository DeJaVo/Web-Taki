<?php
include_once('TakiModel.php');
include_once('view.php');
include_once('controller.php');

$model = new taki_model();
$controller = new controller($model);
$view = new View($controller, $model);


