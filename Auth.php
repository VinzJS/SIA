<?php

    session_start();

    if(!isset($_SESSION['ucode']) || (isset($_SESSION['ucode']) && empty($_SESSION['ucode'])))
    {
        if(strstr($_SERVER['PHP_SELF'], 'Login.php') === false)
        header('location:Login.php');
    }
    
    else
    {
        if(strstr($_SERVER['PHP_SELF'], 'Index.php') === false)
        header('location:Index.php');
    }

?>