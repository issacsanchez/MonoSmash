<?php
include('./classes/DB.php');
include('./classes/TESTLOGIN.php');
if (!TESTLOGIN::isLoggedIn()) {
  die("Not Logged In");
}
else {
	if (isset($_COOKIE['SNID'])) {
		DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token' => sha1($_COOKIE['SNID'])));
		header('Location: http://localhost/monosmash/main.php');
	}
	setcookie('SNID', '1', time() - 3600);
	setcookie('SNID_', '1', time() - 3600);
	header("Location: http://localhost/monosmash/main.php");
}
?>

