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

            if(!$this->model->tm_find_admin_by_params($this->username,$this->password,$this->nickname))
            {
                //search user in database
                if($this->model->tm_find_user_by_params($this->username,$this->password,$this->nickname))
                {
                    $array= $this->model->tm_search_game_by_user_name($this->username);
                    if(empty($array['game_id']))
                    {
                        if(!isset($_SESSION)){ session_start(); }
                        echo("Connecting.... <br>");
                        //TODO: html escaping
                        $_SESSION['username'] = $this->model->set_encrypted($this->username);
                        $result = $this->model->tm_insert_user_to_room($this->username);
                        if(!empty($result))
                        {
                            $this->model->tm_handle_error("Error! entering to waiting room, please login and try again");
                            header('Refresh: 1; URL=../Taki/login.html');
                        }
                        header('Refresh: 1; URL=http:../Taki/waitingroomview.php');
                    }
                    else
                    {
                        $this->model->tm_handle_error("Error! user already plays in an active game");
                        header('Refresh: 1; URL=../Taki/login.html');
                    }
                }
                else
                {
                    $this->model->tm_handle_error("Error! please register first");
                    header('Refresh: 1; URL=../Taki/login.html');
                }
            }
            else//Administrator login
            {
                header('Refresh: 1;URL=http:../Taki/activegames.php');
            }
        }
        else
        {
            $this->model->tm_handle_error("Error! please fill the missing fields");
            header('Refresh: 1; URL=../Taki/login.html');
        }
    }

    //Signup
    function signup_insert_new_user()
    {
        $min_length = 5;
        if(!empty($this->username) && !empty($this->password) &&!empty($this->nickname))
        {
            if(($this->username=='admin')&&($this->nickname=='admin'))
            {
                $this->model->tm_handle_error("Error! username or nickname already exists, please choose a different username");
                header('Refresh: 1; URL=../Taki/login.html');
            }
            $result1 = $this->model->tm_search_user_by_username($this->username);
            $result2 =  $this->model->tm_search_user_by_nickname($this->nickname);
            if(empty($result1) && empty($result2))
            {
                $length = strlen($this->password);
                if ($length < $min_length )
                {
                    $this->model->tm_handle_error("Error! Please, Write a longer password (minimum 5 chars)");
                    header('Refresh: 1; URL=../Taki/login.html');
                }
                $this->model->tm_insert_new_player($this->username,$this->password,$this->nickname);
                header('Refresh: 1; URL=../Taki/login.html');
            }
            else
            {
                $this->model->tm_handle_error("Error! username or nickname already exist, please choose a different username");
                header('Refresh: 1; URL=../Taki/login.html');
            }
        }
    }

}

function trim_value(&$value)
{
    $value = trim($value);    // this removes whitespace and related characters from the beginning and end of the string
}


if(isset($_POST['submit']))
{
    $submit = trim($_POST['submit']);
    $model = new taki_model();
    array_filter($_POST,'trim_value');
    $postfilter =
        array(
            'username'                            =>    array('filter' => FILTER_SANITIZE_ENCODED, 'flags' => FILTER_FLAG_STRIP_LOW),
            'password'                            =>    array('filter' => FILTER_SANITIZE_ENCODED, 'flags' => FILTER_FLAG_STRIP_LOW),
            'nickname'                            =>    array('filter' => FILTER_SANITIZE_ENCODED, 'flags' => FILTER_FLAG_STRIP_LOW),
        );
    $revised_post_array = filter_var_array($_POST, $postfilter);
    $sanitized_username=$revised_post_array['username'];
    $sanitized_password=$revised_post_array['password'];
    $sanitized_nickname=$revised_post_array['nickname'];
    //TODO: fix use in mysql_real into mysqli_stmt also in DB
    $username = mysql_real_escape_string(htmlentities($sanitized_username,ENT_COMPAT | ENT_HTML401,"UTF-8"));
    $password = mysql_real_escape_string(htmlentities($sanitized_password,ENT_COMPAT | ENT_HTML401,"UTF-8"));
    $nickname = mysql_real_escape_string( htmlentities($sanitized_nickname,ENT_COMPAT | ENT_HTML401,"UTF-8"));
    $error =false;
    if(empty($username))
    {
        $error=true;
        $model->tm_handle_error('UserName is empty, please fill username field');
        header('Refresh: 1;URL=../Taki/login.html');
    }
    if(empty($password))
    {
        $error=true;
        $model->tm_handle_error("Password is empty, please fill password field");
        header('Refresh: 1;URL=../Taki/login.html');
    }
    if(empty($nickname))
    {
        $error=true;
        $model->tm_handle_error("Nickname is empty, please fill nickname field");
        header('Refresh: 1;URL=../Taki/login.html');
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

