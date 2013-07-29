<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>
<!DOCTYPE html>
<html>
<body>
<div style="position: absolute; left: 0; top: 0; right: 0; bottom: 0;">
    <div style="width:100%; height:33%; background-color:lightgreen;"></div>
    <div style="width:100%; height:33%; background-color:green;">
        <div style=" position: relative;top: 15%;left: 33%; width:25%%;height:80%;border:1px solid #000;">
            <img src="TakiImages/back/Back.jpg" alt="back" height="150" width="150""></div>
    </div>
    <div style="width:100%; height:33%; background-color:lightgreen;;"></div>
</div>
</body>
</html>
