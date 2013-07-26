<?php
include_once('TakiModel.php');

if(!isset($_SESSION)){ session_start(); }

if (!(isset($_SESSION['username']) && $_SESSION['username'] != '')) {

    header ('URL=../Taki/login.html');
}
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
    <title>Online Taki Statistics Page</title>
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
    //C'tor
    public function stat()
    {
        $this->model=new taki_model();
    }

    //Game stat. results
    public function stat_results($game_id)
    {
        $result= $this->model->tm_search_game_by_game_id($game_id);
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
        //$username=$$_SESSION['username'];
        $result =$this->stat_results('1');
        echo "<table cellspacing='0'>
<tr><th>Game ID</th><th>PlayerA ID</th><th>PlayerB ID</th><th>Highest Number Of Cards A</th>
<th>Highest Number Of Cards B</th><th>Sum of Turns</th><th>Winner</th><th>Game Start Time</th><th>Game Finish Time</th></tr>";
        while($row = mysqli_fetch_array($result))
        {
            echo "<tr>";
            echo "<td>" . $row['game_id'] . "</td>";
            echo "<td>" . $row['playerA_id'] . "</td>";
            echo "<td>" . $row['playerB_id'] . "</td>";
            echo "<td>" . $row['highest_number_of_cards_A'] . "</td>";
            echo "<td>" . $row['highest_number_of_cards_B'] . "</td>";
            echo "<td>" . $row['sum_of_turns'] . "</td>";
            echo "<td>" . $row['winner'] . "</td>";
            echo "<td>" . $row['game_start_time'] . "</td>";
            echo "<td>" . $row['game_finish_time'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        $row = mysqli_fetch_array($result);
        $this->stat_display_user_stat('dvir');
        //$this->stat_results_per_player($row['playerA_id']);
        //$this->stat_results_per_player($row['playerB_id']);
    }

    public function stat_display_user_stat($username)
    {
        $result = $this->stat_results_per_player($username);
        $row = mysqli_fetch_array($result);
        print_r($row);
        echo "<table cellspacing='0'>
<tr><th>UserName</th><th>Num Of Games</th><th>Num Of Wins</th><th>Num Of Loses</th>
<th>Average Num Of Cards Per Game </th>";
        while($row = mysqli_fetch_array($result))
        {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['num_of_games'] . "</td>";
            echo "<td>" . $row['num_of_wins'] . "</td>";
            echo "<td>" . $row['num_of_loses'] . "</td>";
            echo "<td>" . $row['average_num_of_cards_per_game'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

$stat = new stat();
$stat->stat_display();
?>


</body>
</html>