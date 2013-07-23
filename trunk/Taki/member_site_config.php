<?php


class member_site_config
{
    var $rand_key;

    public function member_site_config()
    {
        $this->rand_key = '0iQx5oBk66oVZep';
    }

    public function check_login()
    {
        if(!isset($_SESSION)){ session_start(); }

        $sessionvar = $this->get_login_session_var();

        if(empty($_SESSION[$sessionvar]))
        {
            return false;
        }
        return true;
    }

    public function get_login_session_var()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
}

$config = new member_site_config();