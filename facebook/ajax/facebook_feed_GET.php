<?php
include('/var/www/html/monosmash/classes/DB.php');
include('/var/www/html/monosmash/classes/TESTLOGIN.php');
require "/var/www/html/monosmash/facebook/vendor/autoload.php";
session_start();

$config = require '/var/www/html/monosmash/facebook/config_facebook.php';
$userid = TESTLOGIN::isLoggedIn();

		function hasFacebook() {
			global $userid;
			if(DB::query('SELECT user_id FROM facebook WHERE user_id=:userid', array(':userid' => $userid))) {
				return true;
			} else {
				return false;
			}
		}
		function GetFacebookObject($config) {
			// connect with user token
			$facebook = new Facebook\Facebook([
				'app_id' => $config['app_id'],
				'app_secret' => $config['app_secret']
			]);
			return $facebook;
		}
		//Returns array of embeded html posts
		function GetPostsURL($facebook, $creds) {
			try {
				$posts = [];
				$html = [];
				$response = $facebook->get('/me/likes?fields=id,name', $creds['fb_at']);
				$graphEdge = $response->getGraphEdge();
				foreach($graphEdge as $graphNode) {
					$pageposts = $facebook->get('/' . $graphNode['id'] . '/posts?limit=1&fields=permalink_url,created_time,description', $creds['fb_at']);
					$edge = $pageposts->getGraphEdge();
					foreach($edge as $node) {
						$posts[] = $node['permalink_url'];
					}
				}
				return $posts;

			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: ' . $e->getMessage();
				exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}
		}
		function GetEmbeds($posts) {
			$embeds = [];
			$oembed_endpoint = 'https://www.facebook.com/plugins/post/oembed';
			foreach($posts as $post) {
				$json_url = $oembed_endpoint . '.json/?url=' . rawurlencode($post) . '&maxwidth=550&omitscript=1';
				$curl = curl_init($json_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
				$result = curl_exec($curl);
				$jsonr = json_decode($result, true);
				$embeds[] = $jsonr['html'];
				curl_close($curl);
			}
			return $embeds;
		}
		function BuildHTML($embeds) {
			$fb_script = '<script>(function (d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s);js.id = id;js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11";fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script>';
			$feed_ar_to_str = '';
			foreach($embeds as $html) {
				$feed_ar_to_str .= $html;
			}
			$final_feed = $fb_script . $feed_ar_to_str;
			return $final_feed;
		}
		function LoadFacebookCredentials() {
			global $userid;
			$sql_creds = DB::query('SELECT * FROM facebook WHERE user_id=:userid', array(':userid' => $userid));
			$fb_at = $sql_creds[0]['fb_access_token'];
			$creds = array(
				"fb_at" => $fb_at
			);
			return $creds;
		}

		if(hasFacebook()) {
			$creds = LoadFacebookCredentials();
			$facebook = GetFacebookObject($config);
			$postsURL = GetPostsURL($facebook, $creds);
			$embeds = GetEmbeds($postsURL);
			$final_html = BuildHTML($embeds);
			//header('Content-Type: application/json');
			//echo json_encode($embeds);
			echo $final_html;
		} else {
			die('USER HAS NO facebook');
		}
?>