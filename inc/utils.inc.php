<?php

function loginRedirect()
{
exit;
    $auth = true;
    if (function_exists('wp_get_current_user')) {
        $user = wp_get_current_user();
        if (!$user) $auth = false;
        if (!current_user_can('administrator')) $auth = false;
    } else
        $auth = false;

    if ($auth) {
        $to = '/admin.php';
    } else if(function_exists('site_url')){
        $to = site_url('wp-login.php?return=' . urlencode(baseUrl('/admin.php')));
    } else return;

    header('Location: ' . $to);
    exit;
}



function noCacheHeaders()
{
    header_remove('Pragma');
    header_remove('Expires');
    header_remove('Cache-Control');
    header('Cache-Control: no-store, max-age=30, s-maxage=0');
    header('Expires: 0');
}
function hexToRgb($hexColor)
{
    // Remove '#' if it exists
    $hexColor = ltrim($hexColor, '#');

    // Convert hex color to RGB values
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    // Return the RGB color as a string
    return "$r, $g, $b";
}


function participationEvenement($evenement, $details = false)
{
    if (!$evenement) return;

    $ret = $evenement['ok'] . ' participant(s)';
    if ($details) {
        $ret .= ', ' . $evenement['ko'] . ' refu(s)';
    }
    if ($evenement['maybe']) {
        $ret .= ' et ' . $evenement['maybe'] . ' indécis';
    }


    return $ret;
}
function descriptionEvenement($evenement)
{

    $description = $evenement['evenement'];

    if ($evenement['withdate']) {
        $description .= ' organisé le ' . formatDateToFrench($evenement['date']);
        if ($evenement['heure']) {
            $description .= ' à ' . formatTimeToHHMM($evenement['heure']);
        }
    }
    $description = htmlspecialchars($description);
    return $description;
}
function slugify($str)
{
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9 _-]/', '', $str);
    $str = preg_replace('/\s+/', '-', $str);

    while (strpos($str, '--') !== false) {
        $str = str_replace('--', '-', $str);
    }
    return trim($str, '-');
}

function urlEvenement($evenement)
{
    if (is_numeric($evenement)) {
        $evenement = getEvenement($evenement);
    }
    return baseUrl() . slugify($evenement['evenement']) . '/' . $evenement['id'];
}

function baseUrl($uri = '/')
{
    return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$uri";
}
function rediriger($url = null)
{
    if (is_null($url)) {
        $url = $_SERVER['HTTP_REFERER'];
    }

    header('Location: ' . $url);
    exit;
}
function erreur($code = 400)
{

    http_response_code($code);
    exit("Erreur " . $code);
}

if (!function_exists('me')) {
    function me()
    {
        m(...func_get_args());
        exit;
    }

    function m()
    {
        foreach (func_get_args() as $arg) {
            echo '<pre style="background:#fee100;color:black;padding:1rem">';
            // print_r(debug_backtrace());
            echo    htmlspecialchars(print_r($arg, true));
            echo '</pre>';
        }
    }
}


/**
 * Convertit une date au format YYYYMMDD en format français avec nom du jour et nom du mois.
 * 
 * @param string $date Date au format YYYYMMDD
 * @return string Date au format français
 */
function formatDateToFrench($date)
{

    $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $months = [
        'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
    ];

    $dateTime = new DateTime($date);
    $dayName = $days[$dateTime->format('w')];
    $dayNumber = $dateTime->format('j');
    $monthName = $months[$dateTime->format('n') - 1];

    return "$dayName $dayNumber $monthName";
}


/**
 * Convertit une heure au format HHMM ou HMM en format HH:MM.
 * 
 * @param string $time Heure au format HHMM ou HMM
 * @return string Heure au format HH:MM
 */
function formatTimeToHHMM($time)
{
    $tab = explode(':', $time);
    return $tab[0] . ':' . $tab[1];
}

/**
 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
 *  origin.
 *
 *  In a production environment, you probably want to be more restrictive, but this gives you
 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
 *
 *  - https://developer.mozilla.org/en/HTTP_access_control
 *  - https://fetch.spec.whatwg.org/#http-cors-protocol
 *
 */
function cors() {
    
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
}