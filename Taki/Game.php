<?php

include_once('TakiModel.php');
include_once('card.php');

class game {

    private $model;
    private $game_id = 0 ;
    private $player_a = NULL;
    private $player_b = NULL;
    private $cards_a = NULL;                  //list of a's cards
    private $cards_b = NULL;                  // list of b's cards
    private $highest_num_cards_a = 0;
    private $highest_num_cards_b = 0;
    private $last_open_card = NULL;
    private $closed_cards = NULL;             //list of closed cards
    private $turn = NULL;                     // whose turn is it
    private $sum_of_turns = 0;
    private $winner = NULL;                   // by id/username
    private $game_start_time = NULL;
    private $game_finish_time = NULL;
    private $all_cards = NULL;
    private $sequential_two = 0;              //number of two's in a row
    private $command = NULL;

    //gets a string of card path and split it into sections, to understand it's color and sign.
    public function game_get_cards_data ($card_str) {
        list($dir1,$dir2,$dir3,$sign,$col)=explode("\\",$card_str,5);
        $col=explode(".",$col,-1);
        return array($dir1, $dir2, $dir3, $sign, $col);
    }
    private function change_turn () {
        if ($this->turn==1) {
            $this->turn=0;
        } else {$this->turn=1;}
        return;
    }
    private function incr_turns_count () {$this->sum_of_turns++;}
    private function update_db() {
        $this->model->tm_update_game($this->game_id,$this->cards_a, $this->highest_num_cards_a, $this->cards_b, $this->highest_num_cards_b,$this->last_open_card, $this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time, $this->game_finish_time);
    }
    public function game_draw_cards($player_id,$num_of_cards) {
        $this->game_search_game_record($this->game_id);
        $taken_cards = array();
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
            array_push($players_cards, $card);
            array_push($taken_cards,$card);
            $this->turn=0;
        }
        $this->sum_of_turns++;
        $this->closed_cards = array_diff($this->closed_cards, $taken_cards);
        $this->model->tm_update_game($this->game_id,$this->cards_a, $this->highest_num_cards_a, $this->cards_b, $this->highest_num_cards_b,$this->last_open_card, $this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time, $this->game_finish_time);
        //todo: print new game status as string


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
    private function search_two_plus_cards($player) {
        $twos = array();
        if ($this->player_a==$player) {
            $cards=$this->cards_a;
        } else {
            $cards=$this->cards_b;
        }

        foreach($cards as $card) {
            list($dir1, $dir2, $dir3,$sign,$col)=$this->game_get_cards_data($card);
            if ($sign == 2) {array_push($twos,$card);}
        }
        return $twos;
    }

    //if the player has more than one plus-two cards, we dont do anything and return. if he has only one, we do it automaticaly.
    //we turn 1 if a turn was played automatically, else we return 0- that means the player has to choose one of its twos
    //TODO: implement search twos
    public  function game_handle_plus_two($game_id) {
        $this->game_search_game_record($this->game_id);
        if($this->turn==0) {
            $cards=$this->cards_a;
            $player = $this->player_a;
        } else {
            $cards=$this->cards_b;
            $player = $this->player_b;
        }
        $two_plus_cards=$this->search_two_plus_cards($player);
        if(count($two_plus_cards)==0) {
            //take 2 cards from deck;
            $this->game_draw_cards($player,2);
            $this->change_turn();
            $this->incr_turns_count();
            $this->update_db();
            return 1;
        }
        if(count($two_plus_cards)>1) {
            return 0;
        }
        //play the turn automatically
        $this->remove_cards($player,$two_plus_cards);
        $this->change_turn();
        $this->incr_turns_count();
        $this->sequential_two=$this->sequential_two++;
        $this->update_db();
        return 1;
    }

