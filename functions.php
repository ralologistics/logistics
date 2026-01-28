<?php

$env = "staging"; /* staging or live */

$link = ($env == "staging") ? "http://localhost/ralo" : "";

define('URL', $link);