<?php
require_once("lib/page.php");
require_once("lib/db.php");
require_once("lib/gravatar.php");

$page = new Page("template.html");
$page->title = "Books :: Search";
$page->body = "";
$page->blob = <<<HTM
<p>Want to take a look at what we've got on the shelves, or leave a review for something you've just returned? You can search for books or zines below; just fill in whatever pieces of information you can think of and hit `Search'.</p>
<form method='post' action='results.php'>
<div class="row">
  <div class="label">Author:</div>
  <div class="entry"><input type='text' size='30' id='author' name='author' class='text'/></div>
</div>
<div class="row">
  <div class="label">Title:</div>
  <div class="entry"><input type='text' size='40' id='title' name='title' class='text'/></div>
</div>
<div class="row">
  <div class="label">Section:</div>
  <div class="entry">
    <select name="section">
		<option value=""></option>
		<option value='Journals and Magazines'>Journals &amp; Magazines</option>
		<option value='Ecology and Environmentalism'>Ecology and Environmentalism</option>
		<option value='Biographies'>Biographies</option>
		<option value='Fiction'>Fiction</option>
		<option value='Education'>Education</option>
		<option value='Poetry'>Poetry</option>
		<option value='Nutrition and Cooking'>Nutrition &amp; Cooking</option>
		<option value='Feminism'>Feminism</option>
		<option value='Health'>Health</option>
		<option value='Mental Health'>Mental Health</option>
		<option value='Religion and Spirituality'>Religion &amp; Spirituality</option>
		<option value='Graphic Novels'>Graphic Novels</option>
		<option value='Bicycles'>Bicycles</option>
		<option value='Race'>Race</option>
		<option value='Natural History'>Natural History</option>
		<option value='Nature Guides'>Nature Guides</option>
		<option value='Science'>Science</option>
		<option value='Community'>Community</option>
		<option value='Political Organising'>Political Organising</option>
		<option value='Anthologies'>Anthologies</option>
		<option value='Language'>Language</option>
		<option value='Philosophy'>Philosophy</option>
		<option value='DIY'>DIY</option>
		<option value='National Struggles'>National Struggles</option>
		<option value='Political Theory'>Political Theory</option>
		<option value='War and Conflict'>War &amp; Conflict</option>
		<option value='Youth'>Youth</option>
		<option value='Travel'>Travel</option>
		<option value='Social Analysis'>Social Analysis</option>
		<option value='Global Resistance and Revolt'>Global Resistance &amp; Revolt</option>
		<option value='People's History'>People's History</option>
		<option value='Gender and Equality'>Gender &amp; Equality</option>
    </select>
  </div>
</div>
<div style="float: right;vertical-align:bottom">
  <input type='submit' class='submit' id='next' value='Search' />
</div>
</form>
HTM;

$db = new DB();
$c = 0;
foreach($db->recent_reviews() as $book) {
	// Get single review, or filler text.
	$review = "No reviews posted.";
	if($book->text != "") {
		$review = $book->text;
		if(strlen($review) > 150) {
			$review = substr($review,0,147) . "...";
		}
		$reviewplural = "reviews";
		if($book->reviewcount == 1) {
			$reviewplural = "review";
		}
		$review .= " (<a href='book.php?id={$book->book_id}'>{$book->reviewcount} $reviewplural</a>)";
	}
	// Get gravatar for reviewer, if available
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
	<div class="head"><span class="title"><a href="book.php?id={$book->book_id}">{$book->title}</a></span> by <span class="author"><a href="results.php?author={$book->author}">{$book->author}</a></span></div>
	<div class="$rating_type">$rating<br /><span class="positive">{$book->positive_ratings}</span><span class="zero">/</span><span class="negative">{$book->negative_ratings}</span></div>
	$user_icon
	<p>$review</p>
	<span class="section">{$book->section}</span>
	<div class="clear">&nbsp</div>
</div>
HTM;
	if($book->approved == 1) {
		$results_html .= $html;
	}
}

$page->blob .= "<div style='clear:both'>&nbsp;</div><p>If you don't quite know where to start, these books have been read and reviewed recently:" . $results_html;

echo $page->to_s();

?>
