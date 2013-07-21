<?php

include_once('TakiModel.php');
include_once('card.php');

class game {

    private $model;
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


    //C'tor
    public function game($model) {
        $this->model = $model;
        $this->all_cards = array();
        $this->cards_a - array();
        $this->cards_b - array();
        $this->closed_cards= array();

    }
    //initialize a new game, and insert it into DB.
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
        $this->model->db->db_insert_new_game($this->player_a,$this->player_b,$this->cards_a,$this->highest_num_cards_a,$this->cards_b,$this->highest_num_cards_b,$this->last_open_card,$this->closed_cards,$this->turn,$this->sum_of_turns,$this->winner);
    }

    //Initialize all cards
    public function game_init_all_cards() {
        //creating all regular cards
        $i =1;
        for(;$i<=9;$i++){
            foreach(array('red', 'blue', 'yellow', 'green') as $color) {
                $card = new card('$i', $color);
                array_push($this->all_cards, $card);
            }
        }
        //creating "+" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('plus', $color);
            array_push($this->all_cards, $card);
        }
        //creating "<==>" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('change_dir', $color);
            array_push($this->all_cards, $card);
        }
        //creating "stop" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('stop', $color);
            array_push($this->all_cards, $card);
        }
        //creating "change col" cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('change_col','special');
            array_push($this->all_cards, $card);
        }
        //creating TAKI cards
        foreach(array('red', 'blue', 'yellow', 'green') as $color) {
            $card = new card('taki', $color);
            array_push($this->all_cards, $card);
        }
        //creating "change cards" cards
        foreach(array('red', 'blue') as $color) {
            $card = new card('change_cards', 'special');
            array_push($this->all_cards, $card);
        }
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