<?php
require_once("lib/db.php");

$db = new DB();

//TODO: verification & error handling
//TODO: AJAX rpc
$error = $db->recommend($_REQUEST['title'],$_REQUEST['author'],$_REQUEST['text'],$_REQUEST['name'],$_REQUEST['email']);
header("location: recommendations.php?error=$error");
?>