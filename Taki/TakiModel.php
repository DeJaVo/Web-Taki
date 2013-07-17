<?php
include_once('DataBase.php');

class taki_model
{
     private $db;

    /*function create_account($data)
    {
        create_database();

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

    public function taki_model()
    {
        $this->db= new data_base();
    }

    function search_user()
    {
        $str='Sheira';
        echo $this->db->search_user($str);
    }

    function insert_new_player()
    {
        $str='Sheira';
        $this->db->insert_new_player($str,'123456','123456');
    }

}