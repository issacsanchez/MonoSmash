<?php
include('/var/www/html/monosmash/classes/DB.php');
include('/var/www/html/monosmash/classes/TESTLOGIN.php');

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
			// connect with user token
			$twitter = new TwitterOAuth(
				$config['consumer_key'],
				$config['consumer_secret'],
				$creds['tw_ot'],
				$creds['tw_ots']
			);
			return $twitter;
		}
		//Returns array of embeded html posts
		function GetTwitterEmbeds($twitter, $count) {
			$statustimelineparams = ['count' => $count, "trim_user" => false];
			$statustimeline = $twitter->get('statuses/home_timeline', $statustimelineparams);
			$embedhtml = [];
			foreach($statustimeline as $item) {
				$url = "https://twitter.com/" . $item->user->screen_name . "/status" . "/" . $item->id_str;
				$data = $twitter->get('statuses/oembed', ["url" => $url, 'omit_script' => 1]);
				$embedhtml[] = $data->html;
			}
			return $embedhtml;
		}
		function BuildHTML($embeds) {
			$pregrid = '<div class="grid" id="grid" data-masonry=\'{ "itemSelector": ".grid-item"}\'>';
			$postgrid = '</div>';
			$predata = '<div class="grid-item">';
			$postdata = '</div>';
			$tw_script = '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script><script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>';
			$final_html = '';
			$feed_ar_to_str = '';

			$final_html .= $pregrid;		
			foreach($embeds as $html) {
				$feed_ar_to_str .= $predata . $html . $postdata;
			}
			$final_html .=  $feed_ar_to_str . $postgrid . $tw_script;
			return $final_html;
		}
		function LoadTwitterCredentials() {
			global $userid;
			$sql_creds = DB::query('SELECT * FROM twitter WHERE user_id=:userid', array(':userid' => $userid));
			$tw_ot = $sql_creds[0]['tw_oauth_token'];
			$tw_ots = $sql_creds[0]['tw_oauth_token_secret'];
			$creds = array(
				"tw_ot" => $tw_ot,
				"tw_ots" => $tw_ots
			);
			return $creds;
		}

		if(hasTwitter()) {
			$creds = LoadTwitterCredentials();
			$twitter = GetTwitterObject($config, $creds);
			$embeds = GetTwitterEmbeds($twitter, 20);
			$final_html = BuildHTML($embeds);
			//header('Content-Type: application/json');
			//echo json_encode($statuses);
			echo $final_html;
		} else {
			die('USER HAS NO TWITTER');
		}
?>