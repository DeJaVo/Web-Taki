<?php
class DataBase
{
    //Create a singleton
    //return DataBase object
    public static function Instance()
    {
        static $inst = null;
        if($inst === null)
        {
            $inst = new DataBase();
        }
        return $inst;
    }

    //Constructor
    private function DataBase()
    {
        ////////////////////CREATE NEW DB ///////////////////////
        $con=mysqli_connect("","admin","admin","");
        // Check connection
        if (mysqli_connect_errno($con))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            //TODO:deal with error
        }

        $sql="CREATE DATABASE taki_db";
        if (mysqli_query($con,$sql))
        {
            echo "Database taki_db created successfully";
        }
        else
        {
            //TODO: deal with error
            echo "Error creating database: taki_db" . mysqli_error($con);
        }

        /////////////////////Create Tables////////////////////
        //create Games Table
        //TODO: when game finishes we need to update game finish time
        $sql="CREATE TABLE Games
        (
            game_id  INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(game_id),
            playerA_id INT NOT NULL,
            playerB_id INT NOT NULL,
            cards_A TEXT,highest_number_of_cards_A INT, cards_B TEXT,highest_number_of_cards_B INT,
            last_open_card TEXT , closed_cards TEXT , turn INT, sum_of_turns INT,winner INT,
            game_start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, game_finish_time TIMESTAMP DEFAULT NULL
        )";
        if (mysqli_query($con,$sql))
        {
            echo "table Games created successfully";
        }
        else
        {
            echo "Error creating table: Games " . mysqli_error($con);
            //TODO: Deal with error
        }

        //create Players Table
        //TODO:update average after each game
        //TODO: when entering new password use AES_ENCRYPT
        $sql="CREATE TABLE Players
	    (
            username TEXT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(username),password BLOB,
            nick_name TEXT, num_of_games INT, num_of_wins INT,
            num_of_loses INT,average_num_of_cards_per_game DOUBLE
	    )";
        if (mysqli_query($con,$sql))
        {
            echo "table Players created successfully";
        }
        else
        {
            echo "Error creating table: Players" . mysqli_error($con);
            //TODO:deal with error
        }
        //close the connection
        mysqli_close($con);
        return;
    }

    public function InsertNewPlayer() {

    }

}

print("hello");
//$data = DataBase::Instance();

