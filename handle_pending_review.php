<?php
require_once("lib/user.php");

$db = new DB();

$user = new User();
if($user->checkSession()) {
	$id = $_REQUEST['id'];
	$action = $_REQUEST['action'];
	if($action == "reject") {
		mysql_query("delete from reviews where review_id = $id limit 1");
	} else if($action == "accept") {
		mysql_query("update reviews set approved = 1 where review_id = $id");
		$review = $db->get_review($id);
		$sql = "!";
		if($review->rating == "down") {
			$sql = "UPDATE books set negative_ratings = negative_ratings + 1 where id = 
{$review->bookid}";
		} else {
			$sql = "UPDATE books set positive_ratings = positive_ratings + 1 where id = 
{$review->bookid}";
		}
		echo $db->query($sql);
	}
}
?>
