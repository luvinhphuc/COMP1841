<?php

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

define('BASE_URL', rtrim($scriptName, '/'));