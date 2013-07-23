<?php
include_once('member_site_config.php');

class logout {

    private $config;
    function logout()
    {
        $this->config=new member_site_config();
    }

    function logoff()
    {
        session_start();

        $sessionvar = $this->config->get_login_session_var();

        $_SESSION[$sessionvar]=NULL;

        unset($_SESSION[$sessionvar]);
    }
}