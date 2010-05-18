<?php
require_once("lib/page.php");
require_once("lib/db.php");

// Do search, and render the results.
$db = new DB();
$results_html = "";
$c = 0;
foreach($db->search($_REQUEST['author'],$_REQUEST['title'],$_REQUEST['section'],$_REQUEST['email']) as $book) {
	// Get single review, or filler text.
	$review = "No reviews posted.";
	if($book->text != "") {
		$review = $book->text;
		if(strlen($review) > 250) {
			$review = substr($review,0,247) . "...";
		}
		$reviewplural = "reviews";
		if($book->reviewcount == 1) {
			$reviewplural = "review";
		}
	}
	// Get gravatar for reviewer, if available
	$user_icon = "";
	if($book->text != "") {
		$default = "images/user_icon.jpg";
		$size = 40;

		$grav_url = "http://www.gravatar.com/avatar.php?
		gravatar_id=".md5( strtolower($book->email) ).
		"&default=".urlencode($default).
		"&size=".$size;
		
		$user_icon = "<div class='user_icon'><img src='$grav_url' alt='{$book->name}' title='{$book->name}' /></div>";
	}
	// Compute rating & rating color (red/green)
	$rating = intval($book->positive_ratings) - intval($book->negative_ratings);
	$rating_type = "rating_zero";
	if($rating < 0) {
		$rating_type = "rating_negative";
	} else if($rating > 0) {
		$rating_type = "rating_positive";
	}
	// Alternate background via div class swap
	$bookstyle = "book_b";
	if($c % 2 == 0) {
		$bookstyle = "book_a";
	}
	$c += 1;
	$html = <<<HTM
<div class="$bookstyle">
	<div class="head"><span class="title"><a href="book.php?id={$book->id}">{$book->title}</a></span> by <span class="author"><a href="results.php?author={$book->author}">{$book->author}</a></span></div>
	<div class="$rating_type">$rating<br /><span class="positive">{$book->positive_ratings}</span><span class="zero">/</span><span class="negative">{$book->negative_ratings}</span></div>
	$user_icon
	<p>$review</p>
</div>
HTM;
	$results_html .= $html;
}

$page = new Page("template.html");
$page->title = "Books :: Reviews";
$page->body = "";
$page->blob = <<<HTM
<h2>Search Results</h2>
{$results_html}
HTM;

echo $page->to_s();

?>
