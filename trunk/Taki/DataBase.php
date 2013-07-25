<?php
require_once("member_site_config.php");
if(!$config->check_login())
{
    header('URL=http://localhost/Taki/login.php');
}

class data_base
{
    private $key = 'ASKSDFNSDFKEISDJAHDLDSDF1235UUUiidfsdf';
    //Constructor (Creates connection, database and tables
    public function data_base()
    {
        ////////////////////CREATE NEW DB ///////////////////////
        $con=mysqli_connect("","root","");
        // Check connection
        if (mysqli_connect_errno($con))
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
            //TODO:deal with error
        }
        if (!mysqli_select_db($con,'taki_db'))
        {
            echo("<br>creating database!<br>");
            $sql="CREATE DATABASE IF NOT EXISTS taki_db";
            if (mysqli_query($con,$sql))
            {
                echo "Database taki_db created successfully<br>";
            }
            else
            {
                //TODO: deal with error
                echo "Error creating database: taki_db <br>" . mysqli_error($con)."<br>";
            }

            /////////////////////Create Tables////////////////////
            //create Games Table
            //TODO: when game finishes we need to update game finish time
            $con=mysqli_connect("","root","","taki_db");
            // Check connection
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
            }
            // Create table
            $sql="CREATE TABLE IF NOT EXISTS games
        (
            game_id  INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(game_id),
            playerA_id INT NOT NULL,
            playerB_id INT NOT NULL,
            cards_A TEXT,highest_number_of_cards_A INT DEFAULT '0', cards_B TEXT,highest_number_of_cards_B INT DEFAULT '0',
            last_open_card TEXT , closed_cards TEXT , turn INT DEFAULT '0', sum_of_turns INT DEFAULT '0',winner INT DEFAULT '0',
            game_start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, game_finish_time TIMESTAMP,sequential_two INT DEFAULT '0'
        )";
            if (mysqli_query($con,$sql))
            {
                echo "table Games created successfully<br>";
            }
            else
            {
                echo "Error creating table: Games <br>" . mysqli_error($con);
                //TODO: Deal with error
            }

