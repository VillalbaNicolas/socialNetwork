<?php

include('classes/DB.php');
include('classes/Login.php');

if (Login::isLoggedIn()) {

$userid = Login::isLoggedIn();
    $showTimeline = true;

} else {

 die('Not Logged In') ;
}


if(isset($_POST['uploadprofileimage'])){

    
    $image = base64_encode( file_get_contents( $_FILES['profileimage']['tmp_name']));
  
    $options = array('http'=>array(
        'method'=>"POST",
        'header'=>"Authorization: Bearer 037fa02abf30a2ae8852e241752898946d3f7ae4\n".
        "Content-Type: application/x-www-form-urlencoded",
        'content'=>$image
    ));
    $context = stream_context_create($options);
    $imageURL= "https://api.imgur.com/3/image";

    if($_FILES['profileimage']['size'] > 10240000 ){

        die('Image too big!');

    }
    
    $response = file_get_contents($imageURL, false, $context);
    $response = json_decode($response);
    echo $response->data->link;


    DB::query('UPDATE users SET profileimg = :profileimg WHERE id = :userid', array(':profileimg'=>$response->data->link, ':userid'=>$userid));
    
/*
https://imgur.com/#access_token=037fa02abf30a2ae8852e241752898946d3f7ae4&expires_in=315360000&token_type=bearer&refresh_token=4624a6f42349dd475ecff1ccd5b4825a1afa3443&account_username=LordVizar&account_id=141925491

access_token    037fa02abf30a2ae8852e241752898946d3f7ae4

refresh_token   4624a6f42349dd475ecff1ccd5b4825a1afa3443
*/
}

?>

<h1>My Account</h1>
<form action="my-account.php" method="POST" enctype="multipart/form-data">

Upload a profile image:
<input type="file" name="profileimage"><br><br>
<input type="submit" name="uploadprofileimage" value="Upload Image">


</form>
