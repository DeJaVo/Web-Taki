//create account fields: username password nickname
<? include("Taki.php")?>
<?php
$data = $_POST;
$min_length = 5;
if ( $data['username'] ) {
    $username = mysql_real_escape_string($data['username']);
}
if ( $data['password'] ) {
    $pass = mysql_real_escape_string($data['password']);
    $length = strlen($pass);
    if ($length < $min_length )
    {}
        //TODO: deal with this case
}
if ( $data['nickname'] ) {
    $nickname = mysql_real_escape_string($data['nickname']);
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