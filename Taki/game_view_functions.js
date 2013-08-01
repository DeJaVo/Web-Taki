//functions we need:
//on_surrender
//on_put_down_cards    //animate the movement only if turn is legal
//on_deck
//on_color
//on_card select/unselect
//parse_answer    // if answer is illegal/fatal error/etc...                                       V
//build string to server
//activate player ui
//deactivate player ui
//activate color menu


function game_state(param_list)
{
    for(var i= 0;i<param_list.length;i=i+2)
    {
        var key = param_list[i];
        var val = param_list[i+1];
        this[key]= val;
    }
}

//parse a str representing the server's answer
//server returns:
//0 - internal error
//1 - illegal move
//2 - ok new-game-status
//3 - change color
//4 - game-ended game-finish-status
//5 - player lost
//6 - player wins
function parse_string(str) {
    var params_array= new Array();
    var result= str.charAt(0);
    if ((result == 0) ||(result==1) || (result==3) || (result==5) || (result==6)) {return result;}
    var game_state = str.slice(3,(str.length-1));
    var params = game_state.split("&");
    for (var i=0;i<params.length;i++) {
        var param=params[i];
        var key_val= param.split("=");
        var key= key_val[0];
        var val=key_val[1];
        params_array.push(key, val);
    }
    return params_array;
}



var xmlhttp;
// Configures the init() function to be called after the document is loaded.
window.onload = init;
function init()
{
    setInterval(game_loop,33);
}

function game_loop()
{
    //call logic
    //update css
}

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
function visible_color_menu()
{
    document.getElementById("colors").visibility="visible";
}

//Activate Colors Menu
function hide_color_menu()
{
    document.getElementById("colors").visibility="hidden";
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
//Display my cards (removes and adds cards)
function display_my_hand_cards(cards,action)
{
    var path = "../Taki/TakiImages/";
    var element = document.getElementById("my_hand");
    var length = cards.length;
    if(action==1) //Add cards to my hand
    {
        for(var i= 0; i<length; i++)
        {
            var div = document.createElement("div");
            div.className = "card";
            var card_array = cards[i].split(" ");
            var image = path + card_array[0] +"/"+card_array[1]+".jpg";
            div.style["background-image"]="url(\'"+image +"\')";
            div.style["background-size"] = "contain";
            div.style["background-repeat"]="no-repeat";
            div.style["background-position"]="center";
            div.title =card_array[0]+" "+card_array[1];
            element.appendChild(div);
        }
    }
    if(action==0)//remove Add cards from my hand
    {
        var child = element.getElementsByTagName("div");
        for(var j= 0; j<length; j++)
        {
            for(var k=0; k<child.length;k++)
            {
                var child_title = child[k].getAttribute("title");
                if(cards[j]==child_title)
                {
                    element.removeChild(child[k]);
                }
            }
        }
    }
}

//Display opponent cards (removes and adds cards)
function display_op_hand_cards(num_of_cards)
{
    var path = "../Taki/TakiImages/back/Back.jpg";
    var element = document.getElementById("op_hand");
    var count = element.getElementsByTagName("div").length;

    if(num_of_cards<0){

        num_of_cards = Math.abs(num_of_cards);
        if(count>num_of_cards)
        {
            while(num_of_cards)
            {
                var child = element.getElementsByTagName("div");
                element.removeChild(child.item(0));
                num_of_cards--;
            }
        }
    }
    else if(num_of_cards>0)
    {
        for(var i= count; i<count+num_of_cards; i++)
        {
            var div = document.createElement("div");
            div.className = "card";
            div.style["background-image"]="url(\'"+path +"\')";
            div.style["background-size"] = "contain";
            div.style["background-repeat"]="no-repeat";
            div.style["background-position"]="center";
            element.appendChild(div);
        }
    }
}

