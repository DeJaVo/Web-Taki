<?php

include_once('TakiModel.php');
include_once('card.php');
if(!isset($_SESSION)){ session_start(); }
if (!(isset($_SESSION['username']))) { header ("URL=../Taki/login.html'"); }

class game {
    //TODO: when closed cards == 0 , reest closed cards
    private $model;
    public  $game_id = NULL ;
    public $player_a = NULL;
    public $player_b = NULL;
    private $cards_a = NULL;                                         //list of a's cards
    private $cards_b = NULL;                                            // list of b's cards
    private $highest_num_cards_a = 0;
    private $highest_num_cards_b = 0;
    private $last_open_card = NULL;
    private $closed_cards = NULL;                                       //list of closed cards
    public  $turn = NULL;                                               // whose turn is it
    private $sum_of_turns = 0;
    public $winner = 9999;                                             // by id/username
    private $game_start_time = NULL;
    private $game_finish_time = NULL;
    private $all_cards = NULL;
    private $sequential_two = 0;                                        //number of two's in a row
    private $command = NULL;


    private function game_get_cards_data ($card_str) {
        list($sign,$col)=explode(" ",$card_str,2);
        return array($sign, $col);
    }               //gets a string of card path and split it into sections, to understand it's color and sign.

    public function cvt_brk_crd_to_str($broken_cards) {
        //converts an array of broken cards to an array of fix cards
        //a[0] = five, a[1]= red ===> a[0] "five red"
        $result= array();
        for($i=0;$i< count($broken_cards);) {
            $card_str=$broken_cards[$i]." ".$broken_cards[$i+1];
            array_push($result,$card_str);
            $i=$i+2;
        }
        return $result;
    }
    private function take_one($player_id) {
        $k = array_rand($this->closed_cards,1);
        $card = $this->closed_cards[$k];
        if($player_id==$this->player_a) {
            array_push($this->cards_a, $card);
        } else {
            array_push($this->cards_b, $card);
        }
        unset($this->closed_cards[$k]);
        $this->closed_cards=array_values($this->closed_cards);
    }
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
                $key= array_search($card, $this->cards_a);
                if($key != -1) {
                    unset($this->cards_a[$key]);
                    $this->last_open_card=$card;
                }
            } else {
                $key= array_search($card, $this->cards_b);
                if($key != -1) {
                    unset($this->cards_b[$key]);
                    $this->last_open_card=$card;
                }
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

        //if last open card color is different than this taki card color and they don't have the same sign then turn is not legal.
        if (($l_col!= $col) && ($l_sign != $sign) ) {return 0;}

        foreach ($cards as $card) {
            list($c_sign,$c_col)=$this->game_get_cards_data($card);
            if(($c_col == $col)&& !$color_was_changed) {
                $last_sign=$c_sign;
                continue;
            } elseif ($last_sign== $c_sign) {
                if(!$color_was_changed) {
                    $color_was_changed=1;
                    continue;
                } else {
                    return 0;
                }
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
        if ($l_col == $col) {return 1;}
        if($l_sign == $sign) {return 1;}
        return 0;
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
        if ($l_col== $col) {return 1;}
        //if last open card has the same sign as this card then turn is legal;
        if ($l_sign== $sign) {return 1;}
        return 0;
    }
    private function check_plus($player_id,$cards) {
        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        //if last open card color is different than this card color then turn is not legal.
        if (($l_col!= $col) && ($l_sign != $sign) ) {return 0;}

        if (count($cards)==1) {
            //Force player to take a card from deck. do it automatically, server side, without asking the player to do it.
            $this->take_one($player_id);
            return 1;
        } else {
            list($c_sign,$c_col)=$this->game_get_cards_data($cards[1]);
            if((($c_sign=='one') || ($c_sign=='two') || ($c_sign=='three') || ($c_sign=='four') || ($c_sign=='five') || ($c_sign=='six') || ($c_sign=='seven') || ($c_sign=='eight') || ($c_sign=='nine')) && (count($cards)==2)) {
                if ($c_col == $col) {return 1;}
            }
            if($c_sign=='king') {
                $this->last_open_card=$cards[0];
                if ($this->game_put_down_cards($player_id,array_slice($cards,1))) {
                    $this->last_open_card=$cards[count($cards)-1];
                    $this->change_turn();
                    return 1;
                }
            }
            if($c_sign=='taki') {
                $this->last_open_card=$cards[0];
                if ($this->check_taki(array_slice($cards,1))) {
                    $this->change_turn();
                    return 1;
                }
            }
            if($c_sign=='change_cards') {
                $this->last_open_card=$cards[0];
                if($this->check_change_cards(array_slice($cards,1))) {
                    $this->swap_cards();
                    $this->change_turn();
                    return 1;
                }
            }
            if($c_sign=='change_dir') {
                $this->last_open_card=$cards[0];
                if($this->check_change_dir(array_slice($cards,1))) {
                    $this->change_turn();
                    return 1;
                }
            }
            if($c_sign=='stop') {
                $this->last_open_card=$cards[0];
                if($this->check_stop(array_slice($cards,1))) {
                    return 1;
                }
            }
            if($c_sign=='plus') {
                $this->last_open_card=$cards[0];
                return $this->check_plus($player_id,array_slice($cards,1));
            }
        }
        return 0;
    }
    private function check_number($cards) {
        //get first card data
        list($sign,$col)=$this->game_get_cards_data($cards[0]);
        //get last open card data
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        //if player selected more than one card its illegal.
        if(count($cards)>1) {return 0;}
        //if last open card color is different than this card color then turn is not legal.
        if ($l_col== $col) {return 1;}
        //if last open card has the same sign as this card then turn is legal;
        if ($l_sign== $sign) {return 1;}
        return 0;
    }
    private function game_init_all_cards() {
        //creating all regular cards
        $numbers= array('one', 'two', 'three','four', 'five','six','seven','eight','nine');
        foreach($numbers as $num){
            foreach(array('red', 'blue', 'yellow', 'green') as $color) {
                $card = new card($num, $color);
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
        $this->model=$model;
        $this->all_cards = array();
        $this->cards_a = array();
        $this->cards_b = array();
        $this->closed_cards= array();
        $game_data=$this->model->tm_search_game_by_user_name($user_name);
        if(!empty($game_data['game_id'])) {
            $game_data['cards_A']=explode(",",$game_data['cards_A']);
            $game_data['cards_B']=explode(",",$game_data['cards_B']);
            $game_data['closed_cards']=explode(",",$game_data['closed_cards']);
            $this->game_id= intval($game_data['game_id']);
            $this->player_a=$game_data['usernameA'];
            $this->player_b=$game_data['usernameB'];
            $this->cards_a= $game_data['cards_A'];
            $this->cards_b= $game_data['cards_B'];
            $this->highest_number_of_cards_a=intval($game_data['highest_number_of_cards_A']);
            $this->highest_number_of_cards_b=intval($game_data['highest_number_of_cards_B']);
            $this->last_open_card=$game_data['last_open_card'];
            $this->closed_cards=$game_data['closed_cards'];
            $this->turn=intval($game_data['turn']);
            $this->sum_of_turns=intval($game_data['sum_of_turns']);
            $this->winner=intval($game_data['winner']);
            $this->game_start_time=$game_data['game_start_time'];
            $this->game_finish_time=$game_data['game_finish_time'];
            $this->sequential_two=intval($game_data['sequential_two']);
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
            unset($this->all_cards[$k]);
            array_push($this->cards_a, $a_card);
            //array_push($a_cards,$a_card);
        }
        //$t_cards = array_diff($this->all_cards, $a_cards);
        //initialize b's cards
        foreach (array_rand($this->all_cards, 8) as $k) {
            $b_card = $this->all_cards[$k];
            unset($this->all_cards[$k]);
            array_push($this->cards_b, $b_card);
            //$b_cards[]=$b_card;
        }
        //initialize closed cards
        //$this->closed_cards= array_diff($t_cards, $b_cards);
        $this->closed_cards=array_values($this->all_cards);
        $this->all_cards=array();
        $this->game_init_all_cards();

        //initialize first open card randomly
        $ok = 0;
        do {
            $key=array_rand($this->closed_cards, 1);
            list($sign, $col)=$this->game_get_cards_data($this->closed_cards[$key]);
            if(($sign=='one') || ($sign=='three') || ($sign=='four') || ($sign=='five') || ($sign=='six') || ($sign=='seven') || ($sign=='eight') || ($sign=='nine')) {
                $ok =1;
            }
        } while ($ok == 0);
        $first_card=$this->closed_cards[$key];
        $this->last_open_card=$first_card;
        unset($this->closed_cards[$key]);

        //define who gets first turn
        $this->turn = rand(0,1);

        //initialize highest number of cards
        $this->highest_num_cards_a=8;
        $this->highest_num_cards_b=8;

        //initialize start time
        $this->game_start_time = date("Y-m-d H:i:s");
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
        $this->model->tm_update_player($username,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game );

        $b_data =$this->model->tm_search_user_by_username($this->player_b);
        list($username,$user_password,$nick_name,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game) = $b_data;
        if($this->player_b==$this->winner) {
            $num_of_wins++;
        }else {
            $num_of_loses++;
        }
        $average_num_of_cards_per_game= ($this->highest_num_cards_b+($average_num_of_cards_per_game*$num_of_games))/($num_of_games + 1);
        $num_of_games++;
        $this->model->tm_update_player($username,$num_of_games,$num_of_wins,$num_of_loses,$average_num_of_cards_per_game);

        $this->game_id=0;
        $this->game_finish_time = date("Y-m-d H:i:s");
        $this->update_db();
    }                                  //Update players record when game ends

    public function game_draw_cards() {
        $taken_cards = array();
        //if last open card was plus-2 card- check how many twos were piled up.
        //else take only one card
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        if ($l_sign=='two') {
            $num_of_cards= $this->sequential_two*2;
            $this->sequential_two=0;
        } else {
            $num_of_cards=1;
        }
        if($this->turn == 0) {
            $players_cards = $this->cards_a;
            $count = sizeof($this->cards_a)+ $num_of_cards;
            if($count > $this->highest_num_cards_a) {$this->highest_num_cards_a= $count;}
        } else {
            $players_cards = $this->cards_b;
            $count = sizeof($this->cards_b)+ $num_of_cards;
            if($count > $this->highest_num_cards_b) {$this->highest_num_cards_b= $count;}
        }
        for($i=0;$i<$num_of_cards;$i++) {
            $k = array_rand($this->closed_cards,1);
            $card = $this->closed_cards[$k];
            array_push($players_cards, $card);
            unset($this->closed_cards[$k]);
        }
        if($this->turn == 0) {
            $this->cards_a=array();
            $this->cards_a=$players_cards;
            $this->turn=1;
        } else {
            $this->cards_b=array();
            $this->cards_b=$players_cards;
            $this->turn=0;
        }
        $this->sum_of_turns++;
        //$this->closed_cards = array_diff($this->closed_cards, $taken_cards);
        $this->closed_cards=array_values($this->closed_cards);
        $this->update_db();
        //$this->model->tm_update_game($this->game_id,$this->cards_a, $this->highest_num_cards_a, $this->cards_b, $this->highest_num_cards_b,$this->last_open_card, $this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time, $this->game_finish_time);
        return 1;
    }

    public function game_return_game_data() {
        $username =$this->model->get_decrypted($_SESSION['username']);
        if($username==$this->player_a)
        {
            $my_cards = $this->cards_a;
            $opp_num_cards= count($this->cards_b);
        }
        else
        {
            $my_cards = $this->cards_b;
            $opp_num_cards= count($this->cards_a);
        }
        $my_cards = implode(",",$my_cards);
        $str = "game_id=".$this->game_id."&player_a=".$this->player_a."&player_b=".$this->player_b."&my_cards=".$my_cards."&opp_num_cards=".$opp_num_cards."&last_open_card=".$this->last_open_card."&turn=".$this->turn."&sum_of_turns=".$this->sum_of_turns."&winner=".$this->winner."&game_start_time=".$this->game_start_time."&game_finish_line=".$this->game_finish_time."&sequential_two=".$this->sequential_two;
        return $str;
    }
    public function game_put_down_cards($cards) {
        if ($this->turn==0) {$player_id=$this->player_a;} else {$player_id=$this->player_b;}
        $first_card= $this->game_get_cards_data($cards[0]);
        list($sign,$col)=$first_card;

        //if last open card is a plus-two - check if the player chose a plus two card, if he did- play the turn, else return error.
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        if (($l_sign=='two') && ($this->sequential_two>0)) {
            if ($sign !='two') {
                return 0;
            } else {
                if(count($cards)>1) {
                    return 0;
                }
                $this->incr_turns_count();
                $this->remove_cards($player_id,$cards);
                $this->sequential_two=$this->sequential_two++;
                $this->change_turn();
                $this->update_db();
            }
        }
        switch ($sign){
            case 'king':
                if (count($cards)>1) {
                    $next_card= $cards[1];
                    $next_card_data = $this->game_get_cards_data($next_card);
                    list($n_sign, $n_col)= $next_card_data;
                    $new_card=$n_sign." ".$n_col;
                    $this->last_open_card=$new_card;
                }
                if($this->game_put_down_cards(array_slice($cards,1))) {
                    $this->remove_cards($player_id,$cards[0]);
                    $this->last_open_card=$cards[count($cards)-1];
                    $this->change_turn();
                    $this->incr_turns_count();
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'taki':
                if($this->check_taki($cards)){
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
                    if($l_sign!= 'stop') {
                        $this->change_turn();
                    }
                    if($l_sign == 'two') {
                        $this->sequential_two++;
                    }
                    if($l_sign== 'plus') {
                        $this->take_one($player_id);
                    }
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'change_cards':
                if($this->check_change_cards($cards)){
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->swap_cards();
                    $this->change_turn();
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'change_dir':
                if($this->check_change_dir($cards)) {
                    $this->incr_turns_count();
                    $this->remove_cards($player_id,$cards);
                    $this->change_turn();
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
                    if($l_sign!= 'stop') {
                        $this->change_turn();
                    }
                    if($l_sign == 'two') {
                        $this->sequential_two++;
                    }
                    $this->update_db();
                    return 1;
                }
                return 0;
            case 'change_col':
                if($this->check_change_col($cards)) {
                    if($this->turn == 0) {
                        unset($this->cards_a[array_search($cards[0], $this->cards_a)]);
                    } else {
                        unset($this->cards_b[array_search($cards[0], $this->cards_b)]);
                    }
                    $this->last_open_card=$cards[0];
                    $this->update_db();
                    return 3;
                }
                return 0;
            default :
                if(($sign=='one') || ($sign=='two') || ($sign=='three') || ($sign=='four') || ($sign=='five') || ($sign=='six') || ($sign=='seven') || ($sign=='eight') || ($sign=='nine')) {
                    //in case sign is a number between 1-9
                    if($this->check_number($cards)) {
                        if($sign=='two') {
                            $this->sequential_two++;
                        }
                        $this->incr_turns_count();
                        $this->remove_cards($player_id,$cards);
                        $this->change_turn();
                        $this->update_db();
                        return 1;
                    }
                }
                return 0;
        }
    }
    public function game_change_color($new_col) {
        list($l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        if($l_sign=='change_col') {
            $new_card="$l_sign $new_col";
            $this->last_open_card=$new_card;
            $this->change_turn();
            $this->incr_turns_count();
            $this->update_db();
            return 1;
        }
    }
    public function game_did_game_end() {

        if (empty($this->cards_a)) {
            //a wons
            $this->winner=$this->player_a;
            return 1;
        }
        if (empty($this->cards_b)) {
            //b wons
            $this->winner=$this->player_b;
            return 1;
        }
        if(($this->winner==0) || ($this->winner==1)) {
            return 1;
        }
        return 0;
    }
    public function game_surrender($user) {
        if($user==$this->player_a) {
            $this->winner=$this->player_b;
        } else {
            $this->winner=$this->player_a;
        }
    }
    //when changing color, we change the card path. according to the color chosen by the user we set the new card path.
    //if the player has more than one plus-two cards, we dont do anything and return. if he has only one, we do it automatically.
    //we turn 1 if a turn was played automatically, else we return 0- that means the player has to choose one of its twos
}
if(isset($_POST['arg']))
{
    $command=$_POST['arg'];

    $model = new taki_model();
    $user = $model->get_decrypted( $_SESSION['username']);

    $result = 0;
    $game = new game($model,$user);

//|| ($game->game_id == 0)
    if (($game->winner==0)||($game->winner==1) )
    {
        //in case the game already ended
        if($user==$game->winner)
        {
            echo "6";
        }
        if ($user!= $game->winner)
        {
            echo "5";
        }
    }
    else
    {
        $line = explode(" ", $command);
        switch($line[0])
        {
            case 'start':
                $playerA = $line[3];
                $playerB =  $line[4];
                $result=$game->game_starts($playerA, $playerB);
                break;
            case 'draw':
                if ((($user==$game->player_a) && ($game->turn==1)) || (($user==$game->player_b) && ($game->turn==0))) {
                    //in case malicious users try to play not according to the turns...
                    break;
                }
                $result=$game->game_draw_cards();
                break;
            case 'put':
                if ((($user==$game->player_a) && ($game->turn==1)) || (($user==$game->player_b) && ($game->turn==0))) {
                    //in case malicious users try to play not according to the turns...
                    break;
                }
                $broken_cards=array_slice($line,3,(count($line)-1));
                $cards=$game->cvt_brk_crd_to_str($broken_cards);
                $result=$game->game_put_down_cards($cards);
                break;
            case 'surrender':
                $game->game_surrender($user);
                $game->game_ends();
                $result=5;
                break;
            case 'change':
                if ((($user==$game->player_a) && ($game->turn==1)) || (($user==$game->player_b) && ($game->turn==0))) {
                    //in case malicious users try to play not according to the turns...
                    break;
                }
                $result=$game->game_change_color($line[2]);
                break;
            case 'turn':
                if((($user==$game->player_a)&&($game->turn!=0)) || (($user==$game->player_b)&&($game->turn!=1)))
                {
                    //in case it's not you turn
                    $result=8;
                    break;
                }
                else
                {
                    $game_data=$game->game_return_game_data();
                    $result =7;
                    break;
                }
            case 'print':
                //$result= $game->game_return_game_data();
                $result=1;
                break;
            default:
                $model->tm_handle_error("Fatal Error- Dont mess with our game!");
                break;
        }

        switch($result)
        {
            case 0:
                if(is_string($result))
                {
                    echo $result;
                }
                else
                {
                    echo "1";
                }

                break;
            case 1:
                if($game->game_did_game_end())
                {
                    $game->game_ends();
                    $game_data=$game->game_return_game_data();
                    echo "4"." ".$game_data;
                    break;
                }
                else
                {
                    $game_data=$game->game_return_game_data();
                    echo "2"." ".$game_data;
                    break;
                }
            default:
                if (($result== 7) || ($result == 8 )) {
                    echo $result." ".$user;
                }else {
                    echo $result;
                }

        }
    }
}
//if move was legal - game data will be updated accordingly and result will set to 1;
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

