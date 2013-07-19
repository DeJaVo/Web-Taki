<?php
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
        $sql="CREATE DATABASE taki_db";
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
        $sql="CREATE TABLE games
        (
            game_id  INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(game_id),
            playerA_id INT NOT NULL,
            playerB_id INT NOT NULL,
            cards_A TEXT,highest_number_of_cards_A INT DEFAULT '0', cards_B TEXT,highest_number_of_cards_B INT DEFAULT '0',
            last_open_card TEXT , closed_cards TEXT , turn INT DEFAULT '0', sum_of_turns INT DEFAULT '0',winner INT DEFAULT '0',
            game_start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, game_finish_time TIMESTAMP
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
        //TODO: when entering new password use AES_ENCRYPT
        $sql="CREATE TABLE players
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
        mysqli_close($con);
       return array ($row['username'],$row['pass_decrypt'],$row['nick_name']);
    }

    //Check User
    public function db_find_user_by_parms($username,$password,$nickname)
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error()."<br>";
        }
        $result = mysqli_query($con,"SELECT *,AES_DECRYPT(user_password,'$this->key') AS pass_decrypt FROM players WHERE username ='$username'&& pass_decrypt='$password' && nick_name='$nickname'");
        if(!$result)
        {
            mysqli_close($con);
            return false;
        }
        mysqli_close($con);
        return true;
    }
}


