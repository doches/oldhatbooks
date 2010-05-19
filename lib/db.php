<?php

require_once("lib/post.php");
require_once("lib/event.php");

class DB
{
    public function __construct()
    {
        $this->server = "localhost";
        $this->username = "oldhatbooks";
        $this->password = "451";
        $this->db = "oldhatbooks";
        $this->connect();
    }
    
    private function connect()
    {
        $this->link = mysql_connect($this->server, $this->username, $this->password);
        mysql_select_db($this->db, $this->link);
    }

	private function fetch($sql) {
//		echo "<span style='color:#ddd'>$sql</span><br />";
				
		$results = Array();
		$result = $this->query($sql);
		while ($row = mysql_fetch_object($result)) {
			array_push($results,$row);
		}
		mysql_free_result($result);
		return $results;
	}
	
	public function query($sql) {
		$result = mysql_query($sql);
		if (!$result) {
			//header("location: ${_SERVER['PHP_SELF']}?error=2");
			die("MySQL error: " . mysql_error());
		}
		return $result;
	}
	
	public function add_review($name,$email,$text,$book_id,$rating) {
		$text = addslashes(stripslashes($text));
		$email = addslashes(stripslashes($email));
		$name = addslashes(stripslashes($name));
		$rating = addslashes(stripslashes($rating));
		$book_id = intval($book_id);

		if(strpos($text,"<a") !== false or strpos($email,"<a") !== false or strpos($name,"<a") !== false or strpos($text,"href") !== false or strpos($text,"url") !== false) {
			return 1;
		}

		if($rating != "up" and $rating != "down") {
			$rating = "up";
		}
		
		$sql = "INSERT into reviews (name,email,text,bookid,rating,approved) values ('$name','$email','$text',$book_id,'$rating',0)";

		$result = mysql_query($sql);
		if (!$result) {
	    	return 2;
		}
	}
	
	public function get_review($id) {
		$results = $this->fetch("select * from reviews where review_id = $id limit 1");
		return $results[0];
	}
	
	public function get_recommendations($order,$desc = true) {
		if($desc) {
			$desc = "desc";
		} else {
			$desc = "";
		}
		if($order !="created" and $order != "rating") {
			$order = "rating";
		}
		$sql = "SELECT *,positive_ratings-negative_ratings as rating from recommendations order by $order $desc limit 0,25";
		
		return $this->fetch($sql);
	}
	
	public function vote_recommendation($id,$change) {
		$id = intval($id);
		$change = intval($change);
		
		if($change >= 0) {
			$sql = "UPDATE recommendations set positive_ratings = positive_ratings + 1 where id = $id";
		} else {
			$sql = "UPDATE recommendations set negative_ratings = negative_ratings + 1 where id = $id";
		}
		
		$result = mysql_query($sql);
		if (!$result) {
			return 2;
		} else {
			setcookie("vote_{$id}",1);
			return 0;
		}
	}
	
	public function recommend($title,$author,$reason,$name,$email) {
		$author = addslashes(stripslashes($author));
		$title = addslashes(stripslashes($title));
		$email = addslashes(stripslashes($email));
		$reason = addslashes(stripslashes($reason));
		$name = addslashes(stripslashes($name));

		if(strpos($reason,"<a") !== false or strpos($email,"<a") !== false or strpos($name,"<a") !== false or strpos($author,"<a") !== false or strpos($title,"<a") !== false) {
			return 1;
		}
		
		if(strlen($title)<=1 || strlen($reason)<=1) {
			return 3;
		}
		
		$reason = str_replace("\n","<br />",$reason);
		
		$sql = "INSERT into recommendations (author,title,text,name,email) values ('$author','$title','$reason','$name','$email')";

		$result = mysql_query($sql);
		if (!$result) {
			return 2;
		}
	}
	
	public function add_post($subject,$text,$author,$timestamp) {
		$subject = addslashes(stripslashes($subject));
		$text = addslashes(stripslashes($text));
		// author, timestamp are ints, don't need validation
		
		$sql = "INSERT into posts (subject,text,author_id,date) values ('$subject','$text',$author,'$timestamp')";
		$result = mysql_query($sql);
		if(!$result) {
			return mysql_error() . "!";
			return 2;
		}
		return 0;
	}
	
