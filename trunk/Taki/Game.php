<?php

include_once('TakiModel.php');
include_once('card.php');
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }

class game {

    private $model;
    private $game_id = 0 ;
    private $player_a = NULL;
    private $player_b = NULL;
    private $cards_a = NULL;                                         //list of a's cards
    private $cards_b = NULL;                                            // list of b's cards
    private $highest_num_cards_a = 0;
    private $highest_num_cards_b = 0;
    private $last_open_card = NULL;
    private $closed_cards = NULL;                                       //list of closed cards
    private $turn = NULL;                                               // whose turn is it
    private $sum_of_turns = 0;
    private $winner = NULL;                                             // by id/username
    private $game_start_time = NULL;
    private $game_finish_time = NULL;
    private $all_cards = NULL;
    private $sequential_two = 0;                                        //number of two's in a row
    private $command = NULL;


    private function game_get_cards_data ($card_str) {
        list($sign,$col)=explode(" ",$card_str,2);
        return array($sign, $col);
    }               //gets a string of card path and split it into sections, to understand it's color and sign.
    private function change_turn () {
        if ($this->turn==1) {
            $this->turn=0;
        } else {$this->turn=1;}
        return;
    }
    private function incr_turns_count () {$this->sum_of_turns++;}
    private function update_db() {
        $this->model->tm_update_game($this->game_id,implode(",",$this->cards_a), $this->highest_num_cards_a, implode(",",$this->cards_b), $this->highest_num_cards_b,$this->last_open_card, implode(",",$this->closed_cards),$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time, $this->game_finish_time, $this->sequential_two);
    }
    private function swap_cards(){
        $temp=$this->cards_a;
        $this->cards_a=$this->cards_b;
        $this->cards_b=$temp;
        return;
    }
    private function remove_cards($player_id, $cards) {
        foreach ($cards as $card) {
            if($this->turn == 0) {
                unset($this->cards_a[array_search($card, $this->cards_a)]);
                $this->last_open_card=$card;
            } else {
                unset($this->cards_b[array_search($card, $this->cards_b)]);
                $this->last_open_card=$card;
            }
        }
    }
    private function check_taki($cards) {
        $last_sign = NULL;
        $color_was_changed = 0;
        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);

        //if last open card color is different than this taki card color then turn is not legal.
        if ($l_col!= $col) {return 0;}

