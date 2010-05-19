<?php
require_once("lib/page.php");
require_once("lib/db.php");

$db = new DB();
$page = new Page("template.html");
$page->title = "Welcome to Old Hat Books";
$page->body = "onload='onLoad();'";
$page->head = <<<HTM
  <script type="text/javascript">
    function onLoad() {
      $('search').value = "...by title, author, or keyword...";
    }
    
    function maybeClearSearch() {
      if($('search').value == "...by title, author, or keyword...") {
        $('search').value = "";
      }
    }
  </script>
HTM;
$page->blob = <<<HTM
      <div class="search_info">
<!--        <img src="images/search.png" alt="Search Icon" /> -->
        <div class="box" id="left">
          <h1>Search for a book</h1>
          <form method="get" action="search.php">
            <input type="text" name="search" size=30 id="search" value="" onClick="maybeClearSearch();"/>
            <input type="submit" id="submit" name="Search" class="submit" value="Search"/>
          </form>
        </div>
        <div class="box">
          <h1>Or</h1>
          <form method="get" action="browse.php">
            <input type="submit" id="submit" name="Browse by Category" value="Browse by Category" class="submit" />
          </form>
        </div>
        <div class="prefooter">&nbsp;</div>
      </div>
      <div class="main_content">
        <h1 class="section">News &amp; Blatherings</h1>
HTM;
foreach($db->get_latest_posts(3) as $post) {
	$page->blob .= <<<HTM
	        <div class="post">
	        	{$post->toHTML()}
	        </div>
HTM;
}
$page->blob .= <<<HTM
	      <div class="rss_sidebar" id="rss_sidebar">
	        <img src="images/rss.png" alt="Atom feed" />
	      </div>
      </div>
      <div class="events">
        <h1 class="section">Events</h1>
HTM;


foreach($db->get_events() as $event) {
	$page->blob .= <<<HTM
        <div class="event">
          {$event->toHTML()}
        </div>
HTM;
}

$page->blob .= <<<HTM
      </div>
HTM;

echo $page->to_s();

?>
