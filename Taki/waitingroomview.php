<?php
if(!isset($_SESSION)){ session_start(); }

if (!(isset($_SESSION['username']) && $_SESSION['username'] != '')) {

    header ('URL=../Taki/login.html');
}
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
        function load_waiting_room(str)
        {
            var xmlhttp;
            <!-- username is empty , don't do anything-->
            if (str=="")
            {
                document.getElementById("txtHint").innerHTML="";
                return;
            }
            <!-- Create HTTP request for ajax. -->
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
                }
            }
            xmlhttp.open("POST","../Taki/waitingroom.php?q="+str,true);
            xmlhttp.send();
            setTimeout('load_waiting_room("mook")',10000);
        }

    </script>
</head>
<body>
<div class="heading">
    <title>Online Taki waiting room</title>
</div>
<form name="waiting-form" class="waiting-form" action="" method="post">
<!--Header-->
<div class="header">
    <h1>Waiting Room Form</h1>
    <span>Please be patience and wait for another user to login</span>
</div>
<!--END header-->
<div class="content">
    <script type="text/javascript" language="JavaScript">
        <!--Hard coded searching Dvir -->
        load_waiting_room('dvir');
    </script>
    <div id="txtHint"></div>
</div>
</form>
</body>
</html>