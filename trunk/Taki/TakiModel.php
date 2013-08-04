<?php
include_once('DataBase.php');

if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }


class taki_model
{
    private $secret_key ="H6XmkH+VWvdD88THCl";

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
        return $result;
    }

    //Search Player by nickname in DB
    public function tm_search_user_by_nickname($str)
    {
        $result=$this->db->db_search_user_by_nickname($str);
        return $result;
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
    public function tm_update_player($username, $num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game) {
        $this->db->db_update_player($username, $num_of_games, $num_of_wins, $num_of_loses,$average_num_of_cards_per_game);

    }

    //Delete game record
    public function tm_delete_game_record($game_id)
    {
        $this->db->db_delete_game_record($game_id);
    }

    //Insert new game record
    public function tm_insert_new_game($player_a,$player_b,$cardsA,$highest_num_of_cards_a,$cardsB,$highest_num_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$sequential_two)
    {
        $this->db->db_insert_new_game($player_a,$player_b,$cardsA,$highest_num_of_cards_a,$cardsB,$highest_num_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$sequential_two);
    }

    //Take card from deck
    public function tm_search_game_by_game_id($game_id)
    {
        return $this->db->db_search_game_by_game_id($game_id);
    }

    public function tm_search_game_by_user_name($user_name)
    {
        return $this->db->db_search_game_by_user_name($user_name);
    }

    //Update game record
    public function tm_update_game($game_id,$cards_a,$highest_number_of_cards_a,$cards_b,$highest_number_of_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$game_start_time,$game_finish_time,$sequential_two)
    {
        $this->db->db_update_game($game_id,$cards_a,$highest_number_of_cards_a,$cards_b,$highest_number_of_cards_b,$last_open_card,$closed_cards,$turn,$sum_of_turns,$winner,$game_start_time,$game_finish_time,$sequential_two);
    }

    //Insert new user to waiting room
    public function tm_insert_user_to_room($username)
    {
        $result = $this->db->db_insert_user_to_room($username);
        if($result)
        {
            return true;
        }
        return false;
    }

    //Clear waiting room
    public function tm_remove_user_from_room($user_name)
    {
        $result = $this->db->db_remove_user_from_room($user_name);
        if($result)
        {
            return true;
        }
        return false;
    }
    //Handle_Error
    public function tm_handle_error($message)
    {
        echo "<SCRIPT>
                alert('$message');
            </SCRIPT>";
    }

    //Return number of rows in waiting room
    public function tm_count_number_of_user_in_room()
    {
        $value=$this->db->db_count_number_of_user_in_room();
        return $value;
    }

    //returns all players waiting in waiting room
    public function tm_all_users_in_room () {
        return $this->db->db_all_users_in_room();
    }

    //Finds admin by parameters
    public function tm_find_admin_by_params($username,$password,$nickname)
    {
        $result=$this->db->db_find_admin_by_params($username,$password,$nickname);
        if($result)
        {
            return true;
        }
        return false;
    }
    //returns all active games
    public function tm_all_active_games(){
        return $this->db->db_all_active_games();
    }

    //Encrypt data in session
    public function set_encrypted($value)
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->secret_key, $value, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    //Decrypt data in session
    public function get_decrypted($value)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->secret_key, base64_decode($value), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

}