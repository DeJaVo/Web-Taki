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
    <div id="op_name"></div>
    <div id="op_hand"></div>
</div>
<div id="center">
    <div id="deck">
    </div>
    <div id="open_cards">
    </div>
    <div id="colors">
        <button id="red"></button>
        <button id="yellow"></button>
        <button id="green"></button>
        <button id="blue"></button>
    </div>
</div>
<div id="footer">
    <div id="my_name">stam katov</div>
    <div id="my_hand"></div>
    <button id="surrender">Surrender :(</button>
</div>
</body>
</html>