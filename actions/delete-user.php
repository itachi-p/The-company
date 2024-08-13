<?php

include "../classes/User.php";

$user = new User;

$user->delete_user($_POST);

?>