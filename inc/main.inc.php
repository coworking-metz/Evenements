<?php

include __DIR__ . '/secrets.inc.php';
include __DIR__ . '/utils.inc.php';
include __DIR__ . '/supabase.inc.php';
if(isset($_GET['debug'])) {
    error_reporting(E_ALL);
ini_set('display_errors', 'On');
}
noCacheHeaders();
function logos()
{
    return [
        ['url' => 'https://evenements.coworking-metz.fr/img/logo.png', 'nom' => 'Coworking'],
        ['url' => 'https://a.mailmunch.co/attachments/assets/000/349/761/large/Logo_bliiida.png', 'nom' => 'Bliiida'],
    ];
}
