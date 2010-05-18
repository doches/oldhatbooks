<?php
require_once("lib/markdown.php");
require_once("lib/smartypants.php");

class Post {
	function Post($subject,$text,$author,$timestamp,$email,$id=0) {
		$this->subject = SmartyPants($subject);
		$this->text = SmartyPants(Markdown($text));
		$this->author = $author;
		$this->timestamp = $timestamp;
		$this->email = $email;
		$this->id = $id;
		
		$dateTimeZoneUK = new DateTimeZone("BST");
		$dateTimeUK = new DateTime("now",$dateTimeZoneUK);
		$offset = $dateTimeUK->getOffset();
		$this->date = date("j F, Y g:ia",strtotime($this->timestamp)+$offset);
	}
	
	public function toHTML($show_byline = true) {
		$byline = "<div class=\"tagline\">By {$this->author} on {$this->date}</div>";
		if(!$show_byline) {
			$byline = "";
		}
		return <<<HTM
<h1>{$this->subject}</h1>
{$byline}
{$this->text}
HTM;
	}
}

?>
