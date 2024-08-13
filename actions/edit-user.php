<?php

include "../classes/User.php";

$user = new User;

print_r($_FILES); #Deb
# Array ( [photo] =>
#   Array ( [name] => wolverine.jpeg
#     [type] => image/jpeg
#     [tmp_name] => /Applications/MAMP/tmp/php/php3pJL9T
#     [error] => 0 [size] => 7817 ) )

$user->update($_POST, $_FILES);

// $_FILES holds the info of the image or file such as name and the actual image or file
// $_FILES is a 2D Associative Array
// $_FILES[''][''];

/*
  $_POST['first_name];
  $_POST['last_name];
  $_POST['username];
*/

?>