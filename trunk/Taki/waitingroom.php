<?php
include_once('TakiModel.php');

class waitingroom
{
    private $model;
    public function waitingroom()
    {
        $this->model=new taki_model();
    }

    public function wr_enter_room()
    {
        $result = $this->model->tm_insert_user_to_room($_SESSION['username']);
        if(!$result)
        {
            $this->model->tm_handle_error("Error! entering to waiting room, please login and try again");
            header('Refresh: 5; URL=../Taki/login.html');
        }
    }

    //Start game
    public function wr_start_game()
    {
        $result = $this->model->tm_count_number_of_user_in_room();
        if($result==0)
        {
            $this->wr_enter_room();
        }
        else if($result==1)
        {
            $result =$this->model->tm_truncate_room();
            if(!$result)
            {
                $this->model->tm_handle_error("Fatal Error! when trying to start new game<br>please login and try again");
                header('Refresh: 5; URL=../Taki/login.html');
            }
            //header('Refresh: 5; URL=../Taki/game.php');
        }
        else
        {
            $this->model->tm_handle_error("Fatal Error!<br>please login and try again");
            header('Refresh: 5; URL=../Taki/login.html');
        }

    }

}


$room = new waitingroom();
$room->wr_start_game();
