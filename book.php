<?php
require_once("lib/page.php");
require_once("lib/db.php");
require_once("lib/gravatar.php");

// Do search, and render the results.
$db = new DB();
$reviews = $db->get_book($_REQUEST['id']);
$book = $reviews[0];

// Compute rating & rating color (red/green)
$rating = intval($book->positive_ratings) - intval($book->negative_ratings);
$rating_type = "rating_zero";
if($rating < 0) {
	$rating_type = "rating_negative";
} else if($rating > 0) {
	$rating_type = "rating_positive";
}

// Post a review form
$review_text = "";
$total_reviews = 0;
$pending_reviews = 0;
if(count($reviews) > 0 and $reviews[0]->text != "") {
	$c = 0;
	foreach($reviews as $review) {
		$gravatar = gravatar($review->email);
		$reviewtype = "review_a";
		if($c % 2 == 1) {
			$reviewtype = "review_b";
		}
		$c += 1;
		$user = $review->name;
		if($review->email != "") {
			$user = "<a href='user.php?email={$review->email}'>{$review->name}</a>";
		}
		if($review->approved == 1) {
			$total_reviews += 1;
			$review_text .= <<<HTM
<div class="$reviewtype">
	$gravatar
	<p><span class="user">$user</span> said &#8220;{$review->text}&#8221;</p>
	<div class="clear">&nbsp;</div>
</div>
HTM;
		} else {
			$pending_reviews += 1;
		}
	}
}
if($total_reviews == 0) {
	$review_text .= "<p><em>No reviews available.</em></p>";
}
if($pending_reviews != 0) {
	$plural = "review is";
	if($pending_reviews > 1) {
		$plural = "reviews are";
	}
	$review_text .= "<div class='warning'>{$pending_reviews} {$plural} hidden and awaiting moderation by a volunteer.</div>";
}
if(!$_REQUEST['msg']=="moderation") {
	$review_text .= <<<HTM
	<p>What? You totally disagree? Add your own review:</p>
	<form method='post' action='post_review.php'>
	<div class="row">
	  <div class="label">Name:</div>
	  <div class="entry"><input type='text' size='20' id='author' name='name' class='text'/></div>
	</div>
	<div class="row">
	  <div class="label">Email:</div>
	  <div class="entry"><input type='text' size='20' id='title' name='email' class='text'/> (optional, but if you have a <a href="www.gravatar.com">Gravatar</a> account, use that)</div>
	</div>
	<div class="row">
	  <div class="label">Review:</div>
	  <div class="entry">
		<textarea name="text" rows="3" cols="35"></textarea>
	  </div>
	</div>
	<div class="row">
	  <div class="label">Rating:</div>
	  <div class="entry">
		<input type="radio" name="rating" value="up" checked /> <span class="positive">+1</span>
		<input type="radio" name="rating" value="down" /> <span class="negative">-1</span>
	  </div>
	</div>
	<div style="float: right;vertical-align:bottom">
	  <input type='submit' class='submit' id='next' value='Submit Review' />
	</div>
	<div class="clear">&nbsp;</div>
	<input type="hidden" name="book_id" value="{$book->book_id}" />
	</form>
HTM;
}
// Display error message, if we have one.
$error_text = "";
if($_REQUEST['error']) {
	require_once("lib/errors.php");
	$error_text .= "<div class='error'>{$errors[$_REQUEST['error']]}</div>";
}
if($_REQUEST['msg'] == "moderation") {
	$error_text .= "<div class='warning'><strong>Hooray!</strong> Your review was accepted and is awaiting approval by a moderator.</div>";
}

$page = new Page("template.html");
$page->title = "Books :: {$book->title}";
$page->body = "";
$page->blob = <<<HTM
$error_text
<div class="book_a">
	<div class="head"><span class="title">{$book->title}</span> by <span class="author"><a href="results.php?author={$book->author}">{$book->author}</a></span></div>
	<div class="$rating_type">$rating<br /><span class="positive">{$book->positive_ratings}</span><span class="zero">/</span><span class="negative">{$book->negative_ratings}</span></div>
	<span class="section_large"><span class="in">in</span> {$book->section}</span>
	$review_text
</div>
HTM;

echo $page->to_s();

?>
