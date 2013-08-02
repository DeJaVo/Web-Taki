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
        var xmlhttp;
        function load_f(url,arg,cfunc)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            <!-- create ajax Http request  for older supported browsers-->
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=cfunc;
            xmlhttp.open('POST',url,true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("arg="+arg);
        }
        function print_wr()
        {
            load_f("../Taki/waitingroom.php",1,function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    document.getElementById("table").innerHTML=xmlhttp.responseText;
                    check_start_game();
                }
            });
        }
        function check_start_game()
        {
            load_f("../Taki/waitingroom.php",0,function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    if(xmlhttp.responseText=="OK") {window.location.href="../Taki/gameview.php"; }
                    else if(xmlhttp.responseText=="Stay") {setTimeout(print_wr(),3000);}
                    else if(xmlhttp.responseText=="Error")
                    {
                        alert("Fatal Error! when trying to start new game<br>please login and try again");
                        window.location.href="../Taki/login.html";
                    }
                    else
                    {
                        start_new_game(xmlhttp.responseText);
                    }

                }
            });
        }
        function start_new_game(command)
        {
            load_f("../Taki/Game.php",command,function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    //clearInterval(myVar);
                    //document.getElementById("table").innerHTML=xmlhttp.responseText;
                    window.location.href="../Taki/gameview.php";
                }
            });
        }
    </script>
</head>
<body>
<form name="waiting-form" class="waiting-form">
    <!--Header-->
    <div class="header">
        <h1>Waiting Room Form</h1>
        <span>Please be patience and wait for another user to login</span>
    </div>
    <!--END header-->

    <div class="content">
        <script type="text/javascript" language="JavaScript">
            print_wr();
        </script>
        <div id="table"></div>
    </div>
</form>
</body>
</html>
