<?php
require_once("lib/db.php");
require_once("lib/user.php");

$u = new User();
if(!$u->checkSession()) {
	header("location:login.php");
}

$db = new DB();

$subject = $_POST['subject'];
$text = $_POST['text'];

$text = str_replace("__DOUBLEQUOTE__","\"",$text); // Total hack. Eew. Again.
$subject = str_replace("__DOUBLEQUOTE__","\"",$subject);

$stamp = date("Y-m-d H:i:s", time());
$author = $u->id;

//TODO: AJAX rpc
$error = $db->add_post($subject,$text,$author,$stamp);
if(!$error) {
	header("location: index.php");
} else {
	echo $error;
}
?>