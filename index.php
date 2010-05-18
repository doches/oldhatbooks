<?php
require_once("lib/page.php");
require_once("lib/db.php");

$db = new DB();
if($_REQUEST['post'] > -1) {
	$post = $db->get_post($_REQUEST['post']);
} else {
	$post = $db->get_latest_post();
}
$first_post = $db->get_post(0);
$page = new Page("template.html");
$page->title = "Welcome to Old Hat Books";
$page->blob = $post->toHTML();

if($post->id > 0) {
	$id = $post->id-1;
	$page->blob .= $first_post->toHTML(false);
	$page->blob .= <<<HTM
<div style="float:right"><a href="index.php?post={$id}">Older Posts >></a></div>
HTM;
}

$page->blob .= <<<HTM
<div style="text-align: center; clear: both"><img src="images/flourish.gif" alt="End of Page" /></div>
HTM;

echo $page->to_s();

?>