	public function get_events($limit = 2) {
		$sql = "SELECT events.* from events order by date desc limit $limit";
		$res = $this->fetch($sql);
		$events = Array();
		foreach($res as $list) {
			array_push($events, new Event($list->title,$list->date,$list->location,$list->description,$list->link,$list->id));
		}
		return $events;
	}
	
	public function get_latest_post() {
		$sql = "SELECT posts.*, users.username, users.email FROM posts LEFT JOIN users ON posts.author_id = users.id order by date desc limit 1"; // TODO user join
		$res = $this->fetch($sql);
		$res = $res[0];
		$post = new Post($res->subject,$res->text,$res->username,$res->date,$res->email,$res->id);
		return $post;
	}
	
	public function get_latest_posts($limit=4) {
		$sql = "SELECT posts.*, users.username, users.email FROM posts LEFT JOIN users ON posts.author_id = users.id order by posts.id desc limit $limit";
		$res = $this->fetch($sql);
		$posts = Array();
		foreach($res as $list) {
			array_push($posts, new Post($list->subject,$list->text,$list->username,$list->date,$list->email,$list->id));
		}
		return $posts;
	}

	public function get_post($id) {
//		$sql = "SELECT * from posts where id=$id"; // TODO user join
		$sql = "SELECT posts.*, users.username, users.email FROM posts LEFT JOIN users ON posts.author_id = users.id WHERE posts.id = $id";
		$res = $this->fetch($sql);
		$res = $res[0];
		$post = new Post($res->subject,$res->text,$res->username,$res->date,$res->email,$res->id);
		return $post;
	}
	
	public function login($username,$password) {
		$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
		$res = $this->fetch($sql);
		return $res;
	}
	
	public function do_login($session,$ip,$id) {
		$sql = "UPDATE users SET session = '$session', ip = '$ip' WHERE id = $id";
		$res = mysql_query($sql);
		if(!$res) {
			echo "$sql<br />";
			echo mysql_error();
		}
	}
	
	public function get_user($username,$cookie,$session=false,$ip=false) {
		$sql = "SELECT * FROM users WHERE (username = '$username') AND (cookie = '$cookie')";
		if($session) { $sql .= " AND (session = '$session')"; }
		if($ip) { $sql .= " AND (ip = '$ip')"; }
		return $this->fetch($sql);
	}
	
	public function quote($str) { return addslashes(stripslashes($str)); }

	public function search($author,$title,$section,$email="")
	{
		$author = addslashes(stripslashes($author));
		$title = addslashes(stripslashes($title));
		$email = addslashes(stripslashes($email));
		$section = addslashes(stripslashes($section));
		
		// Build query
		$limit = "limit 25";
		$section_str = "";
		if($section != "") {
			$section_str = "and section = '{$section}'";
		}
		
		// If we're given an email, restrict it to books reviewed by that email, and their reviews.
		$email_limit = "";
		if($email != "") {
			$email_limit = "and reviews.email = '$email'";
		}
		
		$sql = "SELECT *,books.id as book_id,count(reviews.review_id) as reviewcount,books.positive_ratings-books.negative_ratings as rating from books left join reviews on books.id = reviews.bookid where author like '%$author%' and title like '%$title%' $email_limit $section_str group by books.id order by rating desc $limit";
		
		return $this->fetch($sql);
	}
	
	public function recent_reviews($limit=5)
	{
		$sql = "SELECT *,books.id as book_id,count(reviews.review_id) as reviewcount,books.positive_ratings-books.negative_ratings as rating from books left join reviews on books.id = reviews.bookid group by books.id order by reviews.created desc limit $limit";
		
		return $this->fetch($sql);
	}
	
	public function get_pending_reviews() {
		$sql = "select * from books,reviews where books.id = reviews.bookid and approved = 0 order by timestamp limit 50";
		
		return $this->fetch($sql);
	}
	
	public function get_book($id) {
		$id = intval($id);
		$sql = "SELECT *,books.id as book_id from books left join reviews on books.id = reviews.bookid where books.id = $id order by reviews.created";
		
		return $this->fetch($sql);
	}
}
?>
