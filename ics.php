<?php

include __DIR__ . '/inc/main.inc.php';


$evenements = getEvenements();


// Définir l'en-tête du fichier ICS
$icsContent = "BEGIN:VCALENDAR\r\n";
$icsContent .= "VERSION:2.0\r\n";
$icsContent .= "PRODID:-//Coworking Metz//Evenements//EN\r\n";

foreach ($evenements as $event) {
    if(empty($event['calendrier'])) continue;
    $icsContent .= "BEGIN:VEVENT\r\n";
    $icsContent .= "UID:" . uniqid() . "@yourdomain.com\r\n";
    $icsContent .= "DTSTAMP:" . gmdate('Ymd') . 'T' . gmdate('His') . "Z\r\n";
    $icsContent .= "DTSTART:" . gmdate('Ymd', strtotime($event['date'])) . 'T' . gmdate('His', strtotime($event['heure'])) . "Z\r\n";
    $icsContent .= "SUMMARY:" . ($event['evenement']) . "\r\n";
    $icsContent .= "DESCRIPTION:" . ($event['description']) . "\r\n";
    $icsContent .= "LOCATION:" . ($event['lieu']) . "\r\n";
    $icsContent .= "URL;VALUE=URI:" . urlEvenement($event) . "\r\n";
    $icsContent .= "X-IMAGE-URL:" . $event['image_url'] . "\r\n";
    $icsContent .= "END:VEVENT\r\n";
}

$icsContent .= "END:VCALENDAR";

cors();
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="coworking-evenements.ics"');
echo $icsContent;
