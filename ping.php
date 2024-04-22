<?php

include __DIR__ . '/inc/main.inc.php';

$evenements = getEvenements();

if ($evenements) echo 'true';
