<?php

include('classes/DB.php');
include('classes/Login.php');

if(!Login::isLoggedIn()){

    die("Not logged In!");
    
}

if(isset($_POST['confirm'])){

    if(isset($_POST['alldevices'])){

        DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn())); 

    }else{
        
        if(isset($_COOKIE['SNID'])){

           DB::query('DELETE FROM login_tokens WHERE token=:token', array('token'=>sha1($_COOKIE['SNID']))); 
           
        }

        setcookie('SNID', '1', time()-3600);
        setcookie('SNID_', '1', time()-3600);



    }

}

?>

<h1>Log out of your account</h1>
<p>Are you Sure?</p>
<form action="logout.php" method="post">

<input type="checkbox" name="alldevices" value="alldevices" >Log out from all devices?<br>
<input type="submit" name="confirm" value="Confirm">


</form>