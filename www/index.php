<?php
// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0002);

use App\Bootstrap;
use Nette\Application\Application;

define('INDEX_DIR', __DIR__);

// // AUTH
// if (  
// 		(strpos($_SERVER['HTTP_HOST'], 'pilotak-bazar.rjwebdesign.cz') !== false)  ||
// 		(strpos($_SERVER['HTTP_HOST'], 'pilotak-web.rjwebdesign.cz') !== false) 
// ) {
// 	require 'auth.php';
// }


// Získání aktuální URL
$currentURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Odstranění koncového lomítka, pokud existuje, s výjimkou domény
if ($currentURL !== 'http://' . $_SERVER['HTTP_HOST'] . '/') {
    $newURL = rtrim($currentURL, '/');
} else {
    $newURL = $currentURL;
}

// Kontrola, zda URL obsahuje cestu /admin/
if (strpos($newURL, 'admin') === false) {
    // Přesměrování na novou URL
    if ($newURL !== $currentURL) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $newURL");
        exit();
    }
}


if (file_exists(__DIR__ . '/../.maintenance.php')) {
    require __DIR__ . '/../.maintenance.php';
} else {
    require_once __DIR__ . '/../app/autoload.php';
    
	(new Bootstrap())->boot()
		->createContainer()
		->getByType(Application::class)
		->run();
}