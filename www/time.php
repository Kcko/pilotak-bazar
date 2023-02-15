<?PHP

// Create a new DateTime Object from 'now'
$date = new DateTime('now', new DateTimeZone('Europe/Berlin'));

// echo the current time
echo 'php time Europe/Berlin: '.print_r($date,true);

// echo some space
echo '<br>';
echo '<br>';

// Set the timezone to UTC
$date->setTimezone( new DateTimeZone( 'UTC' ) );

// echo the current time
echo 'php time UTC: '.print_r($date,true);
