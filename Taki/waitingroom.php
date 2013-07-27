<?php
include_once('TakiModel.php');

if(!isset($_SESSION)){ session_start(); }

if (!(isset($_SESSION['username']) && $_SESSION['username'] != '')) {

    header ("URL=../Taki/login.html'");
}

class waitingroom
{
    private $model;
    private $list_of_waiting_players = NULL;

    private function check_user ($user_name) {
        foreach ($this->list_of_waiting_players as $key) {
            //list($name, $nick_name) = $pair;
            if ($user_name==$key) return 1;
        }
        return 0;
    }

    public function waitingroom()
    {
        $this->model=new taki_model();
        $this->list_of_waiting_players = array();
    }

    //Start game
    public function wr_start_game()
    {
        //if number of pairs > 1 and session[username] is one of the pairs call to start game.
        $num_of_players = count($this->list_of_waiting_players);
        if($num_of_players<2)
        {
            //dont to anything
        }
        else if ($num_of_players==2) {
            if($this->check_user($_SESSION['username'])) {
                //start game
                $result =$this->model->tm_truncate_room();
                if(!$result)
                {
                    $this->model->tm_handle_error("Fatal Error! when trying to start new game<br>please login and try again");
                    header('Refresh: 5; URL=../Taki/login.html');
                }
                header('Refresh: 5;URL=../Taki/Game.php',true,302);
            }
            else{
                $this->model->tm_handle_error("Fatal Error!<br>please login and try again");
                header('Refresh: 5; URL=../Taki/login.html');
            }

        }

      /*  $result = $this->model->tm_count_number_of_user_in_room();
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
            header('Refresh: 5;URL=../Taki/Game.php');
        }
        else
        {
            $this->model->tm_handle_error("Fatal Error!<br>please login and try again");
            header('Refresh: 5; URL=../Taki/login.html');
        }*/

    }

    public function wr_display()
    {
        $this->list_of_waiting_players =  $this->model->tm_all_users_in_room();

        //print_r($this->list_of_waiting_players);
        //$result =  $this->model->tm_search_user_by_username('dvir');
        echo "<table cellspacing='0'>
<tr><th>User Name</th><th>Nick Name</th></tr>";
        echo "<tr>";
        foreach ($this->list_of_waiting_players as $key ) {
            //list($user_name ,$nick_name)= $pair;

            echo "<td>" . $key . "</td>";
            //echo "<td>" . $key . "</td>";

        }
        echo "</tr>";
        echo "</table>";
    }

}
//$q=$_POST['username'];
$room = new waitingroom();
$room->wr_display();
$room->wr_start_game();

