<?php
class controller
{
    private $model;

    public function controller($model) {
        $this->model=$model;
        $this->model->insert_new_player();
        $this->model->search_user();
    }

}