<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 09/02/2017
 * Time: 11:31 AM
 */

include("functionality.php");

$me = User::withUsername("admin");
$he = User::withUsername("Denis");

echo "\n---------------------------------------------\n\n";

$conv = $me->getConversationWithUser($he);


echo ($conv->reverse)? "yes": "no";



echo "\n\n---------------------------------------------\n";