<?php
require_once("lib/db.php");

$db = new DB();

//TODO: verification & error handling
//TODO: AJAX rpc

$error = $db->add_review($_REQUEST['name'],$_REQUEST['email'],$_REQUEST['text'],$_REQUEST['book_id'],$_REQUEST['rating']);
header("location: book.php?id={$_REQUEST['book_id']}&error=$error&msg=moderation");
?>