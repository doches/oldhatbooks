<?php
require_once("lib/db.php");

$db = new DB();

//TODO: verification & error handling
//TODO: AJAX rpc

$change = $_REQUEST['change'];
if($change < 0) {
	$change = -1;
} else {
	$change = 1;
}
$db->vote_recommendation($_REQUEST['id'],$change);
header("location: recommendations.php");
?>