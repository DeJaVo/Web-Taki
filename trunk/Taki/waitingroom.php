<?php
include_once('TakiModel.php');

if(!isset($_SESSION)){ session_start(); }

if (!(isset($_SESSION['username']) && $_SESSION['username'] != '')) {

    header ("Location: login.html");
}

class waitingroom
{
    private $model;
    public function waitingroom()
    {
        $this->model=new taki_model();
    }

    public function wr_enter_room()
    {
        $result = $this->model->tm_insert_user_to_room('dvir');
        //$result = $this->model->tm_insert_user_to_room($_SESSION['username']);
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
            header('URL=../Taki/game.php');
        }
        else
        {
            $this->model->tm_handle_error("Fatal Error!<br>please login and try again");
            header('Refresh: 5; URL=../Taki/login.html');
        }

    }

    public function wr_display()
    {
        //$q=$$_SESSION['username'];
        $con=mysqli_connect("","root","","taki_db");
// Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        mysqli_select_db($con,"taki_db");
        $sql="SELECT * FROM room  WHERE username = 'dvir'";

        $result = mysqli_query($con,$sql);
        echo "<table cellspacing='0'>
<tr><th>User Name</th><th>Nick Name</th></tr>";
        while($row = mysqli_fetch_array($result))
        {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['nick_name'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

    }

}

$room = new waitingroom();
$room->wr_display();
$room->wr_start_game();

