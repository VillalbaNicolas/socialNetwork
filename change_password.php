<?php

include('classes/DB.php');
include('classes/Login.php');



if (Login::isLoggedIn()) {

    if (isset($_POST['changepassword'])) {

        $oldpassword = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];
        $newpasswordrepeat = $_POST['newpasswordrepeat'];
        $userid = LOGIN::isLoggedIn();

        if (password_verify($oldpassword, DB::query('SELECT pass FROM users WHERE id=:userid', array(':userid' => $userid))[0]['pass'])) {

            if ($newpassword == $newpasswordrepeat) {

                if (strlen($newpassword) >= 6 && strlen($newpassword) <= 50) {

                    DB::query('UPDATE users SET pass=:newpassword WHERE id=:userid', array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':userid' => $userid));
                    echo 'Password change succesfuly!!';
                } else {

                    echo 'Invalid password!!';
                }
            } else {
                echo 'Password dont match!';
            }
        } else {
            echo 'Incorrect old password!!';
        }
    }

} else {

    $tokenIsValid = FALSE;

    if (isset($_GET['token'])) {

        $token = $_GET['token'];

        if (DB::query('SELECT user_id FROM password_tokens WHERE token = :token', array(':token'=> sha1($token)))) {

            $userid = DB::query('SELECT user_id FROM password_tokens WHERE token = :token', array(':token'=> sha1($token)))[0]['user_id'];
            
            $tokenIsValid = TRUE;

            if (isset($_POST['changepassword'])) {

                $newpassword = $_POST['newpassword'];
                $newpasswordrepeat = $_POST['newpasswordrepeat'];

                if ($newpassword == $newpasswordrepeat) {

                    if (strlen($newpassword) >= 6 && strlen($newpassword) <= 50) {

                        DB::query('UPDATE users SET pass = :newpassword WHERE id=:userid', array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':userid' => $userid));
                        
                        echo 'Password change succesfuly!!';
                    
                    } else {

                        echo 'Invalid password!!';
                    }
                
                } else {
                    echo 'Password dont match!';
                }
            }
        
        } else {

            die('Token invalid!');
        }
   
    } else {

        die('Not Logged In');
    }
}

?>

<h1>Change your password</h1>
<form action="<?php if(!$tokenIsValid){echo 'change_password.php';}else{echo'change_password.php?token='.$token.'';}  ?>" method="post">
    <?php if(!$tokenIsValid) {echo '<input type="password" name="oldpassword" value="" placeholder="Current password..."><p />';} ?>
    <input type="password" name="newpassword" value="" placeholder="New password...">
    <p />
    <input type="password" name="newpasswordrepeat" value="" placeholder="Repeat new password...">
    <p />
    <input type="submit" name="changepassword" value="Change Password">

</form>