<?php

require_once __DIR__ . '/sendios/SendiosDi.php';
foreach (glob(__DIR__ . '/sendios/*.php') as $filename) {
    require_once $filename;
}
