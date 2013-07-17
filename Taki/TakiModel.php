<?php
class TakiModel
{
     private $db = null;

    function create_account($data)
    {
        create_database();

    }

    function create_database()
    {
        $this->db = DataBase::Instance();

    }
}