<?php
include('/var/www/html/monosmash/classes/DB.php');
include('/var/www/html/monosmash/classes/TESTLOGIN.php');
session_start();

	require "/var/www/html/monosmash/twitter/vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;
	session_start();
	$config = require '/var/www/html/monosmash/twitter/config_twitter.php';
	$userid = TESTLOGIN::isLoggedIn();

		function hasTwitter() {
			global $userid;
			if(DB::query('SELECT user_id FROM twitter WHERE user_id=:userid', array(':userid' => $userid))) {
				return true;
			} else {
				return false;
			}
		}
		function GetTwitterObject($config, $creds) {
			$connection = new TwitterOAuth(
				$config['consumer_key'],
				$config['consumer_secret'],
				$_SESSION['oauth_token'],
				$_SESSION['oauth_token_secret']
			);
			$oauth_verifier = $_SESSION['oauth_verifier'];
			// request user token
			$token = $connection->oauth('oauth/access_token', array("oauth_verifier" => $oauth_verifier));
			// connect with user token
			$twitter = new TwitterOAuth(
				$config['consumer_key'],
				$config['consumer_secret'],
				$_SESSION['oauth_token'],
				$_SESSION['oauth_token_secret']
			);
			return $twitter;
		}
		//Returns array of embeded html posts
		function GetFeedHTML($twitter, $count) {
			$statustimelineparams = ['count' => $count, "trim_user" => false, "maxwidth" => 220, 'omit_script' => true];
			$statustimeline = $twitter->get('statuses/home_timeline', $statustimelineparams);
			$embedhtml = [];
			foreach($statustimeline as $item) {
				$url = "https://twitter.com/" . $item->user->screen_name . "/status" . "/" . $item->id_str;
				$data = $twitter->get('statuses/oembed', ["url" => $url]);
				$embedhtml[] = $data->html;
			}
			return $embedhtml;
		}
		function LoadTwitterCredentials() {
			global $userid;
			$sql_creds = DB::query('SELECT * FROM twitter WHERE user_id=:userid', array(':userid' => $userid));
			$tw_ot = $sql_creds[0]['tw_oauth_token'];
			$tw_ots = $sql_creds[0]['tw_oauth_token_secret'];
			$tw_otv = $sql_creds[0]['tw_oauth_token_verifier'];
			$creds = array(
				"tw_ot" => $tw_ot,
				"tw_ots" => $tw_ots,
				"tw_otv" => $tw_otv
			);
			return $creds;
		}

			//$creds = LoadTwitterCredentials();
			echo '<pre>';
			var_dump($_SESSION);
			echo '</pre>';
			$twitter = GetTwitterObject($config, $creds);
			$statuses = GetFeedHTML($twitter, 5);
			print_r($statuses);
?>