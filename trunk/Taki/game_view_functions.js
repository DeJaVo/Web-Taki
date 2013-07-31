//functions we need:
//on_put_down_cards    //animate the movement only if turn is legal
//on_deck
//on_color
//on_card select/unselect
//build string to server

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

//Activate Colors Menu
function activate_color_menu()
{
    document.getElementById("colors").visibility="visible";
}


//Disable UI elements
function disable_UI()
{
    document.getElementById("deck").disabled=true;
    document.getElementById("open_cards").disabled =true;
    document.getElementsByClassName("card").disabled = true;
    document.getElementById("my_hand").disabled =true;
    document.getElementById("op_hand").disabled =true;
}

//Enable UI elements
function enable_UI()
{
    document.getElementById("deck").disabled=false;
    document.getElementById("open_cards").disabled =false;
    document.getElementsByClassName("card").disabled = false;
    document.getElementById("my_hand").disabled =false;
    document.getElementById("op_hand").disabled =false;
}

//On surrender event
function on_surrender()
{

    load_f("../Taki/Game.php","surrender",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            if(xmlhttp.responseText=='5')
            {
                if(confirm('You Lost'))
                {
                    disable_UI();
                    window.location.href="../Taki/statistics.php";
                }
            }
        }

    });
}

