//data structures we use:
//1. params_array    ->       assoc array structure of new game state- game after move- returned from server
//2. cuur_gamr       ->       current game state
//3. chosen_cards    ->       list of cards player selects

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

//Globals:
var params_array= new Array();
var game_end = 0;
var xmlhttp;
var chosen_cards= new Array();
var curr_game = {'game_id': 0 ,'player_a': null, 'player_b': null ,'my_cards': null, 'opp_num_cards':0,'last_open_card': null, 'turn':-1, 'sum_of_turns': 0, 'winner': 999, 'game_start_time': null, 'game_finish_Time': null, 'sequential_two':0};


//////////////////////////////////
///////Logic game functions///////
/////////////////////////////////

//does: initialize the board, structures
function game_start() {
    game_get_state();                   //at first we want to get the game start state
    draw_board();                       //draw for the first time the board
    update_game_object();               //update current game state
    disable_UI();                       //deactivate all board
    my_turn();                          //starts asking the server if it's the player's turn to play
}

//does: compare params_array to curr_game for deciding what should be updated
function draw_board() {
    var splitted_curr_game;
    if(!(curr_game['my_cards']== params_array['my_cards'])) {
        var splitted_params_array = params_array['my_cards'].split(",");
        if(curr_game['my_cards'] == null){
            splitted_curr_game= new Array();
        } else {
            splitted_curr_game = curr_game['my_cards'].split(",");
        }
        var cards_group= intersection3(splitted_curr_game,splitted_params_array);
        var to_be_removed=cards_group[0];
        var to_be_added=cards_group[2];
        display_my_hand_cards(to_be_removed,0,1);
        display_my_hand_cards(to_be_added,1,0);
    }
    else {
        //illegal move: need to revert D&D changes
        //draw dragged cards in "my_hand" again
        if(curr_game['my_cards'] != null){
            splitted_curr_game = curr_game['my_cards'].split(",");
            display_my_hand_cards(splitted_curr_game,0,0);
            display_my_hand_cards(splitted_curr_game,1,0);
        }
    }
    if(curr_game['opp_num_cards'].toString()!= params_array['opp_num_cards']) {
        var num_old=curr_game['opp_num_cards'];
        var num_new = params_array['opp_num_cards'];
        display_op_hand_cards(num_new-num_old);
    }
    if(curr_game['last_open_card']!= params_array['last_open_card']) {
        display_last_opened_card(params_array['last_open_card']);
    } else {
        display_last_opened_card(curr_game['last_open_card']);
    }
    if(curr_game['sum_of_turns']!= params_array['sum_of_turns']) {
    }
}

//does: proccess the answer sent by server
function server_answer( answer) {
    switch (parseInt(answer)) {
        case 1:
            //illegal move
            alert("Illegal Move, Please Try Again");
            draw_board();
            chosen_cards= new Array();
            break;
        case 2:
            // legal move
            draw_board();
            update_game_object();
            chosen_cards= new Array();
            disable_UI();
            setTimeout(my_turn,3000);
            break;
        case 3:
            // user needs to select a color
            disable_UI();
            visible_color_menu();
            break;
        case 4:
            // game ended
            draw_board();
            update_game_object();
            disable_UI();
            check_who_wins();
            break;
    }
}

//does: update the curr_game structure
function update_game_object() {
    curr_game['game_id']=params_array['game_id'];
    curr_game['player_a']=params_array['player_a'];
    curr_game['player_b']=params_array['player_b'];
    curr_game['my_cards']=params_array['my_cards'];
    curr_game['opp_num_cards']=params_array['opp_num_cards'];
    curr_game['last_open_card']=params_array['last_open_card'];
    curr_game['turn']=params_array['turn'];
    curr_game['sum_of_turns']=params_array['sum_of_turns'];
    curr_game['winner']=params_array['winner'];
    curr_game['game_start_time']=params_array['game_start_time'];
    curr_game['game_finish_time']=params_array['game_finish_time'];
    curr_game['sequential_two']=params_array['sequential_two'];
}

