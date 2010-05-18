<?php
require_once("lib/page.php");
require_once("lib/user.php");

// TODO: Will someone please fix this enormous hack before release?
$redirect_prefix = "";//"http://kornbluth.local/oldhatbooks/";

$user = new User();
if($_REQUEST['username']) {
	if($user->checkLogin($_REQUEST['username'],$_REQUEST['password'])) {
		if($_REQUEST['redirect']!="") {
			header("location:{$redirect_prefix}{$_REQUEST['redirect']}.php");
		} else {
			header("location:{$redirect_prefix}dashboard.php");
		}
	}
	print_r($_REQUEST);
	
	echo "ATTEMPT";
} else if(isset($_REQUEST['logout'])) {
	$user->logout();
}

if($user->checkSession()) {
	if($_REQUEST['redirect']) {
		header("location:{$redirect_prefix}{$_REQUEST['redirect']}.php");
	} else {
		header("location:{$redirect_prefix}dashboard.php");
	}
}
$page = new Page("template.html"); // Admin template? TODO
$page->title = "Volunteer Login";
$page->blob = <<<HTM
	<h2>Login</h2>
	<form method='post' action='login.php'>
	<div class="row">
	  <div class="label">Username:</div>
	  <div class="entry"><input type='text' size='30' id='username' name='username' class='text'/></div>
	</div>
	<div class="row">
	  <div class="label">Password:</div>
	  <div class="entry"><input type='password' size='30' id='password' name='password' class='text'/></div>
	</div>
	<div class="clear">&nbsp;</div>
	<div style="float: right;vertical-align:bottom">
	  <input type='submit' class='submit' id='next' value='Login' />
	</div>
	<div class="clear">&nbsp;</div>
	<input type=hidden name='redirect' value='{$_REQUEST['redirect']}' />
	</form>
HTM;
echo $page->to_s();

?>
