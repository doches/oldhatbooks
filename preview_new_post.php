<?php
require_once("lib/page.php");
require_once("lib/post.php");
require_once("lib/user.php");

$u = new User();
if(!$u->checkSession()) {
	header("location:login.php");
}

$subject = $_POST['subject'];
$text = $_POST['text'];
$stamp = date("Y-m-d H:i:s", time());
$author = $u->username;
$email = $u->email;

$post = new Post($subject,$text,$author,$stamp,$email);

$stext = str_replace("\"","__DOUBLEQUOTE__",$text); // Total hack. Eew.
$ssubject = str_replace("\"","__DOUBLEQUOTE__",$subject);

$page = new Page("template.html"); // Admin template? TODO
$page->title = "Admin::Preview Post";
$page->blob = <<<HTM
{$post->toHTML()}
 	<div class="clear">&nbsp;</div>
	<form method="POST" action="do_post.php">
	<input type="hidden" name="subject" value="{$ssubject}" />
	<input type="hidden" name="text" value="{$stext}" />
	<div style="float: right;vertical-align:bottom">
	  <input type='submit' class='submit' id='next' value='Publish' />
	</div>
	</form>
HTM;

echo $page->to_s();

?>
