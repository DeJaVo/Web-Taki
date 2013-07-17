<?php
class data_base
{
    //Constructor
    public function data_base()
    {
        ////////////////////CREATE NEW DB ///////////////////////
        $con=mysqli_connect("","root","");
        // Check connection
        if (mysqli_connect_errno($con))
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error();
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
            echo "Error creating database: taki_db <br>" . mysqli_error($con);
        }

        /////////////////////Create Tables////////////////////
        //create Games Table
        //TODO: when game finishes we need to update game finish time
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error();
        }
        // Create table
        $sql="CREATE TABLE games
        (
            game_id  INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(game_id),
            playerA_id INT NOT NULL,
            playerB_id INT NOT NULL,
            cards_A TEXT,highest_number_of_cards_A INT, cards_B TEXT,highest_number_of_cards_B INT,
            last_open_card TEXT , closed_cards TEXT , turn INT, sum_of_turns INT,winner INT,
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
            PRIMARY KEY(username),password BLOB,
            nick_name TEXT, num_of_games INT, num_of_wins INT,
            num_of_loses INT,average_num_of_cards_per_game DOUBLE
	    )";
        if (mysqli_query($con,$sql))
        {
            echo "table Players created successfully<br>";
        }
        else
        {
            echo "Error creating table: Players<br>" . mysqli_error($con);
            //TODO:deal with error
        }
        //close the connection
        mysqli_close($con);
        return;
    }

    public function insert_new_player($username, $password, $nickname) {
        $con=mysqli_connect("","root","","taki_db");
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        mysqli_query($con,"INSERT INTO players (username, password, nickname) VALUES ('$username', '$password', '$nickname')");
        mysqli_close($con);
    }
    public function search_user($user)
    {
        $con=mysqli_connect("","root","","taki_db");
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: <br>" . mysqli_connect_error();
        }
        $result = mysqli_query($con,"SELECT * FROM players WHERE 'username'='$user'");
        $row = mysqli_fetch_array($result);
        mysqli_close($con);
       return array ($row['username'],$row['password'],$row['nickname']);
    }
}

