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









?>





<!--
<?/* include("Header.php"); */?>

<head>
    <title>First login PHP Script</title>
    <link rel="stylesheet" type="text/css" href="css/index.css" />
</head>

<BODY>

<div id="content">

    <div id="header">

    </div>

    <div id="nav">

    </div>

    <div id="right">
        <form action="Controller.php" method="post" >
    <span class="fname">
      <label for="firstname">First Name:</label><input name="fname" type="text"/><br />
     </span>
    <span class="lname">
      <label for="lastname">Last Name:</label><input name="lname"  type="text" /><br />
    </span>
    <span class="mail">
      <label for="emailaddress">Email:</label><input name="email" type="text"  /><br />
    </span>
    <span class="uname">
      <label for="username">Username:</label><input name="username"  type="text"/><br />
     </span>
     <span class="pass1">
      <label for="password1">Password:</label><input name="password1"  type="password" /><br />
     </span>
     <span class="pass2">
      <label for="password2">Password (retype):</label><input name="password2"  type="password" /><br />
     </span>
            <input type="submit" value="Sign Up" name="submit" class="submit" />

        </form>

    </div>


</div>

--><?/* include("Footer.php"); */?>