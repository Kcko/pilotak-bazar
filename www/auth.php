<?php

$LoginSuccessful = FALSE;
$login = 'pilotak';
$password = 'pil0tak';

if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
 
    $Username = $_SERVER['PHP_AUTH_USER'];
    $Password = $_SERVER['PHP_AUTH_PW'];
 
    if ($Username == $login && $Password == $password){
        $LoginSuccessful = TRUE;
    }
}

if (!$LoginSuccessful)
{

    header('WWW-Authenticate: Basic realm="Secret page"');
    header('HTTP/1.0 401 Unauthorized');
 
    print "Login failed!\n";
    exit(0);
 
}



