<html>
<body>
<form action="Controller.php" method="post" class="login">

    Username:&nbsp;<input type="text" name="login" value="<? echo $_COOKIE['username']; ?>" /><br />
    Password:&nbsp;<input type="password" name="password" value="<? echo $_COOKIE['password']; ?>"/><br />
    Nickname:&nbsp;<input type="nickname" name="nickname" value="<? echo $_COOKIE['nickname']; ?>"/><br />

    <input type="submit" value="Login" />

</form>
</body>
</html>