<?php

include('classes/DB.php');

if (isset($_POST['createaccount'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

        if (strlen($username) >= 3 && strlen($username) <= 25) {

            if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

                if (strlen($password) >= 6 && strlen($password) <= 50) {

                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                        if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email' => $email))) {

                            DB::query('INSERT INTO users VALUES (\'\', :username, :pass, :email, \'\')', array(':username' => $username, ':pass' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));
                            echo "Success!!";
                        } else {
                            echo 'email in use!!';
                        }
                    } else {
                        echo 'Invalid email!!';
                    }
                } else {
                    echo 'Invalid password!!';
                }
            } else {
                echo 'Invalid username!!';
            }
        } else {
            echo 'Invalid username!!';
        }
    } else {
        echo 'User alredy exist!!';
    }
}

?>

<h1>Register Account</h1>
<form action="create_account.php" method="post">
    <input type="text" name="username" value="" placeholder="Username..."></p>
    <input type="password" name="password" value="" placeholder="Password..."></p>
    <input type="email" name="email" value="" placeholder="Email..."></p>
    <input type="submit" name="createaccount" value="Create Account">

</form>