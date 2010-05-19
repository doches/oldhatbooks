<?php
require_once("lib/markdown.php");
require_once("lib/smartypants.php");

class Event {
	function Event($title,$date,$location,$description,$link,$id=0) {
		$this->title = $title;
		$this->location = $location;
		$this->description = SmartyPants(Markdown($description));
		$this->link = $link;
		$this->id = $id;
		
		$dateTimeZoneUK = new DateTimeZone("BST");
		$dateTimeUK = new DateTime("now",$dateTimeZoneUK);
		$offset = $dateTimeUK->getOffset();
		$this->date = date("l j F Y \a\\t H:i",strtotime($date)+$offset);
	}
	
	public function toHTML($show_byline = true) {
		return <<<HTM
<span class="title">{$this->title}</span>
<em>What:</em> 
<span class="description">{$this->description}</span>
<em>When:</em> 
<span class="date">{$this->date}</span>
<em>Where:</em> 
<span class="location">{$this->location}</span>
<a class="facebook" href="{$this->link}"><img src="images/facebook.png" alt="facebook" /> See on Facebook</a>
HTM;
	}
}

?>
