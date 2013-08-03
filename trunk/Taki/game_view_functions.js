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
var params_array= new Array();
var game_end = 0;
var my_interval = null;
//var got_input = 0;
//var command;
var chosen_cards= new Array();
var curr_game = {'game_id': 0 ,'player_a': null, 'player_b': null ,'my_cards':new Array(), 'opp_num_cards':0,'last_open_card': null, 'turn':-1, 'sum_of_turns': 0, 'winner': 999, 'game_start_time': null, 'game_finish_Time': null, 'sequential_two':0};
//var new_game = new Object();

function server_answer( answer) {
    switch (parseInt(answer)) {
        case 1: //illegal_move();
            chosen_cards= new Array();
            break;
        case 2:
            draw_board();
            update_game_object();
            my_interval=setInterval(my_turn(),3000);
            break;
        case 3:
            disable_UI();
            visible_color_menu();
            break;
        case 4:
            draw_board();
            update_game_object();
            disable_UI();
            check_who_wins();
            break;
    }
}

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
    var cmd ="put down cards ";
    for(var i=0;i<chosen_cards.length;i++) {
        cmd= cmd.concat(chosen_cards[i]);
    }
    //got_input=1;
    //command= cmd;
    var answer=send_move_request(cmd);
    return server_answer(answer);
}
//game_params is a list of key-val
function draw_board() {
    //game_state fields: game_id, player_a player_b my_cards opp_num_cards last_open_card sum_of_turns winner game_start_time game_finish_Tine sequential_two
    //compare game_params to curr_game for deciding what should be updated
    //document.writeln(game_params['my_cards']);

    if(!(curr_game['my_cards']== params_array['my_cards'])) {
        var splitted_params_array = params_array['my_cards'].split(",");
        var cards_group= intersection3(curr_game['my_cards'],splitted_params_array);
        var to_be_removed=cards_group[0];
        var to_be_added=cards_group[2];
        display_my_hand_cards(to_be_removed,0,1);
        display_my_hand_cards(to_be_added,1,0);
    }
    if(curr_game['opp_num_cards'].toString()!= params_array['opp_num_cards']) {
        var num_old=curr_game['opp_num_cards'];
        var num_new = params_array['opp_num_cards'];
        if(num_old>num_new){
            //remove opp cards
            display_op_hand_cards(num_old-num_new);
        } else if (num_old<num_new) {
            //add opp cards
            display_op_hand_cards(num_new-num_old);
        }else {};
    }
    if(curr_game['last_open_card']!= params_array['last_open_card']) {
        display_last_opened_card(params_array['last_open_card']);
    }
    if(curr_game['sum_of_turns']!= params_array['sum_of_turns']) {
    }
}
function intersection3(arr1, arr2) {
    var right=new Array();
    var mid= new Array();
    var left=new Array();
    for (var i = 0; i < arr2.length; i++) {
        if (arr1.indexOf(arr2[i]) !== -1) {
            mid.push(arr1[i]);
        } else {
            right.push(arr2[i]);
        }
    }
    for(var i = 0; i< arr1.length; i++){
        if(mid.indexOf(arr1[i]) == -1) {
            left.push(arr1[i]);
        }
    }
    var results= new Array();
    results.push(left,mid,right);
    return results;
}
function game_start() {
    game_get_state();                   //at first we want to get the game start state
    draw_board();                            //draw for the first time the board
    update_game_object();          //update current game state
    draw_names();
    disable_UI();                                       //deactivate all board
    my_interval=setInterval(my_turn(),5000);                                        //enter loop
}
function draw_names() {
    var my_name = curr_game['player_a'];
    var op_name = curr_game['player_b'];
    document.getElementById("my_name").innerHTML= my_name;
    document.getElementById("op_name").innerHTML= op_name;
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

    /*    for(var i= 0;i<params_array;i=i+2)
     {
     var key = params_array[i];
     var val = params_array[i+1];
     curr_game[key]= val;
     }*/
}
function send_move_request(move) {
    post_f("../Taki/Game.php",move,function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            var result= xmlhttp.responseText;
            if((result.charAt(0)==2)||(result.charAt(0)==4)) {
                var params=result.slice(2,result.length-1);
                parse_string(params);
            }
            return result.charAt(0);
        }
        return -1;

    });
}

function my_turn() {
    clearInterval(my_interval);
    post_f("../Taki/Game.php","turn check",function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //if result == 8 ==> not my turn
            //if result == 7  ==> my turn (result is build of "7 game-params"
            var result = xmlhttp.responseText;
            var num = result.charAt(0);
            if(num=="8") {
                my_interval=setInterval(my_turn(),5000);
                var elem1 =document.getElementById("my_name");
                elem1.style.color="whitesmoke";
                var elem2 =document.getElementById("op_name");
                elem2.style.color="yellow";
            }
            if(num==7) {
                game_get_state();
                draw_board();
                update_game_object();
                enable_UI();
                var elem1 =document.getElementById("my_name");
                elem1.style.color="yellow";
                var elem2 =document.getElementById("op_name");
                elem2.style.color="whitesmoke";
            }
        }

    });
}

