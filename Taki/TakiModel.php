<?php
include_once('DataBase.php');

if(!isset($_SESSION)){ session_start(); }


class taki_model
{
    private $db;
    //C'tor
    public function taki_model()
    {
        $this->db= new data_base();
    }

    //Search Player in DB
    public function tm_search_user_by_username($str)
    {
        $result=$this->db->db_search_user_by_username($str);
        if($result)
        {
            return true;
        }
        return false;
    }

    //Insert New Player
    public function tm_insert_new_player($username,$password,$nickname)
    {
        $this->db->db_insert_new_player($username,$password,$nickname);
    }

    //Check if user exist in DB, its nickname and password are matched
    public function tm_find_user_by_params($username, $password,$nickname)
    {
        $result = $this->db->db_find_user_by_params($username,$password,$nickname);
        if($result)
        {
            return true;
        }
        return false;
    }

    //Update player info
    public function tm_update_player($username, $user_password ,$nick_name, $num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game) {
        $this->db->db_update_player($username, $user_password ,$nick_name, $num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game);
    }

    //Delete game record
    public function tm_delete_game_record($game_id)
    {
        $this->db->db_delete_game_record($game_id);
    }

    //Insert new game record
    public function tm_insert_new_game($player_a,$player_b,$cardsA,$highest_num_of_cards_a,$cardsB,$highest_num_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner)
    {
        $this->db->db_insert_new_game($player_a,$player_b,$cardsA,$highest_num_of_cards_a,$cardsB,$highest_num_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner);
    }

    //Take card from deck
    public function tm_search_game_by_game_id($game_id)
    {
        $this->db->db_search_game_by_game_id($game_id);
    }

    //Update game record
    public function tm_update_game($game_id,$cards_A,$highest_number_of_cards_A,$highest_number_of_cards_B,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$game_start_time,$game_finish_time,$sequential_two)
    {
        $this->db->db_update_game($game_id,$cards_A,$highest_number_of_cards_A,$highest_number_of_cards_B,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$game_start_time,$game_finish_time,$sequential_two);
    }
}