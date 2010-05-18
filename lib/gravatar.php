<?php
function gravatar($email) {
	$default = "http://github.com/images/gravatars/gravatar-50.png";
	//$default = "http://www.oldhatbooks.org/images/gravatar.jpg";
	$size = 40;

	$grav_url = "http://www.gravatar.com/avatar.php?
	gravatar_id=".md5( strtolower($email) ).
	"&default=".urlencode($default).
	"&size=".$size.
	"&rating=x";

	$user_icon = "<div class='user_icon'><a href='user.php?email={$email}'><img width=40px height=40px src='$grav_url' alt='{$book->name}' title='{$book->name}' /></a></div>";
	return $user_icon;
}
?>