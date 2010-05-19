<?php
require_once("lib/page.php");
require_once("lib/db.php");

$db = new DB();
$page = new Page("template.html");
$page->title = "Welcome to Old Hat Books";
$page->blob = <<<HTM
      <div class="main_content">
HTM;
foreach($db->get_latest_posts(3) as $post) {
	$page->blob .= <<<HTM
	        <div class="post">
	        	{$post->toHTML()}
	        </div>
HTM;
}
$page->blob .= <<<HTM
      </div>
      <div class="events">
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
