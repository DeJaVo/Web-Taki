<?php
include_once('TakiModel.php');
include_once('member_site_config.php');

class login
{
    private $model;
    private $config;

    public function login($model) {
        $this->model=$model;
        $this->config=new member_site_config();
    }

    function login_find_user_by_params()
    {
        $submit = trim($_POST['submit']);
        if(isset($submit))
        {
            $username = mysql_real_escape_string(trim($_POST['username']));
            $password = mysql_real_escape_string(trim($_POST['password']));
            $nickname = mysql_real_escape_string(trim($_POST['nickname']));
            if(empty($username))
            {
                $this->login_handle_error('UserName is empty, please fill username field');
                return false;
            }
            if(empty($password))
            {
                $this->login_handle_error("Password is empty, please fill password field");
                return false;
            }
            if(empty($nickname))
            {
                $this->login_handle_error("Nickname is empty, please fill nickname field");
                return false;
            }

            if(!isset($_SESSION)){ session_start(); }

            //check to make sure that the username,password and nickname fields are not empty.
            if(!empty($username) && !empty($password) &&!empty($nickname))
            {
                //search user in database
                if($this->model->tm_find_user_by_params($username,$password,$nickname))
                {
                    echo("<br> Connecting.... <br>");
                    $_SESSION[$this->config->get_login_session_var()] = $username;
                    //TODO: go to waiting room
                    header('Refresh: 5; URL=http://localhost/Taki/waitingroom.html');
                }
                else
                {
                    $this->login_handle_error("Error! incorrect information, please enter new details");
                }
            }
            else
            {
                $this->login_handle_error("Error! please fill the missing fields");
            }
        }
        else
        {
            $this->login_handle_error("Error!");
        }
    }

    //Handle_Error
    private function login_handle_error($message)
    {
        echo "<SCRIPT>
                alert('$message');
            </SCRIPT>";
    }

}

$model = new taki_model();
$login = new login($model);
$login->login_find_user_by_params();