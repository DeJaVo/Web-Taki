<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<html>
<body>
<form>
    <h1> Entered Game View PHP</h1>
</form>
</body>
</html>
