<?php
require_once("lib/page.php");
require_once("lib/user.php");

$u = new User();
if(!$u->checkSession()) {
	header("location:login.php");
}

$page = new Page("template.html"); // Admin template? TODO
$page->title = "Admin::Add a new post";
$page->blob = <<<HTM
	<h2>Add a New Post</h2>
	<p>This page lets you add a new post to the front page. Adding a new post automatically archives the current front-page post, which can then be accessed via the 'Older Posts' link. The post title should be plain text, but the body should be in <a href="http://daringfireball.net/projects/markdown/syntax">Markdown</a> format. Basically, write like you would write an email and the formatting *should* turn out OK.</p>
	<form method='post' action='preview_new_post.php'>
	<div class="row">
	  <div class="label">Title:</div>
	  <div class="entry"><input type='text' size='50' id='subject' name='subject' class='text'/></div>
	</div>
	<div class="row">
	  <div class="label">Text (in <a href="http://daringfireball.net/projects/markdown/syntax">Markdown</a> format):</div>
	  <div class="entry">
		<textarea name="text" rows="20" cols="52"></textarea>
	  </div>
	</div>
	<div class="clear">&nbsp;</div>
	<div style="float: right;vertical-align:bottom">
	  <input type='submit' class='submit' id='next' value='Preview &amp; Post' />
	</div>
	<div class="clear">&nbsp;</div>
	</form>
HTM;

echo $page->to_s();

?>
