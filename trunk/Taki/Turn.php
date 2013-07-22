<?php

//take game id saved in coockie or session

//get record of game by game id from DB.

//parse the record into variables

//switch on command

function take_cards_from_deck($player_id,$num_of_cards)
{
    $taken_cards = array();
    if(turn == 0) {
        $players_cards = $a_cards;
    } else {
        $players_cards = $b_cards;
    }
    foreach (array_rand($closed_cards, $num_of_cards) as $k) {
        $card = $closed_cards[$k];
        array_push($players_cards, $card);
        array_push($taken_cards,$card);
    }
    $closed_cards = array_diff($closed_cards, $taken_cards);

    //TODO: Animation of flying cards from deck to player!
}