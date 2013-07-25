<!DOCTYPE html>
<html>
<head>
    <!-----Meta----->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Online Taki waiting room</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taki" />
    <meta name="keywords" content="login form, psd, html, css3, tutorial" />
    <meta name="M&M" content="Miki Mook" />
    <!--Stylesheets-->
    <link href="css/room.css" rel="stylesheet" type="text/css" />
    <link href="fonts/pacifico/stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body>

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
echo '<div class="heading">';
echo '<title>Online Taki waiting room</title>';
echo '</div>';
echo '<form name="waiting-form" class="waiting-form" action="" method="post">';
echo' <!--Header-->';
echo'<div class="header">';
echo '<h1>Waiting Room Form</h1>';
echo '<span>Please be patience and wait for another user to login</span>
    </div>
    <!--END header-->';
echo'<div class="content">';
echo'<table cellspacing="0">';
echo '<tr><th>User Name</th><th>Nick Name</th></tr>';
$con=mysqli_connect("","root","","taki_db");
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
}
$result=mysqli_prepare($con, "SELECT * FROM room");
if($result)
{
    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);
    $num_of_rows=mysqli_stmt_num_rows($result);
    mysqli_stmt_close($result);
    mysqli_close($con);
}
else
{
    mysqli_close($con);
}
while($num_of_rows) {
    echo '<tr>';
    foreach($num_of_rows as $key=>$value) {
        echo '<td>',$value,'</td>';
    }
    echo '</tr>';
}
echo '</table>';

echo '</div>';
?>

</form>
</body>
</html>