            //create Players Table
            //TODO:update average after each game
            $sql="CREATE TABLE IF NOT EXISTS players
	    (
            username VARCHAR(200) NOT NULL,
            PRIMARY KEY(username),user_password BLOB,
            nick_name TEXT, num_of_games INT DEFAULT '0', num_of_wins INT DEFAULT '0',
            num_of_loses INT DEFAULT '0',average_num_of_cards_per_game DOUBLE DEFAULT '0.0'
	    )";
            if (mysqli_query($con,$sql))
            {
                echo "table Players created successfully<br>";
            }
            else
            {
                echo "Error creating table: Players<br>" . mysqli_error($con)."<br>";
                //TODO:deal with error
            }
            $sql="CREATE TABLE IF NOT EXISTS room
	    (
             username VARCHAR(200) NOT NULL,PRIMARY KEY(username), nick_name TEXT
	    )";
            if (mysqli_query($con,$sql))
            {
                echo "table Room created successfully<br>";
            }
            else
            {
                echo "Error creating table: Room<br>" . mysqli_error($con)."<br>";
                //TODO:deal with error
            }

        }
        //close the connection
        mysqli_close($con);
        return;
    }

    //Insert New Player + encrypt password
    public function db_insert_new_player($username, $password, $nickname) {
        $con=mysqli_connect("","root","","taki_db");
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error()."<br>";
        }
        mysqli_query($con,"INSERT INTO players (username, user_password, nick_name) VALUES ('$username', AES_ENCRYPT('$password','$this->key'), '$nickname')");
        mysqli_close($con);
    }

    // Search User
    public function db_search_user_by_username($user)
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result = mysqli_query($con,"SELECT *,AES_DECRYPT(user_password,'$this->key') AS pass_decrypt FROM players WHERE username ='$user'");
        $row = mysqli_fetch_array($result);
        $result = array();
        if(is_array($row))
        {
            foreach($row as $k => $v) {
                array_push($result, $k, $v);
            }
        }
        mysqli_close($con);
        return $result;
    }

    //Check User
    public function db_find_user_by_params($username,$password,$nickname)
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result = mysqli_query($con,"SELECT * FROM players WHERE username ='$username'AND AES_DECRYPT(user_password,'$this->key')='$password' AND nick_name='$nickname'");
        $row = mysqli_fetch_array($result);
        if(empty($row))
        {
            mysqli_close($con);
            return false;
        }
        mysqli_close($con);
        return true;
    }

    //Insert new game
    //TODO: calculate sum of turns and game start time and finish time
    public function db_insert_new_game($player_a,$player_b,$cardsA,$highest_num_of_cards_a,$cardsB,$highest_num_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner)
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        mysqli_query($con,"INSERT INTO games ( playerA_id,playerB_id,cards_A,highest_number_of_cards_A,cards_B, highest_number_of_cards_B,last_open_card,closed_cards,turn,sum_of_turns,winner) VALUES ('$player_a','$player_b','$cardsA','$highest_num_of_cards_a','$cardsB','$highest_num_cards_b','$last_open_card','$closed_cards','$turn','$sum_of_turns','$winner')");
        mysqli_close($con);
    }

    //Update Player record
    public function db_update_player($username,$num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game) {
        $con=mysqli_connect("","root","","taki_db");
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }

        mysqli_query($con,"UPDATE players SET num_of_games='$num_of_games', num_of_wins='$num_of_wins', num_of_loses='$num_of_loses',average_num_of_cards_per_game='$average_num_of_cards_per_game' WHERE username='$username'");
        mysqli_close($con);
    }

    //Delete Game Record
    public function db_delete_game_record($game_id)
    {
        $con=mysqli_connect("","root","","taki_db");
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result=mysqli_query($con,"SELECT * FROM games WHERE game_id='$game_id'");
        if(!$result)
        {
            mysqli_query($con,"DELETE FROM games WHERE game_id='$game_id'");
        }
        mysqli_close($con);
    }

    /*    //Find game data by player id
        public function db_search_player_data_in_games_by_player_id($player_id)
        {
            $con=mysqli_connect("","root","","taki_db");
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
            }
            $result=mysqli_query($con,"SELECT * FROM games WHERE  (playerA_id!=playerB_id)&&(playerA_id='$player_id' ||  playerB_id='$player_id')");
            $row = mysqli_fetch_array($result);
            $result = array();
            foreach($row as $k => $v) {
                array_push($result, $k, $v);
            }
            mysqli_close($con);
            return $result;
        }*/

    //Find game by game id
    public function db_search_game_by_game_id($game_id)
    {
        $con=mysqli_connect("","root","","taki_db");
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result=mysqli_query($con,"SELECT * FROM games WHERE  game_id='$game_id'");
        $row = mysqli_fetch_array($result);
        $result = array();
        foreach($row as $k => $v) {
            array_push($result, $k, $v);
        }
        mysqli_close($con);
        return (String)$result;
    }


    //Update Game record in games table
    public function db_update_game($game_id,$cards_A,$highest_number_of_cards_A,$highest_number_of_cards_B,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$game_start_time,$game_finish_time,$sequential_two)
    {

        $con=mysqli_connect("","root","","taki_db");
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        mysqli_query($con,"UPDATE games SET cards_A='$cards_A',highest_number_of_cards_A='$highest_number_of_cards_A',highest_number_of_cards_B='$highest_number_of_cards_B',last_open_card='$last_open_card',closed_cards='$closed_cards',turn='$turn',sum_of_turns='$sum_of_turns',winner='$winner',game_start_time='$game_start_time',game_finish_time='$game_finish_time',sequential_two ='$sequential_two ' WHERE game_id='$game_id'");
        mysqli_close($con);
    }


    //Insert new user to waiting room table
    public function db_insert_user_to_room($username)
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result = $this->db_search_user_by_username($username);
        foreach($result as $k =>$v )
        {
            if($k== 'nick_name')
            {
                mysqli_query($con, "INSERT INTO room (username,nick_name) VALUES ('$username', '$v') ");
                return true;
            }
        }
        mysqli_close($con);
        return false;
    }

    //Clear room
    public function db_truncate_room()
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result=mysqli_query($con, "TRUNCATE room");
        if($result)
        {
            mysqli_close($con);
            return true;
        }
        mysqli_close($con);
        return false;
    }

    //Count number of users in waiting room
    public function db_count_number_of_user_in_room()
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
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
            return $num_of_rows;
        }
        mysqli_close($con);
        return -1;
    }
}
