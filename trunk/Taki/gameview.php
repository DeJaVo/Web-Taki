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
    <div id="deck">
    </div>
    <div id="open_cards">
    </div>
    <div id="colors">
        <button id="red" onclick=""></button>
        <button id="yellow" onclick=""></button>
        <button id="green" onclick=""></button>
        <button id="blue" onclick=""></button>
    </div>
</div>
<div id="footer">
    <div id="my_name">Dvir</div>
    <div id="my_hand">
        <script>
            var array = ["five blue","six red","six red","six red","six red","six red","six red"];
            display_my_hand_cards(array,1);
            var r_array = ["five blue"];
            display_my_hand_cards(r_array,0);
        </script>
    </div>
    <button id="surrender">Surrender :(</button>
</div>
</body>
</html>