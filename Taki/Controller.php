<?php
class controller
{
    private $model;

    public function controller() {
        $this->model = new taki_model();
        $this->model->insert_new_player();
        $this->model->search_user();
    }

}