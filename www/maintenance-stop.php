<?php

if (file_exists(__DIR__ .'/temp')) {
	chmod(__DIR__ .'/temp', 0777);
}

if (file_exists(__DIR__ .'/temp/sessions')) {
	chmod(__DIR__ .'/temp/sessions', 0777);
}

if (file_exists(__DIR__ .'/log')) {
	chmod(__DIR__ .'/log', 0777);
}

if (file_exists(__DIR__ . '/../.maintenance.php')) {
	rename(__DIR__ . '/../.maintenance.php', __DIR__ . '/../.maintenance.stop.php');
}

