<?php
include_once('DataBase.php');

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
        if($this->db->db_search_user_by_username($str))
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
        if($this->db->db_find_user_by_parms($username,$password,$nickname))
        {
            return true;
        }
        return false;
    }

    public function tm_update_player($username, $user_password ,$nick_name, $num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game) {
        $this->db->db_update_player($username, $user_password ,$nick_name, $num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game);
    }

    public function tm_delete_game_record($game_id)
    {
        $this->db->db_delete_game_record($game_id);
    }
}