    private function check_taki($cards) {
        $last_sign = NULL;
        $color_was_changed = 0;
        list($dir1, $dir2, $dir3,$sign,$col)=$this->game_get_cards_data($cards[0]);
        foreach ($cards as $card) {
            list($dir,$dir,$dir,$c_sign,$c_col)=$this->game_get_cards_data($card);
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
        return 1;
    }
    private function check_stop($cards) {
        //check that there are no other cards after the change_dir card
        if(count($cards)>1) {return 0;}
        return 1;
    }
    private function check_plus($player_id,$cards) {
        list($dir1, $dir2, $dir3,$sign,$col)=$this->game_get_cards_data($cards[0]);
            if (count($cards)==1) {
                //TODO: Force player to take a card from deck. do it automatically, server side, without asking the player to do it.
                //TODO: animate the card pulling from the deck.
            } else {
                list($dir,$dir,$dir,$c_sign,$c_col)=$this->game_get_cards_data($cards[1]);
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
                //TODO: deal with error
            }
    }
    public function game_put_down_cards($player_id, $cards) {
        $this->game_search_game_record($this->game_id);


        $first_card= $this->game_get_cards_data($cards[0]);
        list($dir1, $dir2, $dir3,$sign,$col)=$first_card;

        //if last open card is a plus-two - check if the player chosed a plus two card, id he did play the turn, else return error.
        list($dir, $dir, $dir,$l_sign,$l_col)=$this->game_get_cards_data($this->last_open_card);
        if ($l_sign==2) {
            if ($sign !=2) {
                //TODO: deal with error. you must choose a two.
            } else {
                if(count($cards)>1) {
                //TODO error- you can choose only one plus-two card;
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
            default : //TODO: DEAL WITH ERROR;
        }
        //todo: print new game status as string
    }

    //when changing color, we change the card path. according to the color chosen by the user we set the new card path.
    public function game_change_color($player_id,$card) {
        //Todo:: ask for color from user
        //$new_col =
        //TODO:: set the color field in card obj to enforce obligation!!!!!!!!!!
        list($dir1, $dir2, $dir3,$sign,$col)= $this->game_get_cards_data($card);
        if($this->turn == 0) {
            unset($this->cards_a[array_search($card, $this->cards_a)]);
            $this->turn=1;
        } else {
            unset($this->cards_b[array_search($card, $this->cards_b)]);
            $this->turn=0;
        }

        $new_card="\\$dir1\\$dir2\\$dir3\\$sign\\$new_col.jpg";
        $this->last_open_card=$new_card;
        $this->sum_of_turns++;
        $this->model->tm_update_game($this->game_id,$this->cards_a, $this->highest_num_cards_a, $this->cards_b, $this->highest_num_cards_b,$this->last_open_card, $this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner,$this->game_start_time, $this->game_finish_time);
        //todo: print new game status as string
    }

    private function game_search_game_record() {
        $game_data=$this->model->tm_search_game_by_game_id($this->game_id);
        list($this->$game_id,$this->$cards_a,$this->$highest_number_of_cards_a,$this->$cards_b,$this->$highest_number_of_cards_b,$this->$last_open_card,$$this->closed_cards,$this->$turn,$this->$sum_of_turns,$this->$winner,$this->$game_start_time,$this->$game_finish_time,$this->$sequential_two)=$game_data;
        return;
    }

    //C'tor
    public function game($game_id,$model,$command) {
        $this->model = $model;
        $this->all_cards = array();
        $this->cards_a = array();
        $this->cards_b = array();
        $this->closed_cards= array();
        $this->game_id= $game_id;
        $this->command= $command;
    }
    //initialize a new game, and insert it into DB.
    //TODO:initialize first open card randomly
    public  function game_starts ($player_a, $player_b) {
        $a_cards = array();
        $b_cards = array();
        $this->$player_a = $player_a;
        $this->$player_b = $player_b;

        //initialize all cards
        $this->game_init_all_cards();
        //initialize a's cards
        foreach (array_rand($this->all_cards, 8) as $k) {
            $a_card = $this->all_cards[$k];
            array_push($this->cards_a, $a_card);
            array_push($a_cards,$a_card);
        }
        $t_cards = array_diff($this->all_cards, $a_cards);
        //initialize b's cards
        foreach (array_rand($t_cards, 8) as $k) {
            $b_card = $this->all_cards[$k];
            array_push($this->cards_b, $b_card);
            array_push($b_cards,$b_card);
        }
        //initialize closed cards
        $this->closed_cards= array_diff($t_cards, $b_cards);

        //define who gets first turn
        $this->turn = rand(0,1);

        //initialize start time
        $this->game_start_time = date("d:m:y h:i:s ");

        // insert new game to DB
        $this->model->tm_insert_new_game($this->player_a,$this->player_b,$this->cards_a,$this->highest_num_cards_a,$this->cards_b,$this->highest_num_cards_b,$this->last_open_card,$this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner);
    }
    //Initialize all cards
    //cards with no color ("change col", "change_cards") will be stored in "sign\special" directory
    public function game_init_all_cards() {
        //creating all regular cards
        $i =1;
        for(;$i<=9;$i++){
            foreach(array('red', 'blue', 'yellow', 'green') as $color) {
                $card = new card('$i', $color);
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
        //TODO:: add king cards!!!!!!!!!!!!!!!!!!
    }
    //Update players record when game ends
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
        //TODO: think about deleting the game record
    }


}

//take game id saved in cookie or session + take command
$model = new taki_model();
$game = new game($game_id,$model, $command);

//TODO:: if game id is not defined- create a new game- needs to players!!!!!!!!!!!!!
//TODO:: take out all necessary parameters before calling the command
switch ($game->$command) {
    case 'Draw_Cards' :
        $result=$game->game_draw_cards($game_id,$num_of_cards);
        break;
    case 'Put_Down_Cards' :
       $result= $game->game_put_down_cards($player_id,$cards);
        break;
    case 'Change_Color' :
       $result= $game->game_change_color($game_id, $card);
        break;
    case 'Start_new_Game' :
        $result = $game->game_starts($player_a, $player_b);
    case 'Plus_Two':
        $result= $game->game_handle_plus_two($game_id);
    default :  //todo: handle error
}

//TODO: print updated game record as a string, maybe new function?