//parse a str representing the server's answer
//game_state fields: game_id, player_a player_b my_cards opp_num_cards last_open_card turn sum_of_turns winner game_start_time game_finish_Tine sequential_two
function parse_string(str) {

    var values= new Array();
    var result= str.charAt(0);
    if ((result == 0) ||(result==1) || (result==3) || (result==5) || (result==6))
    {
        return result;
    }
    var game_state = str.slice(2,(str.length-1));
    var params = game_state.split("&");

    for (var i=0;i<params.length;i++) {
        var param=params[i];
        var key_val= param.split("=");
        var key= key_val[0];
        var val=key_val[1];
        values.push(val);
    }
    params_array['game_id']=parseInt(values[0]);
    params_array['player_a']=values[1];
    params_array['player_b']=values[2];
    params_array['my_cards']=values[3];
    params_array['opp_num_cards']=parseInt(values[4]);
    params_array['last_open_card']=values[5];
    params_array['turn']=parseInt(values[6]);
    params_array['sum_of_turns']=parseInt(values[7]);
    params_array['winner']=parseInt(values[8]);
    params_array['game_start_time']=values[9];
    params_array['game_finish_time']=values[10];
    params_array['sequential_two']=isNaN(parseInt(values[11]))? 0:parseInt(values[11]);
}

////////////////////////////////////
///////////Ajax Functions///////////
////////////////////////////////////

//does:  a general functions for handling posts requests.
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
    //alert("!!!!!");
    xmlhttp.open('POST',url,false);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.onreadystatechange=cfunc;
    xmlhttp.send("arg="+arg);
}

//checks if its the players turn to play.
//if so- we ask for the latest game state, build the updated board, update our data structurs, enabling the UI
//if not- we keep asking the server 
function my_turn() {
    post_f("../Taki/Game.php","turn check",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //if result == 8 ==> not my turn
            //if result == 7  ==> my turn (result is build of "7 game-params"
            var result = xmlhttp.responseText;
            var num = result.charAt(0);
            var result_arr = result.split(" ");
            var my_name= result_arr[1];
            game_get_state();
            draw_board();
            update_game_object();
            if(num=="8") {
                draw_names(my_name,8);
                setTimeout(my_turn,3000);
            }
            if(num=="7") {
                draw_names(my_name,7);
                enable_UI();
            }
        }

    });
}


function check_who_wins() {
    post_f("../Taki/Game.php","",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            var num = xmlhttp.responseText;
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

function send_move_request(move) {
    post_f("../Taki/Game.php",move,function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            var result= xmlhttp.responseText;
            if((result.charAt(0)==2)||(result.charAt(0)==4)) {
                parse_string(result);
            }
            server_answer(result.charAt(0));
        }
    });
}


//gets the last state of the game, and updates the params_array structure.
function game_get_state() {
    post_f("../Taki/Game.php","print game",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //get current game params
            parse_string(xmlhttp.responseText);
        }
    });
}

/////////////////////////////////
/////// display functions////////
/////////////////////////////////

//does: display my cards.
//input: cards- list of cards titles,
//       action- 0-> remove cards, 1-> add cards
//       animate- 0- dont animate movement, 1- animate movement
function display_my_hand_cards(cards,action,animate)
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
            var is_firefox= navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
            if(is_firefox)
            {
                div.style["background"]="url(\'"+image +"\')";
            }
            else
            {
                div.style["background-image"]="url(\'"+image +"\')";
                div.style["background-size"] = "contain";
                div.style["background-repeat"]="no-repeat";
                div.style["background-position"]="center";
            }
            div.title =card_array[0]+" "+card_array[1];
            div.setAttribute('onclick',"on_card_click(\'"+card_array[0]+" "+card_array[1]+"\')");
            div.setAttribute('draggable','true');
            div.setAttribute("ondragstart","on_drag(event)");
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
                    if(animate)
                    {
                        animate_move(child[k]);
                    }
                    else
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
}

