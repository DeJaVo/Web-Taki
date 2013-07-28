<?php
include_once('TakiModel.php');

if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }

class waitingroom
{
    private $model;
    private $list_of_waiting_players = NULL;

    private function check_user ($user_name) {
        foreach ($this->list_of_waiting_players as $pair ) {
            list($name, $nick_name) = $pair;
            if ($user_name==$name) return 1;
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
        $users_string= "";
        $num_of_players = count($this->list_of_waiting_players);
        if($num_of_players<2)
        {
            echo "";
        }
        else if ($num_of_players==2) {
            if($this->check_user($_SESSION['username'])) {
                //remove users from waiting room table
                foreach($this->list_of_waiting_players as $pair) {
                    list($user_name, $nick_name)=$pair;
                    $users_string=$users_string." ".$user_name;
                    $success =$this->model->tm_remove_user_from_room($user_name);
                    if (!$success) {
                        //error while removing users from room table
                        echo "Error";
                    }
                }
                echo "start_new_game".$users_string;
            }
        }
    }

    public function wr_display_table()
    {
        $this->list_of_waiting_players =  $this->model->tm_all_users_in_room();
        echo "<table cellspacing='0'>
<tr><th>User Name</th><th>Nick Name</th></tr>";

        foreach ($this->list_of_waiting_players as $pair ) {
            list($username ,$nickname)= $pair;
            echo "<tr>";
            echo "<td>" . $username . "</td>";
            echo "<td>" . $nickname . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

}
$room = new waitingroom();
$room->wr_display_table();
$room->wr_start_game();

