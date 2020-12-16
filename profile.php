<?php

include('classes/DB.php');
include('classes/Login.php');
include('classes/Post.php');

$username = "";
$verified = FALSE;
$isfollowing = FALSE;

if (isset($_GET['username'])) {

    if (DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))) {

        $username = DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['verified'];
        $followerid = Login::isLoggedIn();
        if (isset($_POST['follow'])) {
            if ($userid != $followerid) {
                if (!DB::query('SELECT follower_id FROM follower WHERE user_id = :userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
                    if ($followerid == 8) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array('userid' => $userid));
                    }
                    DB::query('INSERT INTO follower VALUES (\'\', :userid, :followerid)', array(':userid' => $userid, ':followerid' => $followerid));
                } else {
                    echo 'Alredy following';
                }
                $isfollowing = TRUE;
            }
        }
        if (isset($_POST['unfollow'])) {
            if ($userid != $followerid) {
                if (DB::query('SELECT follower_id FROM follower WHERE user_id = :userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
                    if ($followerid == 8) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array('userid' => $userid));
                    }
                    DB::query('DELETE FROM follower WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid));
                }
                $isfollowing = FALSE;
            }
        }
        if (DB::query('SELECT follower_id FROM follower WHERE user_id = :userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
            //echo 'Alredy following';
            $isfollowing = TRUE;
        }
        if (isset($_POST['post'])) {

            Post::createPost($_POST['postbody'], login::isLoggedIn(), $userid);
        }

        if (isset($_GET['postid'])) {
           Post::likePost($_GET['postid'], $followerid);
        }
        $posts = Post::DisplayPost($userid, $username, $followerid);
    } else {
        die('User not found!!');
    }
}
?>

<h1> <?php echo $username; ?> Profile <?php if (!$verified) {
                                            echo ' - Verified';
                                        } ?> </h1>

<form action="profile.php?username=<?php echo $username; ?>" method="post">

    <?php

    if ($userid != $followerid) {

        if ($isfollowing) {

            echo '<input type="submit" name="unfollow" value="Unfollow">';
        } else {

            echo '<input type="submit" name="follow" value="Follow">';
        }
    }

    ?>

</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post">

    <textarea name="postbody" id="" cols="80" rows="8"></textarea>
    <input type="submit" name="post" value="Post">

</form>


<div class="posts">

    <?php echo $posts; ?>

</div>

