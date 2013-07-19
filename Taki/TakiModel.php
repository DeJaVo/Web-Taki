<?php
include_once('DataBase.php');

class taki_model
{
     private $db;

    /*function create_account($data)
    {


        if(isset($_POST['submit']))
        {
            $username = mysql_real_escape_string($_POST['username']);
            $password1 = mysql_real_escape_string($_POST['password1']);
            $password2 = mysql_real_escape_string($_POST['password2']);
            $nickname = mysql_real_escape_string($_POST['nickname']);

            //check to make sure that the username and password fields are not empty and that the passwords match
            if(!empty($username) && !empty($password1) && !empty($password2) &&   ($password1 == $password2)&&!empty($nickname))
            {
                //make sure the username does not already exist, create sql query to check username
                $query = "SELECT * FROM Players WHERE username = '$username' ";
                $data=mysql_query($query);
                //check to make sure no username row exists in the data, if data is empty or zero
                if(mysql_num_rows($data) == 0)
                {

                }
                else
                {

                }
            }
            elseif ($password1 != $password2)
            {

            }

            else
            {

            }
        }
    }*/

    //C'tor
    public function taki_model()
    {
        $this->db= new data_base();
    }

    //Search Player in DB
    function search_user($str)
    {
        if($this->db->search_user($str))
        {
            return true;
        }
        return false;
    }

    //Insert New Player
    function insert_new_player()
    {
        $this->db->insert_new_player('Sheira','123456','123456');
    }

    //Check if user exist in DB, its nickname and password are matched
    function check_user($username, $password,$nickname)
    {
        if($this->db->check_user($username,$password,$nickname))
        {
            return true;
        }
        return false;
    }

}