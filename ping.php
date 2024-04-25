<?php

include __DIR__ . '/inc/main.inc.php';

$evenements = getEvenements();

allow_cors();
if ($evenements) echo 'true';
