<?php
use ORM\Connection\Connection;
use ORM\Model\User;

require 'vendor/autoload.php';




$db = Connection::getInstance();

if($db instanceof PDO){
    return print('\Start Code');
}

return print('\End Code');
