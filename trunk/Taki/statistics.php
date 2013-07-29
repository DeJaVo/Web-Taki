<?php
include_once('TakiModel.php');

if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<!DOCTYPE html>
<html>
<head>
    <!-----Meta----->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Online Taki Statistics Page</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taki" />
    <meta name="keywords" content="login form, psd, html, css3, tutorial" />
    <meta name="M&M" content="Miki Mook" />
    <!--Stylesheets-->
    <link href="css/stat.css" rel="stylesheet" type="text/css" />
    <link href="fonts/pacifico/stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="heading">
    <title>Statistics Page</title>
</div>
<form name="stat-form" class="stat-form" action="" method="post">
    <!--Header-->

    <div class="header">
        <h1>Statistics Page</h1>
    </div>
    <!--END header-->
</form>

<?php
class stat
{
    private $model;
    private $game_id;
    //C'tor
    public function stat()
    {
        $this->model=new taki_model();
    }

    //Game stat. results
    public function stat_results($username)
    {
        $result= $this->model->tm_search_game_by_user_name($username);
        return $result;
    }

    //Player stat. results
    public function stat_results_per_player($username)
    {
        $result = $this->model->tm_search_user_by_username($username);
        return $result;
    }


    //Display game stat
    public function stat_display()
    {
        $username=$_SESSION['username'];
        $result =$this->stat_results($username);
        $this->game_id=$result['game_id'];
        echo "<div id=wrapper>";
        echo "<table cellspacing='0'>
<tr><th>User Name A</th><th>User Name B</th><th>Highest Number Of Cards A</th>
<th>Highest Number Of Cards B</th><th>Sum of Turns</th><th>Winner</th><th>Game Start Time</th><th>Game Finish Time</th></tr>";
        echo "<tr>";
        echo "<td>" . $result['usernameA'] . "</td>";
        echo "<td>" . $result['usernameB'] . "</td>";
        echo "<td>" . $result['highest_number_of_cards_A'] . "</td>";
        echo "<td>" . $result['highest_number_of_cards_B'] . "</td>";
        echo "<td>" . $result['sum_of_turns'] . "</td>";
        echo "<td>" . $result['winner'] . "</td>";
        echo "<td>" . $result['game_start_time'] . "</td>";
        echo "<td>" . $result['game_finish_time'] . "</td>";
        echo "</tr>";
        echo "</table>";
        $this->stat_display_user_stat($username);
    }

    public function stat_display_user_stat($username)
    {
        $result = $this->stat_results_per_player($username);
        echo "<table cellspacing='0'>
<tr><th>UserName</th><th>Num Of Games</th><th>Num Of Wins</th><th>Num Of Loses</th>
<th>Average Num Of Cards Per Game </th>";
        echo "<tr>";
        echo "<td>" . $result['username'] . "</td>";
        echo "<td>" . $result['num_of_games'] . "</td>";
        echo "<td>" . $result['num_of_wins'] . "</td>";
        echo "<td>" . $result['num_of_loses'] . "</td>";
        echo "<td>" . $result['average_num_of_cards_per_game'] . "</td>";
        echo "</tr>";
        echo "</table>";
        //Delete game record
        $this->model->tm_delete_game_record($this->game_id);
    }
}

$stat = new stat();
$stat->stat_display();
?>

</div>
</body>
</html>