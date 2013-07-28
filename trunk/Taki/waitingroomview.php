<?php
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }
?>

<!DOCTYPE html>
<html>
<head>
    <!-----Meta----->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Online Taki waiting room</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taki" />
    <meta name="keywords" content="login form, psd, html, css3, tutorial" />
    <meta name="M&M" content="Miki Mook" />
    <!--Stylesheets-->
    <link href="css/room.css" rel="stylesheet" type="text/css" />
    <link href="fonts/pacifico/stylesheet.css" rel="stylesheet" type="text/css" />
    <script>
        function load_waiting_room()
        {
            var xmlhttp;
            <!-- create ajax Http request  for latest supported browsers-->
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            <!-- create ajax Http request  for older supported browsers-->
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            <!--run function if request is request finished and response is ready and her state is "OK" -->

            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
                }
            }
            xmlhttp.open("POST","../Taki/waitingroom.php",true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.setRequestHeader("Connection", "close");
            xmlhttp.send();

            //document.writeln( xmlhttp.responseText.value);
            if (xmlhttp.responseText=="") {
                //document.writeln("entered first if");
                setTimeout('load_waiting_room()',3000);
            } else if (xmlhttp.responseText=="error") {
                alert("Fatal Error! when trying to start new game<br>please login and try again");
                window.location="../Taki/login.html";
            } else {
                xmlhttp.open("POST","../Taki/Game.php",true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.setRequestHeader("Connection", "close");
                xmlhttp.send("command="+xmlhttp.responseText);
                window.location="../Taki/gameview.php";
            }
        }
    </script>
</head>
<body>
<div class="heading">
    <title>Online Taki waiting room</title>
</div>
<form name="waiting-form" class="waiting-form">

<!--Header-->
<div class="header">
    <h1>Waiting Room Form</h1>
    <span>Please be patience and wait for another user to login</span>
</div>
<!--END header-->
    <!--TODO: Fix hard coded call-->
<div class="content">
    <script type="text/javascript" language="JavaScript">
        load_waiting_room();
    </script>
    <div id="txtHint"></div>
</div>
</form>
</body>
</html>