<?php
include_once('TakiModel.php');

/*
1. if a new user click on submit (without registering first) the server wil throw an informative error and go back to login screen.
2. if user fill in form and click on register , we insert its data in DB and go back to login screen.
3. if user is registered and click on submit , we verify its details in DB and enter to waiting room
 */

class login
{
    private $model;
    private $username;
    private $password;
    private $nickname;

    public function login($model,$username,$password,$nickname) {
        $this->model=$model;
        $this->username=$username;
        $this->password=$password;
        $this->nickname=$nickname;
    }

    //Login
    function login_find_user_by_params()
    {
        //check to make sure that the username,password and nickname fields are not empty.
        if(!empty($this->username) && !empty($this->password) &&!empty($this->nickname))
        {
            //search user in database
            if($this->model->tm_find_user_by_params($this->username,$this->password,$this->nickname))
            {
                if(!isset($_SESSION)){ session_start(); }
                echo("Connecting.... <br>");
                //TODO: think about encrypting username and game_id
                $_SESSION['username'] = $this->username;
                header('Refresh: 5; URL=http:../Taki/waitingroom.html');
            }
            else
            {
                $this->model->tm_handle_error("Error! please register first");
                header('Refresh: 5; URL=../Taki/login.html');
            }
        }
        else
        {
            $this->model->tm_handle_error("Error! please fill the missing fields");
            header('Refresh: 5; URL=../Taki/login.html');
        }
    }

    //Signup
    function signup_insert_new_user()
    {
        $min_length = 5;
        if(!empty($this->username) && !empty($this->password) &&!empty($this->nickname))
        {
            if(!$this->model->tm_search_user_by_username($this->username))
            {
                $length = strlen($this->password);
                if ($length < $min_length )
                {
                    $this->model->tm_handle_error("Error! Please, Write a longer password (minimum 5 chars)");
                    header('Refresh: 5; URL=../Taki/login.html');
                }
                $this->model->tm_insert_new_player($this->username,$this->password,$this->nickname);
                header('Refresh: 5; URL=../Taki/login.html');
            }
            else
            {
                $this->model->tm_handle_error("Error! username already exists, please choose a different username");
                header('Refresh: 5; URL=../Taki/login.html');
            }
        }
    }
}

if(isset($_POST['submit']))
{
    $submit = trim($_POST['submit']);
    $model = new taki_model();
    //TODO: fix use in mysql_real into mysqli_stmt also in DB
    $username = mysql_real_escape_string(trim($_POST['username']));
    $password = mysql_real_escape_string(trim($_POST['password']));
    $nickname = mysql_real_escape_string(trim($_POST['nickname']));
    $error =false;
    if(empty($username))
    {
        $error=true;
        $model->tm_handle_error('UserName is empty, please fill username field');
        header('Refresh: 5;URL=../Taki/login.html');
    }
    if(empty($password))
    {
        $error=true;
        $model->tm_handle_error("Password is empty, please fill password field");
        header('Refresh: 5;URL=../Taki/login.html');
    }
    if(empty($nickname))
    {
        $error=true;
        $model->tm_handle_error("Nickname is empty, please fill nickname field");
        header('Refresh: 5;URL=../Taki/login.html');
    }
    $login = new login($model,$username,$password,$nickname);
    if(!$error)
    {
        switch($submit)
        {
            case 'Submit':
                $login->login_find_user_by_params();
                break;
            case 'Register':
                $login->signup_insert_new_user();
                break;
        }
    }
}

