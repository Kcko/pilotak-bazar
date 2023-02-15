<?php

if (file_exists(__DIR__ .'/../.maintenance.stop.php')) {
	rename(__DIR__ .'/../.maintenance.stop.php', __DIR__ .'/../.maintenance.php');
}
