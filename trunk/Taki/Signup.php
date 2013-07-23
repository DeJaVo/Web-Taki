<?php
include_once('TakiModel.php');

class signup
{
    //create account fields: username password nickname
    private $model;
    public function signup($model)
    {
        $this->model=$model;
    }

    function signup_insert_new_user()
    {
        if(!isset($_POST['Submit']))
        {
            return false;
        }

        $min_length = 5;
        $username = mysql_real_escape_string($_POST['username']);
        $password = mysql_real_escape_string($_POST['password']);
        $nickname = mysql_real_escape_string($_POST['nickname']);
        if(empty($username))
        {
            $this->signup_handle_error('UserName is empty, please fill username field');
            return false;
        }
        if(empty($password))
        {
            $this->signup_handle_error("Password is empty, please fill password field");
            return false;
        }
        if(empty($nickname))
        {
            $this->signup_handle_error("Nickname is empty, please fill nickname field");
            return false;
        }

        if(!empty($username) && !empty($password) &&!empty($nickname))
        {
            if(!$this->model->tm_search_user_by_username($username))
            {
                $length = strlen($password);
                if ($length < $min_length )
                {
                    $this->signup_handle_error("Error! Please, Write a longer password (more than 5 chars)");
                }
                $this->model->tm_insert_new_player($username,$password,$nickname);
                header('Refresh: 5; URL=http://localhost/Taki/login.html');
            }
            else
            {
                $this->signup_handle_error("Error! username already exists, please choose a different username");
            }
        }
    }
    //Handle_Error
    private function signup_handle_error($message)
    {
        echo "<SCRIPT>
                alert('$message');
            </SCRIPT>";
    }
}

$model = new taki_model();
$signup = new signup($model);
$signup->signup_insert_new_user();