<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<!DOCTYPE html>
<link href="css/game.css" rel="stylesheet" type="text/css" />
<html>
<head>
    <title>Taki</title>
    <script type='text/javascript'src='../Taki/game_view_functions.js'></script>
</head>
<body>
<div id="header">
    <div id="op_name"></div>
    <div id="op_hand">
        <script>
            display_op_hand_cards(5);
            display_op_hand_cards(-3);
        </script>
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
    <div id="my_name">Dvir</div>
    <div id="my_hand">
        <script>
            var array = ["five blue","six red","six red","six red","six red","six red","six red"];
            display_my_hand_cards(array,1);
            var r_array = ["six red"];
            display_my_hand_cards(r_array,0);
        </script>
    </div>
    <button id="surrender">Surrender</button>
</div>
</body>
</html>