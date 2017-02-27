<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 09/02/2017
 * Time: 11:31 AM
 */

include("functionality.php");

$user = User::withUsername("admin");

$founded = $user->getAllConversations();