        foreach ($cards as $card) {
            list($c_sign,$c_col)=$this->game_get_cards_data($card);
            if(($c_col == $col)&& !$color_was_changed) {
                $last_sign=$c_sign;
                continue;
            } elseif ($last_sign== $c_sign) {
                    $color_was_changed=1;
                    continue;
            } else {
                return 0;

            }
        }
        return 1;
    }
    private function check_change_cards($cards) {
        //check that there are no other cards after the swap_card card
        if(count($cards)>1) {return 0;}
        return 1;
    }
    private function check_change_dir ($cards) {
        //check that there are no other cards after the change_dir card
        if(count($cards)>1) {return 0;}

        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        //if last open card color is different than this card color then turn is not legal.
        if ($l_col!= $col) {return 0;}

        return 1;
    }
    private function check_change_col($cards) {
        //check that there are no other cards after this card
        if(count($cards)>1) {return 0;}
        return 1;
    }
    private function check_stop($cards) {
        //check that there are no other cards after the change_dir card
        if(count($cards)>1) {return 0;}

        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        //if last open card color is different than this card color then turn is not legal.
        if ($l_col!= $col) {return 0;}

        return 1;
    }
    private function check_plus($player_id,$cards) {
        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        //if last open card color is different than this card color then turn is not legal.
        if ($l_col!= $col) {return 0;}

            if (count($cards)==1) {
                //TODO: Force player to take a card from deck. do it automatically, server side, without asking the player to do it.
                //TODO: animate the card pulling from the deck.
            } else {
                list($c_sign,$c_col)=$this->game_get_cards_data($cards[1]);
                if((($c_sign>=1) && ($c_sign<=9)) && (count($cards)==2)) {
                    return 1;
                }
                if($c_sign=='king') {
                    if ($this->game_put_down_cards($player_id,array_slice($cards,1))) {
                        $this->change_turn();
                        return 1;
                    }
                }
                if($c_sign=='taki') {
                    if ($this->check_taki(array_slice($cards,1))) {
                        $this->change_turn();
                        return 1;
                    }
                }
                if($c_sign=='change_cards') {
                    if($this->check_change_cards(array_slice($cards,1))) {
                        $this->swap_cards();
                        $this->change_turn();
                        return 1;
                    }
                }
                if($c_sign=='change_dir') {
                    if($this->check_change_dir(array_slice($cards,1))) {
                        $this->change_turn();
                        return 1;
                    }
                }
                if($c_sign=='stop') {
                    if($this->check_stop(array_slice($cards,1))) {
                     return 1;
                    }
                }
                if($c_sign=='plus') {
                    return $this->check_plus($player_id,array_slice($cards,1));
                }
            }
    }
    private function check_number($cards) {
        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        //if last open card color is different than this card color then turn is not legal.
        if ($l_col!= $col) {return 0;}

        //if player selected more than one card its illegal.
        if(count($cards)>1) {return 0;}
    }
    private function game_init_all_cards() {
        //creating all regular cards
        $i =1;
        for(;$i<=8;$i++){
            foreach(array('red', 'blue', 'yellow', 'green') as $color) {
                $card = new card($i, $color);
                array_push($this->all_cards, $card->__get('pic'));
            }
        }
        //creating "+" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('plus', $color);
            array_push($this->all_cards, $card->__get('pic'));
        }
        //creating "<==>" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('change_dir', $color);
            array_push($this->all_cards, $card->__get('pic'));
        }
        //creating "stop" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('stop', $color);
            array_push($this->all_cards, $card->__get('pic'));
        }
        //creating "change col" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('change_col','special');
            array_push($this->all_cards, $card->__get('pic'));
        }
        //creating TAKI cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('taki', $color);
            array_push($this->all_cards, $card->__get('pic'));
        }
        //creating "change cards" cards
        foreach(array('red', 'blue') as $color) {
            $card = new card('change_cards', $color);
            array_push($this->all_cards, $card->__get('pic'));
        }
        foreach(array('red', 'blue') as $color) {
            $card = new card('king','special');
            array_push($this->all_cards, $card->__get('pic'));
        }
    }                        //Initialize all cards

    public function game($model,$user_name) {
        $this->model = $model;
        $this->all_cards = array();
        $this->cards_a = array();
        $this->cards_b = array();
        $this->closed_cards= array();
        $game_data=$this->model->tm_search_game_by_user_name($user_name);
        if(!empty($game_data)) {
            $game_data['cards_A']=explode(",",$game_data['cards_A']);
            $game_data['cards_B']=explode(",",$game_data['cards_B']);
            $game_data['closed_cards']=explode(",",$game_data['closed_cards']);
        list($this->game_id,$this->cards_a,$this->highest_number_of_cards_a,$this->cards_b,$this->highest_number_of_cards_b,$this->last_open_card,$this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time,$this->game_finish_time,$this->sequential_two)=$game_data;
        }
            return;
    }                       //C'tor
    public function game_starts ($player_a, $player_b) {
        $a_cards = array();
        $b_cards = array();
        $this->player_a = $player_a;
        $this->player_b = $player_b;


        //initialize all cards
        $this->game_init_all_cards();
        //initialize a's cards
        foreach (array_rand($this->all_cards, 8) as $k) {
            $a_card = $this->all_cards[$k];
            $this->cards_a[]= $a_card;
            $a_cards[]=$a_card;
        }
        $t_cards = array_diff($this->all_cards, $a_cards);
        //initialize b's cards
        foreach (array_rand($t_cards, 8) as $k) {
            $b_card = $this->all_cards[$k];
            $this->cards_b[] =$b_card;
            $b_cards[]=$b_card;
        }
        //initialize closed cards
        $this->closed_cards= array_diff($t_cards, $b_cards);

        //initialize first open card randomly
        $key=array_rand($this->closed_cards, 1);
        $first_card=$this->closed_cards[$key];
        $this->last_open_card=$first_card;
        unset($this->closed_cards[$key]);

        //define who gets first turn
        $this->turn = rand(0,1);

        //initialize start time
        $this->game_start_time = date("d:m:y h:i:s ");
        // insert new game to DB
        $this->model->tm_insert_new_game($this->player_a,$this->player_b,implode(",",$this->cards_a),$this->highest_num_cards_a,implode(",",$this->cards_b),$this->highest_num_cards_b,$this->last_open_card,implode(",",$this->closed_cards),$this->turn,$this->sum_of_turns,$this->winner, $this->sequential_two);
        return 1;
    }            //initialize a new game, and insert it into DB.
    public function game_ends () {
        //update each player's record
        $a_data =$this->model->tm_search_user_by_username($this->player_a);
        list($username,$user_password,$nick_name,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game) = $a_data;
        if($this->player_a==$this->winner) {
            $num_of_wins++;
        }else {
            $num_of_loses++;
        }
        $average_num_of_cards_per_game= ($this->highest_num_cards_a+($average_num_of_cards_per_game* $num_of_games))/($num_of_games + 1);
        $num_of_games++;
        $this->model->tm_update_player($username, $user_password, $nick_name,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game );

        $b_data =$this->model->tm_search_user_by_username($this->player_b);
        list($username,$user_password,$nick_name,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game) = $b_data;
        if($this->player_b==$this->winner) {
            $num_of_wins++;
        }else {
            $num_of_loses++;
        }
        $average_num_of_cards_per_game= ($this->highest_num_cards_b+($average_num_of_cards_per_game*$num_of_games))/($num_of_games + 1);
        $num_of_games++;
        $this->model->tm_update_player($username,$user_password,$nick_name,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game);

        $this->game_start_time = date("d:m:y h:i:s ");
    }                                  //Update players record when game ends

    public function game_draw_cards() {
        $taken_cards = array();
        //if last open card was plus-2 card- check how many twos were piled up.
        //else take only one card
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        if ($l_sign==2) {
            $num_of_cards= $this->sequential_two*2;
            $this->sequential_two=0;
        } else {
            $num_of_cards=1;
        }
        if($this->turn == 0) {
            $players_cards = $this->cards_a;
            $count = sizeof($this->cards_a)+ $num_of_cards;
            if($count > $this->highest_num_cards_a) {$this->highest_num_cards_a= $count;}
            $this->turn=1;
        } else {
            $players_cards = $this->cards_b;
            $count = sizeof($this->cards_b)+ $num_of_cards;
            if($count > $this->highest_num_cards_b) {$this->highest_num_cards_b= $count;}
            $this->turn=0;
        }
        foreach (array_rand($this->closed_cards, $num_of_cards) as $k) {
            $card = $this->$this->closed_cards[$k];
            $players_cards[]= $card;
            $taken_cards[]=$card;
            $this->turn=0;
        }
        $this->sum_of_turns++;
        $this->closed_cards = array_diff($this->closed_cards, $taken_cards);
        $this->model->tm_update_game($this->game_id,$this->cards_a, $this->highest_num_cards_a, $this->cards_b, $this->highest_num_cards_b,$this->last_open_card, $this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time, $this->game_finish_time);
        return 1;
    }

    public function game_return_game_data() {
        $my_cards = array();
        if($_SESSION['username']==$this->player_a)
        {
            $my_cards = $this->cards_a;
        }
        else if ($_SESSION['username']==$this->player_b)
        {
            $my_cards = $this->cards_b;

        }
        return "game_id=".$this->game_id."&player_a=".$this->player_a."&player_b=".$this->player_b."&my_cards=".implode(",",$my_cards)."&last_open_card=".$this->last_open_card."&closed_cards=".implode(",",$this->closed_cards)."&turn=".$this->turn."&sum_of_turns=".$this->sum_of_turns."&winner=".$this->winner."&game_start_time=".$this->game_start_time."&game_finish_line=".$this->game_finish_time."&all_cards=".implode(",",$this->all_cards)."&sequential_two=".$this->sequential_two."&last_command=".$this->command;
    }
    public function game_put_down_cards($cards) {
        if ($this->turn==0) {$player_id=$this->player_a;} else {$player_id=$this->player_b;}
        $first_card= $this->game_get_cards_data($cards[0]);
        list($sign,$col)=$first_card;

        //if last open card is a plus-two - check if the player chose a plus two card, if he did- play the turn, else return error.
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        if ($l_sign==2) {
            if ($sign !=2) {
                //TODO: deal with error. you must choose a two.
                return 0;
            } else {
                if(count($cards)>1) {
                //TODO error- you can choose only one plus-two card;
                    return 0;
                }
                $this->change_turn();
                $this->incr_turns_count();
                $this->remove_cards($player_id,$cards);
                $this->sequential_two=$this->sequential_two++;
                $this->update_db();
            }
        }
        switch ($sign){
            case 'king':
                if($this->game_put_down_cards($player_id, array_slice($cards,1))) {
                    $this->remove_cards($player_id,$cards[0]);
                    $this->change_turn();
                    $this->incr_turns_count();
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'taki':
                if($this->check_taki($cards)){
                    $this->change_turn();
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'change_cards':
                if($this->check_change_cards($cards)){
                    $this->change_turn();
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->swap_cards();
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'change_dir':
                if($this->check_change_dir($cards)) {
                    $this->change_turn();
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'stop':
                if($this->check_stop($cards)) {
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'plus':
                if($this->check_plus($player_id,$cards)) {
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->update_db();
                    return 1;
                }
               return 0;
            case 'change_col':
                if($this->check_change_col($cards)) {
                    //Todo:: ask for color from user
                    //$new_col =
                    if($this->turn == 0) {
                        unset($this->cards_a[array_search($cards, $this->cards_a)]);
                        $this->turn=1;
                    } else {
                        unset($this->cards_b[array_search($cards, $this->cards_b)]);
                        $this->turn=0;
                    }
                    $new_card="$sign $new_col";
                    $this->last_open_card=$new_card;
                    $this->change_turn();
                    $this->incr_turns_count();
                    $this->update_db();
                    return 1;
                }
                return 0;
            default :
                //in case sign is a number between 1-9
                if(($sign>=1) && ($sign<=9)) {
                    if($this->check_number($cards)) {
                        if($sign==2) {
                            $this->sequential_two++;
                        }
                        $this->incr_turns_count();
                        $this->remove_cards($player_id,$cards);
                        $this->update_db();
                    }
                }
                return 0;
        }
    }
    public function game_did_game_end() {
        if (count($this->cards_a)==0) {
            //a wons
            $this->winner==$this->player_a;
            return 1;
        }
        if (count($this->cards_b)==0) {
            //b wons
            $this->winner==$this->player_b;
            return 1;
        }
        return 0;
    }
    //when changing color, we change the card path. according to the color chosen by the user we set the new card path.
    //if the player has more than one plus-two cards, we dont do anything and return. if he has only one, we do it automatically.
    //we turn 1 if a turn was played automatically, else we return 0- that means the player has to choose one of its twos
}
//take game id saved in cookie or session + take command
$command=$_POST['arg'];
$user_name = $_SESSION['username'];
$result = 0;
$model = new taki_model();
$game = new game($model, $user_name);

$line = explode(" ", $command);
$playerA = $line[3];
$playerB =  $line[4];
if ($line[0] == 'start') {
    $result=$game->game_starts($playerA, $playerB);
} elseif ($line[0]== 'draw') {
    $result=$game->game_draw_cards();
} elseif ($line[0]== 'put') {
    $result=$game->game_put_down_cards(array_slice($line,3,(count($line)-1)));
} else {
    //todo: handle error;
}
//if move was legal - game data will be updated accordingly and result will set to 1;
if($result==0) {
    //TODO: deal with error - exit with error -illegal move
} else {
    if($game->game_did_game_end()) {
        $game->game_ends();
    }
}
echo $game->game_return_game_data();

//TODO: think about deleting the game record if game ended
//TODO: when retrieving game record , do it according to the user name saved in the session.