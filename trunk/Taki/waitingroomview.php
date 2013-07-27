<?php
if(!isset($_SESSION)){ session_start(); }

if (!(isset($_SESSION['username']) && $_SESSION['username'] != '')) {

    header ('URL=../Taki/login.html');
}
//$expire=time()+60;
//setcookie("username", $_SESSION['username'], $expire);
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
        /*    <!--Check if str is empty , if it is do not do anything -->
            if (str=="")
            {
               // document.getElementById("txtHint").innerHTML="";
                return;
            }*/
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
            //var length =str.length;
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //xmlhttp.setRequestHeader("Content-length", 'length');
            xmlhttp.setRequestHeader("Connection", "close");
            xmlhttp.send();
            //xmlhttp.send("username="+str);
            //var username = getCookie("username");
            setTimeout('load_waiting_room()',10000);
        }
        /*function getCookie(c_name)
        {
            var c_value = document.cookie;
            var c_start = c_value.indexOf(" " + c_name + "=");
            if (c_start == -1)
            {
                c_start = c_value.indexOf(c_name + "=");
            }
            if (c_start == -1)
            {
                c_value = null;
            }
            else
            {
                c_start = c_value.indexOf("=", c_start) + 1;
                var c_end = c_value.indexOf(";", c_start);
                if (c_end == -1)
                {
                    c_end = c_value.length;
                }
                c_value = unescape(c_value.substring(c_start,c_end));
            }
            return c_value;
        }*/
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
        //var username = getCookie("username");
        load_waiting_room();
    </script>
    <div id="txtHint"></div>
</div>
</form>
</body>
</html>