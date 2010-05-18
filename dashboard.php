<?php
require_once("lib/page.php");
require_once("lib/user.php");

$user = new User();
if(!$user->checkSession()) {
	header("location:login.php?redirect=dashboard");
} else {
	$db = new DB();
	$page = new Page("template.html"); // Admin template? TODO
	$page->title = "Volunteer Dashboard";
	$page->head = <<<HTM
	<link rel="stylesheet" media="screen" type="text/css" href="styles/volunteer.css" />
	<script src="scripts/prototype.js" type="text/javascript" ></script>
	<script src="scripts/volunteer.js" type="text/javascript" ></script>
HTM;
	$page->blob = <<<HTM
	<h2>Volunteer Home</h2>
	<p>So, umm, hi. If you've logged in here you're probably a volunteer for the Old Hat Books library, and there are a couple of things you might want to do. Most of them probably aren't supported yet, but if you're looking to update the front page with a news post or moderate pending book reviews you've come to the right place!</p>
	<h3>News</h3>
	<p><a href="new_post.php" class="button">Update the front page</a></p>
	<h3>Reviews</h3>
HTM;
	
	foreach($db->get_pending_reviews() as $review) {
		$item = <<<HTM

		<div class="pending_review" id="pending_{$review->review_id}">
			<h1>
				<span class="title">{$review->title}</span> by <span class="author">{$review->author}</span>
				<a class="delete" href="#" onclick="reject({$review->review_id})"><img src="images/delete.png" title="Reject" alt="[delete]" /></a>
			</h1>
			<p><span class="name">{$review->name}</span> (<span class="email">{$review->email}</span>) writes:</p>
			<blockquote>{$review->text}</blockquote>
			<div class="approve"><a href="#" onclick="approve({$review->review_id})">Approve</a></div>
		</div>
HTM;
		$page->blob .= $item;
	}
	$page->blob .= "<div class='logout'><a href='login.php?logout'>Logout</a></div>";
	
	echo $page->to_s();
}

?>