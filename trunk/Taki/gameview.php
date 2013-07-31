<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<!DOCTYPE html>
<link href="css/game.css" rel="stylesheet" type="text/css" />
<html>
<head>
    <title>Taki</title>
</head>
<body>
<div id="header">
</div>
<div id="left">
</div>
<div id="center">
</div>
<div id="footer">
    <div id="my_name">stam katov</div>
    <div id="my_hand"></div>
    <div id="surrender">Surrender :(</div>
</div>
</body>
</html>