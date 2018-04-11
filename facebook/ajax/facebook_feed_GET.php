<?php
include('/var/www/html/monosmash/classes/DB.php');
include('/var/www/html/monosmash/classes/TESTLOGIN.php');
require "/var/www/html/monosmash/facebook/vendor/autoload.php";
session_start();

$config = require '/var/www/html/monosmash/facebook/config_facebook.php';
$userid = TESTLOGIN::isLoggedIn();

		function hasFacebook($userid) {
			if(DB::query('SELECT user_id FROM facebook WHERE user_id=:userid', array(':userid' => $userid))) {
				return true;
			} else {
				return false;
			}
		}
		function LoadFacebookCredentials($userid) {
			$sql_creds = DB::query('SELECT * FROM facebook WHERE user_id=:userid', array(':userid' => $userid));
			$fb_at = $sql_creds[0]['fb_access_token'];
			$creds = array(
				"fb_at" => $fb_at
			);
			return $creds;
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
				$response = $facebook->get('/me/likes?fields=id,name', $creds['fb_at']);
				$graphEdge = $response->getGraphEdge();
				foreach($graphEdge as $graphNode) {
					$pageposts = $facebook->get('/' . $graphNode['id'] . '/posts?limit=1&fields=permalink_url,created_time,description', $creds['fb_at']);
					$edge = $pageposts->getGraphEdge();
					foreach($edge as $node) {
						$posts[] = array('created_time'=>$node['created_time'], 'provider'=>'facebook', 'post_url'=>$node['permalink_url']);
					}
				}
				return $posts;

			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: at GetPostsURL top level' . $e->getMessage();
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
				$json_url = $oembed_endpoint . '.json/?url=' . rawurlencode($post['post_url']) . '&maxwidth=550&omitscript=1';
				$curl = curl_init($json_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
				$result = curl_exec($curl);
				$jsonr = json_decode($result, true);
				$embeds[] = array('created_time'=>$post['created_time'], 'provider'=>$post['provider'], 'html'=>$jsonr['html']);
				curl_close($curl);
			}
			return $embeds;
		}

		if(hasFacebook($userid)) {
			$creds = LoadFacebookCredentials($userid);
			$facebook = GetFacebookObject($config);
			$posts = GetPostsURL($facebook, $creds);
			$embeds = GetEmbeds($posts);
			header('Content-Type: application/json');
			echo json_encode($embeds);
		} else {
			die('USER HAS NO facebook');
		}
?>