<?php

$env = "staging"; /* staging or live */

$link = ($env == "staging") ? "http://localhost/ralo" : "";

if (!defined('URL')) {
    define('URL', $link);
}