//Display opponent cards (removes and adds cards with 'back' images)
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

//Updates the open card according to the given cards
function  display_last_opened_card(card)
{
    var path = "../Taki/TakiImages/";
    var card_array = card.split(" ");
    if((card_array[0]=="king") || (card_array[0]=="change_col")) {card_array[1]="special";}
    var image = path + card_array[0] +"/"+card_array[1]+".jpg";
    var element = document.getElementById("open_cards");
    element.style["background-image"]="url(\'"+image +"\')";
    element.style["background-size"] = "contain";
    element.style["background-repeat"]="no-repeat";
    element.style["background-position"]="center";
    element.setAttribute('ondrop',"on_drop(event)");
    element.setAttribute('ondragover',"allow_drop(event)");
}

/////////////////////////////////
///////Events Functions/////////
////////////////////////////////

//On surrender event- sends a post request to the model with a surrender command.
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

// Sends a post request for changing color
function on_color(color)
{
    var cmd= "change color "+color;
    hide_color_menu();
    send_move_request(cmd);
}

// Sends a post request with a 'draw card' command
function on_deck()
{
    //got_input=1;
    var cmd= "draw cards";
    send_move_request(cmd);
}

//saves the card's title + adds the card to chosen cards list
function on_drag(event)
{
    event.dataTransfer.setData("Text",event.target.title);
    on_card_click(event.target.title);
}

//Specify what shall happen on drop event;
//1. replace image on open cards
//2. remove the card from my hand
function on_drop(event)
{
    var path = "../Taki/TakiImages/";
    var element = document.getElementById("open_cards");
    event.preventDefault();
    var data=event.dataTransfer.getData("Text");
    var card_array = data.split(" ");
    var image = path + card_array[0] +"/"+card_array[1]+".jpg";
    element.style["background-image"]="url(\'"+image +"\')";
    var temp = new Array(data);
    display_my_hand_cards(temp,0,0);
}

function on_card_click(card) {
    //if the card is already in the chosen card- remove it, else add it.
    for(var i=0;i<chosen_cards.length;i++) {
        if(card == chosen_cards[i]) {
            var card_node=find_card_by_title(card);;
            card_node.style.border="";
            //remove card
            chosen_cards.splice(i,1);
            return;
        }
    }
    //add card
    var card_node=find_card_by_title(card);
    card_node.style.border="5px solid yellow";
    chosen_cards.push(card);
}
function on_put_down_click() {
    //take all chosen cards and prepare a string command to the server
    var cmd ="put down cards";
    for(var i=0;i<chosen_cards.length;i++) {
        cmd=cmd.concat(" ");
        cmd= cmd.concat(chosen_cards[i]);
    }
    send_move_request(cmd);
}

//////////////////////////////////////
///////// Animation functions/////////
//////////////////////////////////////

