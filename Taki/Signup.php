//create account fields: username password nickname
<?php
include_once('TakiModel.php');

class signup
{
    private $model;
    public function signup($model)
    {
        $this->model=$model;
    }

    function signup_insert_new_user()
    {
        $min_length = 5;
        $username = mysql_real_escape_string($_POST['username']);
        $password = mysql_real_escape_string($_POST['password']);
        $nickname = mysql_real_escape_string($_POST['nickname']);
        if(!empty($username) && !empty($password) &&!empty($nickname))
        {
            if($this->model->tm_search_user_by_username($username))
            {
                $length = strlen($password);
                if ($length < $min_length )
                {
                    die("Error! Please, Write a longer password (more than 5 chars)");
                }
                $this->model->tm_insert_new_player($username,$password,$nickname);
                //TODO : go to login
                header('Refresh: 5; URL=http://localhost/login.html');
            }
            else
            {
                die("Error! username already exists, please choose a different username");
            }
        }
    }
}

