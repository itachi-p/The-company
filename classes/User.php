<?php

/*
include
include_once

require
require_once
*/

require_once "Database.php";

class User extends Database
{
  // store() - Insert record to DB
  public function store($request) // Generally a variable named $request is used.
  {
    $first_name = $request['first_name'];
    $last_name = $request['last_name'];
    $username = $request['username'];
    $password = $request['password'];

    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, last_name, username, password)
          VALUES ('$first_name', '$last_name', '$username', '$password')";

    if ($this->conn->query($sql)) {
      header('location: ../views'); // go to automatically index.php or the login page
      exit;   //same as die;
    } else {
      die('Error creating the user: ' . $this->conn->error);
    }
  }

  // login() - Check the users data to DB
  public function login($request)
  {
    $username = $request['username'];
    $password = $request['password'];

    // Check that the user information entered matches the existing data in the DB.
    $sql = "SELECT * FROM users WHERE username = '$username'";

    $result = $this->conn->query($sql);

    # Verify the username exists.
    if ($result->num_rows == 1) {
      $user = $result->fetch_assoc();

      // #Deb (If you don't comment out the header('location:), it will disappear instantly with the screen transition.)
      // echo "num_rows: $result->num_rows";
      // echo "<br>result:";
      // print_r($result);
      // mysqli_result Object
      // ( [current_field] => 0 [field_count] => 6 [lengths] => Array ( [0] => 1 [1] => 6 [2] => 9 [3] => 8 [4] => 60 [5] => 0 ) [num_rows] => 1 [type] => 0 )
      // echo "<br>user:";
      // print_r($user);
      // Array
      // ( [id] => 1 [first_name] => Taichi [last_name] => Shinohara [username] => itachi-P [password] => $2y$10$M7Td95E57nhmSt.A12vB6efot1Fw2u2./Et5NrBkOOi9UJVZTffNu [photo] => )

      # check the password if correct
      if (password_verify($password, $user['password'])) {
        # Create session variables for future use.
        session_start();
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];

        // Successful login and screen transition.
        header('location: ../views/dashboard.php');
        exit;
      } else {
        die('Password is incorrect!');
      }
    } else {
      die('Username not found.');
    }
  }

  // logout()
  // Discard the session data. If you don't explicitly destroy the data, it is easy to have problems later, such as unexpected behavior.)
  public function logout()
  {
    session_start(); // This is also necessary. If without this, unset or destroy is invalid.
    session_unset(); // Initialize all attributes in the session. (double check)
    session_destroy(); // Destroying the session itself

    header('location: ../views');
    exit;
  }

  // getAllUsers()
  public function getAllUsers()
  {
    $sql = "SELECT id, first_name, last_name, username, photo FROM users";

    if ($result = $this->conn->query($sql)) {
      return $result;
    } else {
      die('Error retrieving all users: ' . $this->conn->error);
    }
  }

  // getUser() - get single (login) user's information
  public function getUser()
  {
    $id = $_SESSION['id'];

    $sql = "SELECT first_name, last_name, username, photo FROM users WHERE id = $id";

    if ($result = $this->conn->query($sql)) {
      return $result->fetch_assoc();
    } else {
      die('Error retrieving the user: ' . $this->conn->error);
    }
  }

  // update() - login user's profile
  public function update($request, $files)
  {
    session_start();
    $id = $_SESSION['id'];
    $first_name = $request['first_name'];
    $last_name = $request['last_name'];
    $username = $request['username'];
    $photo = $_FILES['photo']['name']; // holds the name of image
    $tmp_photo = $_FILES['photo']['tmp_name']; // holds the actual image from temporary storage

    // ['photo'] is the name of the form input file
    // ['name] is the actual name of the image
    // ['tmp_name'] is the temporary storage of the image

    $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id";

    if ($this->conn->query($sql)) {
      $_SESSION['username'] = $username;
      $_SESSION['full_name'] = "$first_name $last_name";

      # If there is an upload photo, save it to the db and save the file to images folder.
      if ($photo) {
        $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
        $destination = "../assets/images/$photo"; // Local storage location

        // Save the image name to db
        if ($this->conn->query($sql)) {
          // Save the file to images folder
          if (move_uploaded_file($tmp_photo, $destination)) {
            header('location: ../views/dashboard.php');
            exit;
          } else {
            die('Error moving the photo');
          }
        } else {
          die('Error saving photo name: ' . $this->conn->error);
        }
      }
      header('location: ../views/dashboard.php');
      exit;
    } else {
      die('Error updating your account: ' . $this->conn->error);
    }
  }

  // delete() - delete current user & logout (session destroy) immediately.
  public function delete_user($request)
  {
    session_start();
    $id = $_SESSION['id'];

    $sql = "DELETE FROM users WHERE id = $id";

    // Delete current user from DB. and then logout with destroy session.
    if ($this->conn->query($sql)) {
      // session_unset();
      // session_destroy();
      // header('location: ../views');
      $this->logout(); //TODO: The start of the session is duplicated.
      exit;
    } else {
      die('Error deleting your account.');
    }
  }
}
