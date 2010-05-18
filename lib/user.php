<?php

require_once("lib/db.php");

session_start();

// Set some sensible defaults if we're not logged in
function session_defaults() {
	$_SESSION['logged'] = false;
	$_SESSION['uid'] = 0;
	$_SESSION['username'] = '';
	$_SESSION['email'] = '';
	$_SESSION['cookie'] = 0;
}

//If no UID present (not logged in), set defaults
if(!isset($_SESSION['uid'])) {
	session_defaults();
}

class User {
	var $db = null; 
	var $failed = false; // failed login attempt
	var $id = 0; // the current user's id
	
	function User() {
		$this->db = new DB();
		if($_SESSION['logged']) {
			$this->checkSession();
		} elseif(isset($_COOKIE['oldhatbooks_login'])) {
			$this->_checkRemembered($_COOKIE['oldhatbooks_login']);
		}
		$this->username = $_SESSION['username'];
		$this->email = $_SESSION['email'];
	}
	
	function checkLogin($username, $password, $remember=true) {
		$username = $this->db->quote($username);
		$password = $this->db->quote(md5($password));
		$result = $this->db->login($username,$password);
		if ( count($result) > 0 ) {
			$this->_setSession($result[0], $remember);
			return true;
		} else {
			$this->logout();
			return false;
		}
	}
	
	function logout() {
		$this->failed = true;
		session_defaults();
	}
	
	function _setSession(&$values, $remember=true, $init = true) {
		$this->id = $values->id;
		$_SESSION['uid'] = $this->id;
		$_SESSION['username'] = htmlspecialchars($values->username);
		$_SESSION['cookie'] = $values->cookie;
		$_SESSION['logged'] = true;
		$_SESSION['email'] = $values->email;
		
		if ($remember) {
			$this->updateCookie($values->cookie, true);
		}
		if ($init) {
			$session = $this->db->quote(session_id());
			$ip = $this->db->quote($_SERVER['REMOTE_ADDR']);
			
			$this->db->do_login($session,$ip,$this->id);
		}
	}
	
	function updateCookie($cookie, $save) {
		$_SESSION['cookie'] = $cookie;
		if ($save) {
			$cookie = serialize(array($_SESSION['username'], $cookie) );
			setcookie('oldhatbooks_login', $cookie, time() + 31104000);
		}
	}
	
	function _checkRemembered($cookie) {
		list($username, $cookie) = @unserialize($cookie);
		if (!$username or !$cookie) return;
		$username = $this->db->quote($username);
		$cookie = $this->db->quote($cookie);
		$result = $this->db->get_user($username,$cookie);
		if (count($result) > 0 ) {
			$this->_setSession($result[0], true);
		}
	}
	
	function checkSession() {
		$username = $this->db->quote($_SESSION['username']);
		$cookie = $this->db->quote($_SESSION['cookie']);
		$session = $this->db->quote(session_id());
		$ip = $this->db->quote($_SERVER['REMOTE_ADDR']);
		$result = $this->db->get_user($username,$cookie,$session,$ip);
		if (count($result) > 0 ) {
			$this->_setSession($result[0], false, false);
			return true;
		} else {
			$this->logout();
			return false;
		}
	}
}

?>