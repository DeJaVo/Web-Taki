//functions we need:
//on_surrender    V
//on_put_down_cards   V
//on_deck   V
//on_color  V
//on_card select/unselect  V
//parse_answer    // if answer is illegal/fatal error/etc...   V
//build string to server
//activate player ui      V
//deactivate player ui     V
//activate color menu      V

//data structures we use:
//1. structure of new game state- game after move- returned from server
//2. current game state
//3. list of cards player selects

//server returns:
//0 - internal error
//1 - illegal move
//2 - ok new-game-status
//3 - change color
//4 - game-ended game-finish-status
//5 - You Lost
//6 - You Win
//7 - Your turn
//8 - Not your turn

var game_end = 0;
var got_input = 0;
var command;
var chosen_cards;
var curr_game;
var new_game;


function on_card_click(card) {
    //if the card is already in the chosen card- remove it, else add it.
    for(var i=0;i<chosen_cards.length;i++) {
        if(card == chosen_cards[i]) {
            //remove card
            chosen_cards.splice(i,1);
            return;
        }
    }
    //add card
    chosen_cards.push(card);
}
function on_put_down_click() {
    //take all chosen cards and prepare a string command to the server
    var cmd= new String;
    cmd= cmd+"put down cards";
    for(var i=0;i<chosen_cards.length;i++) {
        cmd= cmd.concat(chosen_cards[i]);
    }
    got_input=1;
    command= cmd;
}
//game_params is a list of key-val
function draw_board(game_params) {
    //game_state fields: game_id, player_a player_b my_cards opp_num_cards last_open_card sum_of_turns winner game_start_time game_finish_Tine sequential_two
    //compare game_params to curr_game for deciding what should be updated
    if(curr_game['my_cards']!= game_params['my_cards']) {
        var cards_group= intersection3(curr_game['my_cards'],game_params['my_cards']);
        to_be_removed=cards_group[0];
        to_be_added=cards_group[2];
        display_my_hand_cards(to_be_removed,0);
        display_my_hand_cards(to_be_added,1);
    }
    if(curr_game['opp_num_cards']!= game_params['opp_num_cards']) {
        var num_old, num_new;
        if(num_old>num_new){
            //remove opp cards
            display_op_hand_cards(num_old-num_new);
        } else if (num_old<num_new) {
            //add opp cards
            display_op_hand_cards(num_new-num_old);
        }else {};
    }
    if(curr_game['last_open_card']!= game_params['last_open_card']) {
        display_last_opend_card(game_params['last_open_card']);
    }
    if(curr_game['sum_of_turns']!= game_params['sum_of_turns']) {
    }
}
function intersection3(arr1, arr2) {
    var arr1=curr_game['my_cards'];
    var arr2=game_params['my_cards'];
    var right, mid, left;
    for (var i = 0; i < arr2.length; i++) {
        if (arr1.indexOf(arr2[i]) !== -1) {
            mid.push(arr1[i]);
        } else {
            left.push(arr2[i]);
        }
    }
    for(var i = 0; i< arr1.length; i++){
        if(mid.indexOf(arr1[i]) == -1) {
            right.push(arr1[i]);
        }
    }
    var results= new Array();
    return results.push(left,mid,right);
}
function game_start() {
    var game_params=game_get_state();                   //at first we want to get the game start state
    draw_board(game_params);                            //draw for the first time the board
    update_game_object(curr_game,game_params);          //update current game state
    disable_UI();                                       //deactivate all board
    game_loop();                                        //enter loop
}

function game_loop() {
    while (game_end == 0) {
        if(my_turn()) {
            var game_params=game_get_state();               //???set interval for getting the game current status???
            draw_board(game_params);
            update_game_object(curr_game,game_params);
            enable_UI();                                    //deactivate the player's board according to the turn
        }
        while (got_input == 0) {}                              //wait for input
        var answer=send_move_request(command);                 //send request to the server
        switch (answer[0]) {
            case 1: {illegal_move();break;}
            case 2: {
                draw_board(answer[1]);
                update_game_object(curr_game,answer[1]);
                break; }
            case 3: {
                disable_UI();
                visible_color_menu();
                break;}
            case 4: {
                draw_board(answer[1]);
                update_game_object(curr_game,answer[1]);
                disable_UI();
                check_who_wins();
                break;}
        }
        got_input=0;
        //if the server answered OK
        //animate the move saved before
        // save the new state as current state
        //else - alert illegal move - draw gui according to last state- wait for input again.
    }
}
function check_who_wins() {
    post_f("../Taki/Game.php","",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            var num = xmlhttp.responseText
            if(num==5) {
                if(confirm('You Lost'))
                {
                    window.location.href="../Taki/statistics.php";
                }
            }else {
                if(confirm('You Win!'))
                {
                    window.location.href="../Taki/statistics.php";
                }
            }
        }
    });
}

function update_game_object(obj,param_list) {
    for(var i= 0;i<param_list.length;i=i+2)
    {
        var key = param_list[i];
        var val = param_list[i+1];
        obj[key]= val;
    }
}
function send_move_request(move) {
    post_f("../Taki/Game.php",move,function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            var answer= new Array();
            var result= xmlhttp.responseText;
            var num= result.charAt(0);
            answer.push(num);
            if((num==2)||(num==4)) {
                var params=result.slice(2,result.length-1);
                answer.push(params);
            }
            return answer;
        }
    });
}

function my_turn() {
    post_f("../Taki/Game.php","turn check",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //if result == 8 ==> not my turn
            //if result == 7  ==> my turn (result is build of "7 game-params"
            var result = xmlhttp.responseText;
            var num = result.charAt(0);
            if(num=="8") {
                return 0;
            }
            if(num==7) {
                return 1;
            }
        }
    });
}

//parse a str representing the server's answer
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

//gets the last state of the game.
function game_get_state() {
    post_f("../Taki/Game.php","print game",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //get current game params
            return (parse_string(xmlhttp.responseText));
        }
    });
}



var xmlhttp;
function post_f(url,arg,cfunc)
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

    post_f("../Taki/Game.php","surrender",function()
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
            div.setAttribute('onclick',"on_card_click()");
            element.appendChild(div);
        }
    }
    if(action==0)//remove Add cards from my hand
    {
        var removed =0;
        var child = element.getElementsByTagName("div");
        for(var j= 0; j<length; j++)
        {
            for(var k=0; k<child.length;k++)
            {
                var child_title = child[k].getAttribute("title");
                if(cards[j]==child_title)
                {
                    element.removeChild(child[k]);
                    removed++;
                    if(removed==length)
                    {
                        return;
                    }
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

// Return change color (color_name)
// turn on got input
function on_color(color)
{
     got_input=1;
    command= "change color "+color;
}

//return draw cards
function on_deck()
{
    got_input=1;
    command= "draw cards";
}

//Display last open card
function display_last_open(card)
{
    var path = "../Taki/TakiImages/";
    var image = path + card[0] +"/"+card[1]+".jpg";
    var element = document.getElementById("open_cards");
    element.style["background-image"]="url(\'"+image +"\')";
    element.style["background-size"] = "contain";
    element.style["background-repeat"]="no-repeat";
    element.style["background-position"]="center";
}