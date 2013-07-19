<?php
include_once('TakiModel.php');

class login
{
    private $model;

    public function login($model) {
        $this->model=$model;
    }

    public function search_user()
    {
        if(isset($_POST['submit']))
        {
            $username = mysql_real_escape_string($_POST['username']);
            $password = mysql_real_escape_string($_POST['password']);
            $nickname = mysql_real_escape_string($_POST['nickname']);

            //check to make sure that the username,password and nickname fields are not empty.
            if(!empty($username) && !empty($password) &&!empty($nickname))
            {
                //search user in database
                if($this->model->check_user($username,$password,$nickname))
                {
                    //TODO: go to waiting room
                    return true;
                }
                else
                {
                    printfjs("alert('Error! incorrect information, please enter new details");
                    return false;
                }
            }
            else
            {
                printfjs("alert('Error! please fill the missing fields");
                return false;
            }
        }
        else
        {
           printfjs("alert('Error!");
            return false;
        }
    }

}








/*<!--<html>
<body>
<form action="Controller.php" method="post" class="login">

    Username:&nbsp;<input type="text" name="login" value="<?/* echo $_COOKIE['username']; */?>" /><br />
    Password:&nbsp;<input type="password" name="password" value="<?/* echo $_COOKIE['password']; */?>"/><br />
    Nickname:&nbsp;<input type="nickname" name="nickname" value="<?/* echo $_COOKIE['nickname']; */?>"/><br />

    <input type="submit" value="Login" />

</form>
</body>
</html>-->*/