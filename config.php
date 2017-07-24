<?php
# This file contains the config information to connect to a local database.
# Please note that all information, including secret, are stored plain text.

# These lines are for Slim Framework, editing them might cause it to function improperly.
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

class db{
        // Properties
        private $dbhost = '127.0.0.1';
        private $dbuser = 'root';
        private $dbpass = '123456';
        private $dbname = 'siren';
        // Connect
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
            $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
}

?>