//does: animating a card movment statring from its current position
//when the card arrives it's destination (the last-open-card element) we remove it from 'my-hand' div
//input: card element, numeric value to shift in x axis, numeric value ti shoft in y axis
function animate(card, delta_x, delta_y)
{
    var open_card_result = getPosition(document.getElementById("open_cards"));
    open_card_result=open_card_result.split(",");

    var old_top =card.style.top;
    var old_left = card.style.left;

    var splited__old_left = old_left.split('px');
    var splited_old_top = old_top.split('px');

    old_top=parseInt(splited_old_top[0]);
    old_left=parseInt(splited__old_left[0]);

    var d_top=parseInt(open_card_result[1]);
    var d_left=parseInt(open_card_result[0]);

    var did_moved = 0;

    if (old_left < d_left ) {
        if(old_left+delta_x<=d_left) {
            var new_left= old_left+delta_x+"px";
            card.style.left=new_left;
            did_moved=1;
        }
    } else {
        if(old_left-delta_x>=d_left) {
            var new_left= old_left-delta_x+"px";
            card.style.left=new_left;
            did_moved=1;
        }
    }
    if(old_top-delta_y >= d_top) {
        var new_top= old_top-delta_y+"px";
        card.style.top=new_top;
        did_moved=1;
    }
    if (did_moved == 0) {
        card.style.zIndex="";
        if(document.contains(card))
        {
            card.style.visibility="hidden";
            card.style.position="absolute";
            document.getElementById("my_hand").removeChild(card);
        }
    }
    else
    {
        setTimeout(function(){ animate(card,8, 11); }, 33);
    }
}
// a wrapper to animate
function animate_move (card) {
    var card_result = getPosition(card);
    card_result=card_result.split(",");
    card.style.display="none";
    setTimeout(function(){
        card.style.position="fixed";
        card.style.left= card_result[0]+"px";
        card.style.top=card_result[1]+"px";
        card.style.zIndex='199999999';
        card.style.display="inline";
    }, 500);
    //card.style.position="fixed";
    //card.style.left= card_result[0]+"px";
    //card.style.top=card_result[1]+"px";
    //card.style.zIndex='199999999';
    //card.style.display="inline";
    setTimeout(function(){ animate(card,8, 11); }, 500);

}


////////////////////////////////////
/////////util functions////////////
///////////////////////////////////

//does: compare elements of 2 arrays
//input: 2 arrays
//output: 3 arrays: left-all elems in arr1 that dont exist in arr2
//                  mid- all elems that exist in both arrays
//                  right- all elems in arr2 that dont exist in arr1

function intersection3(arr1, arr2) {
    var tmp = new Array();
    var right=new Array();
    var mid= new Array();
    var left=new Array();
    for (var i = 0; i < arr2.length; i++) {
        var key =arr1.indexOf(arr2[i]);
        if ( key !== -1) {
            mid.push(arr2[i]);
            tmp[key]= 1;
        } else {
            right.push(arr2[i]);
        }
    }
    for(var i = 0; i< arr1.length; i++){
        if(mid.indexOf(arr1[i]) == -1) {
            left.push(arr1[i]);
        }else if (tmp[i]==undefined) {
            left.push(arr1[i]);
        }
    }
    var results= new Array();
    results.push(left,mid,right);
    return results;
}

//does: Finds the real position of an object in the DOM
//input: obj
//output: obj's coords relative to the top left ot the browser
function getPosition(obj){
    var topValue= 0,leftValue= 0;
    while(obj){
        leftValue+= obj.offsetLeft;
        topValue+= obj.offsetTop;
        obj= obj.offsetParent;
    }
    return leftValue + "," + topValue;
}

//Prevent the default handling of the element.
function allow_drop(event)
{
    event.preventDefault();
}


//Activate Colors Menu
function visible_color_menu()
{
    document.getElementById("colors").style.visibility="visible";
}

//Activate Colors Menu
function hide_color_menu()
{
    document.getElementById("colors").style.visibility="hidden";
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

function draw_names(my_name, active) {
    var elem1 =document.getElementById("my_name");
    var elem2 =document.getElementById("op_name");
    var op_name;
    if(my_name == curr_game['player_a']) {
        op_name= curr_game['player_b'];
    } else {
        op_name = curr_game['player_a'];
    }
    elem1.innerHTML=my_name;
    elem2.innerHTML=op_name;
    if (active==7) {
        elem1.style.color="yellow";
        elem2.style.color="whitesmoke";
    }else {
        elem1.style.color="whitesmoke";
        elem2.style.color="yellow";
    }
}

//Find card title
function find_card_by_title(card_title)
{
    var element =document.getElementById("my_hand");
    var childrens = element.children;
    for(var j= 0; j<childrens.length; j++)
    {
        var child_title = childrens[j].getAttribute("title");
        if(child_title==card_title)
        {
            return childrens[j]
        }
    }
}

