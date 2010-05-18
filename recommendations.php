<?php
require_once("lib/page.php");
require_once("lib/db.php");
require_once("lib/gravatar.php");

// Do search, and render the results.
$db = new DB();
$results_html = "";
$c = 0;
foreach($db->get_recommendations("rating") as $book) {

	// Get gravatar for suggester, if available
	$user_icon = "";
	if($book->text != "") {
		$user_icon = gravatar($book->email);
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
	<div class="head"><span class="title">{$book->title}</span> by <span class="author">{$book->author}</span></div>
	<div class="$rating_type">$rating<br /><span class="positive">{$book->positive_ratings}</span><span class="zero">/</span><span class="negative">{$book->negative_ratings}</span></div>
	$user_icon
	<p>{$book->text}</p>
HTM;
	if($book->name != "") {
		$html .= "\t<p><em>Recommended by {$book->name}</em></p>";
	}
	$html .= <<<HTM
	<div class="vote">
HTM;
	if($_COOKIE["vote_{$book->id}"]) {
		$html .= "Thanks for voting!";
	} else {
		$html .= <<<HTM
<a href="vote.php?id={$book->id}&change=1" class="vote_up"><img src="images/up.png" alt="[+]" />Vote Up</a> / <a href="vote.php?id={$book->id}&change=-1" class="vote_down"><img src="images/down.png" alt="[-]" />Down</a>
HTM;
	}
	$html .= <<<HTM
</div>
</div>
HTM;
	$results_html .= $html;
}

$results_html .= <<<HTM
 	<div class="clear">&nbsp;</div>
	<p>Got something you think we ought to have? Add your recommendation:</p>
	<form method='post' action='post_recommendation.php'>
	<div class="row">
	  <div class="label">Your Name:</div>
	  <div class="entry"><input type='text' size='20' id='name' name='name' class='text'/></div>
	</div>
	<div class="row">
	  <div class="label">Email:</div>
	  <div class="entry"><input type='text' size='20' id='email' name='email' class='text'/> (optional, but if you have a <a href="http://www.gravatar.com">Gravatar</a> account, use that)</div>
	</div>
	<div class="row">
	  <div class="label">Title:</div>
	  <div class="entry"><input type='text' size='30' id='title' name='title' class='text'/></div>
	</div>
	<div class="row">
	  <div class="label">Author:</div>
	  <div class="entry"><input type='text' size='20' id='author' name='author' class='text'/> (or anything else that will help us find it)</div>
	</div>
	<div class="row">
	  <div class="label">Tell us why we need it:</div>
	  <div class="entry">
		<textarea name="text" rows="3" cols="35"></textarea>
	  </div>
	</div>
	<div style="float: right;vertical-align:bottom">
	  <input type='submit' class='submit' id='next' value='Recommend' />
	</div>
	<div class="clear">&nbsp;</div>
	</form>
HTM;

// Display error message, if we have one.
if($_REQUEST['error']) {
	require_once("lib/errors.php");
	$error_text = "<div class='error'>{$errors[$_REQUEST['error']]}</div>";
}

$page = new Page("template.html");
$page->title = "Recommendations";
$page->body = "";
$page->blob = <<<HTM
<h2>Recommendations</h2>
$error_text
{$results_html}
<div class="clear">&nbsp;</div>
HTM;

echo $page->to_s();

?>
