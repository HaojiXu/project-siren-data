<?php
# This file contains the config information to connect to a local database.
# Please note that all information, including secret, are stored plain text.

# These lines are for Slim Framework, editing them might cause it to function improperly.
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

# Edit the following lines to connect to a local database
$config['db']['host']   = "localhost";
$config['db']['user']   = "user";
$config['db']['pass']   = "password";
$config['db']['dbname'] = "siren";


?>
