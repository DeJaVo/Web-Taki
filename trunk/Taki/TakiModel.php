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
    function tm_search_user_by_username($str)
    {
        if($this->db->db_search_user_by_username($str))
        {
            return true;
        }
        return false;
    }

    //Insert New Player
    function tm_insert_new_player($username,$password,$nickname)
    {
        $this->db->db_insert_new_player($username,$password,$nickname);
    }

    //Check if user exist in DB, its nickname and password are matched
    function tm_find_user_by_params($username, $password,$nickname)
    {
        if($this->db->db_find_user_by_parms($username,$password,$nickname))
        {
            return true;
        }
        return false;
    }

}