//parse a str representing the server's answer
//game_state fields: game_id, player_a player_b my_cards opp_num_cards last_open_card //turn sum_of_turns winner game_start_time game_finish_Tine sequential_two
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
    return params_array;
}

//gets the last state of the game.
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
    xmlhttp.open('POST',url,false);
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
            div.style["background-image"]="url(\'"+image +"\')";
            div.style["background-size"] = "contain";
            div.style["background-repeat"]="no-repeat";
            div.style["background-position"]="center";
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
    //got_input=1;
    var cmd= "change color "+color;
    var answer=send_move_request(cmd);
    return server_answer(answer);
}

//return draw cards
function on_deck()
{
    //got_input=1;
    var cmd= "draw cards";
    var answer=send_move_request(cmd);
    return server_answer(answer);
}

//Display last open card
function  display_last_opened_card(card)
{
    var path = "../Taki/TakiImages/";
    var card_array = card.split(" ");
    var image = path + card_array[0] +"/"+card_array[1]+".jpg";
    var element = document.getElementById("open_cards");
    element.style["background-image"]="url(\'"+image +"\')";
    element.style["background-size"] = "contain";
    element.style["background-repeat"]="no-repeat";
    element.style["background-position"]="center";
    element.setAttribute('ondrop',"on_drop(event)");
    element.setAttribute('ondragover',"allow_drop(event)");
}

//Prevent the default handling of the element.
function allow_drop(event)
{
    event.preventDefault();
}

//Data to be dragged + select the dragged data in chosen cards
function on_drag(event)
{
    event.dataTransfer.setData("Text",event.target.title);
    on_card_click(event.target.title);
}

//Specify what shall happen on drop event;
//1. replace image on  open cards
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

/*function cal_frames_move(card)
{
    //var frameCount = 20;
    var frames = [];//array of coordinates we'll compute
    var points = {
        // moving a box "from" and "to", eg. on the X coordinate
        'fromX': 0,
        'fromY':0,
        'toX': 0,
        'toY':0
    }
    var to = document.getElementById("open_cards");
    var old_left = card.style.left;
    var old_top =  card.style.top;
    var splited_left = old_left.split('px');
    var splited_top = old_top.split('px');
    var to_old_left = to.style.left;
    var to_old_top =  to.style.top;
    var to_splited_left = to_old_left.split('px');
    var to_splited_top = to_old_top.split('px');
    points.fromX=splited_left;
    points.toX= to_splited_left;
    points.fromY=splited_top;
    points.toY=to_splited_top ;
    var animDeltaX = (points.toX - points.fromX); // how far to move
    var animDeltaY = (points.toY - points.fromY); // how far to move
    *//*var tweenAmount = (points.to - points.from)/frameCount;
     for (var i=0; i<frameCount; i++) {
     // calculate the points to animate
     frames[i] = points.from+(tweenAmount*i);
     }*//*
    // animation curve: "sum of numbers" (=100%), slow-fast-slow
    var tweenAmount = [1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1];
    // move from X to Y over frames as defined by tween curve
    var frameCount = tweenAmount.length;
    var newFrameX = points.fromX; // starting coordinate
    var newFrameY = points.fromY; // starting coordinate
    for (var i=0; i<frameCount; i++) {
        // calculate the points to animate
        newFrameX += (animDeltaX*tweenAmount[i]/100);
        newFrameY += (animDeltaY*tweenAmount[i]/100);
        frames[i] = new Array(newFrameX,newFrameY);
    }
    return frames;
}*/

/*
function animate(card)
{
    card.setAttribute('z-index','199999999');
    var frames = cal_frames_move(card);

    for(var i=0;i<frames.length;i++)
    {
        var old_left = card.style.left;
        var old_top =  card.style.top;
        var splited_left = old_left.split('px');
        var splited_top = old_top.split('px');
        var newX = frames[i][0];
        var newY = frames[i][1];
        var new_left = parseInt(splited_left[0]) + newX ;
        var new_top = parseInt(splited_top[0]) + newY;
        card.style.left=new_left+"px";
        card.style.top=new_top+"px";
    }
    card.removeAttribute('z-index');
}*/
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
    if(old_left+delta_x<=d_left) {
        var new_left= old_left+delta_x+"px";
        card.style.left=new_left;
        did_moved=1;
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
            card.style.position="absolute";
            document.getElementById("my_hand").removeChild(card);
        }
    }
    else
    {
        setTimeout(function(){ animate(card,5, 8); }, 33);
    }
}

function animate_move (card) {
    var card_result = getPosition(card);
    card_result=card_result.split(",");
   card.style.visibility="hidden";
    card.style.position="fixed";
    card.style.left= card_result[0]+"px";
    card.style.top=card_result[1]+"px";
    card.style.visibility="visible";

    card.style.zIndex='199999999';
    setTimeout(function(){ animate(card,5, 8); }, 50);

}

function getPosition(obj){
    var topValue= 0,leftValue= 0;
    while(obj){
        leftValue+= obj.offsetLeft;
        topValue+= obj.offsetTop;
        obj= obj.offsetParent;
    }
    return leftValue + "," + topValue;
}
