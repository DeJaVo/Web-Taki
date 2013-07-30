<?php
include_once('TakiModel.php');
?>

<!DOCTYPE html>
<html>
<head>
    <!-----Meta----->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>List-of-active-games page</title>
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
    <title>Active Games Page</title>
</div>
<form name="stat-form" class="stat-form">
    <!--Header-->
    <div class="header">
        <h1>Active Games</h1>
    </div>
    <!--END header-->
</form>
    <div id=wrapper>
        <?php
        class active_game
        {
            private $model;

            public function active_game()
            {
                $this->model=new taki_model();
            }

            public function active_game_display()
            {
                $results = $this->model->tm_all_active_games();
                echo "<table cellspacing='0'>
<tr><th>Game ID</th><th>User Name A</th><th>User Name B</th><th>Highest Number Of Cards A</th>
<th>Highest Number Of Cards B</th><th>Turn</th><th>Sum of Turns</th><th>Winner</th><th>Game Start Time</th><th>Game Finish Time</th></tr>";
                foreach ($results as $result) {
                    echo "<tr>";
                    foreach ($result as $key => $val) {
                        echo "<td>" . $val . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        $active = new active_game();
        $active->active_game_display()
        ?>
    </div>
</body>
</html>