<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<!DOCTYPE html>
<link href="css/game.css" rel="stylesheet" type="text/css" />
<html>
<head>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico" >
    <title>Taki</title>
    <script type='text/javascript'src='../Taki/game_view_functions.js'></script>
</head>
<body>
<div id="header">
    <div id="op_name"></div>
    <div id="op_hand">
    </div>
</div>
<div id="center">
    <div id="deck" onclick="on_deck();">
    </div>
    <button id="play" onclick="on_put_down_click();">Play</button>
    <div id="open_cards">
    </div>
    <div id="colors">
        <button id="red" onclick="on_color('red');"></button>
        <button id="yellow" onclick="on_color('yellow');"></button>
        <button id="green" onclick="on_color('green');"></button>
        <button id="blue" onclick="on_color('blue');"></button>
    </div>
</div>
<div id="footer">
    <div id="my_name"></div>
    <div id="my_hand">
    </div>
    <button id="surrender" onclick="on_surrender()">Surrender</button>
</div>
<script>
    game_start();
</script>
</body>
</html>