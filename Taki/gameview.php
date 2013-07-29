<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<!DOCTYPE html>
<link href="css/game.css" rel="stylesheet" type="text/css" />
<link href="fonts/pacifico/stylesheet.css" rel="stylesheet" type="text/css" />
<html>
<body>
<div class="outline">
    <div class="top"></div>
    <div class="middle">
        <div class="deck"></div>
        <div class="last-open"></div>
    </div>
    <div class="bottom">
        <button onclick="surrender()" class="surrender">Surrender</button>
    </div>
</div>
</body>
</html>
