<?php
	session_start();
?>
<style>
div.container {
    width: 100%;
    border: 0px solid gray;
}
header{
	padding: 1em;
    color: white;
    background-color: green;
    clear: left;
    text-align: center;
}
nav {
    float: left;
    max-width: 160px;
    margin: 0;
    padding: 0em;
}
article {
    margin-left: 170px;
    padding: 0em;
    overflow: hidden;
}
</style>
<div class='container'>
<header>
	<h3>rtCamp Assignment for Twitter Timeline Challenge</h3>
</header>
<nav>
	Task 1: Home Timeline<br/><br/>
	Task 2: Follower, Follower timeline<br/><br/>
	Task 3: Download Tweets<br/><br/>
	<h3>Designed by: Chintan Patel</h3>
</nav>
<article>
<?php
// Load the library files
require_once('twitteroauth/OAuth.php');
require_once('twitteroauth/twitteroauth.php');

// define the consumer key and secet and callback
define('CONSUMER_KEY', 'MQT5hHDMRPOlcaqgLsEvJtF0Y');
define('CONSUMER_SECRET', '3zUAzwdJmMmFdkhRPwl07CXkNZQRAa53aEHsRPGYsR6IWrLoUi');
define('OAUTH_CALLBACK', 'https://rtcamp-challenge.000webhostapp.com/');

// to handle logout request
if(isset($_GET['logout'])){
	// delete created files and destroy session
	$my_file=$_SESSION['user_name']."_tweets.xml";
	unlink($my_file);
	unlink($_SESSION['user_name'].".xml");
	session_destroy();
	echo "<script>window.location = 'index.php'</script>";
}


// if user session not enabled get the login url
if(!isset($_SESSION['data']) && !isset($_GET['oauth_token'])) {
	// new twitter connection object
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	// get the token from connection object
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK); 
	// if request_token exists then get the token and secret and store in the session
	if($request_token){
		$token = $request_token['oauth_token'];
		$_SESSION['request_token'] = $token ;
		$_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];
		// get the login url from getauthorizeurl method
		$login_url = $connection->getAuthorizeURL($token);
	}
}

// callback url
if(isset($_GET['oauth_token'])){
	// create a new twitter connection object with request token
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['request_token'], $_SESSION['request_token_secret']);
	// get the access token from getAccesToken method
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	if($access_token){	
		// create another connection object with access token
		$_SESSION['oauth_token1'] = $access_token['oauth_token'];
		$_SESSION['oauth_token_secret1'] = $access_token['oauth_token_secret'];
		
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		// set the parameters array with attributes include_entities false
		$params =array('include_entities'=>'false');
		// get the data
		$data = $connection->get('account/verify_credentials',$params);
		if($data){
			// store the data in the session
			$_SESSION['data']=$data;
			// redirect to same page to remove url parameters
			echo "<script>window.location = 'index.php'</script>";
		}
	}
}

if(isset($login_url) && !isset($_SESSION['data'])){
	// echo the login url
	echo "You have not logged in with Twitter.<br />";
	echo "Please login to view more.<br />";
	echo "<a href='$login_url'><button>Login with twitter </button></a>";
}
else{
	?>
	<link rel="stylesheet" type="text/css" href="css/screen.css">
	<!-- Include jQuery library -->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<!-- Include Cycle plugin By Mike Alsup-->
	<script type="text/javascript" src="js/jquery.cycle.all.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
		$('#promo').cycle({
			fx: 'fade',
			timeout:    3000,
			speed:      400,
			next: '#promonav .next',
			pager:    '#promoindex',
			height: 225,
			pause: 1
			});
		});
	</script>
	<?php

	// get the data stored from the session
	$data = $_SESSION['data'];
	// echo the logout button
	echo "<br /><a href='?logout=true'><button>Logout</button></a>";
	
	// echo the name username, id and photo
	echo "<div>";
	echo "<br /><br /><img src='".$data->profile_image_url_https."' height='60' width='60' align='left' alt='Image did't load'/><br />";
	echo "Welcome: ".$data->name."<br />";
	echo "Your Handle: @".$data->screen_name."<br /><br />";
	echo "</div>";
	
	$_SESSION['user_name'] = $data->screen_name;
	$_SESSION['total_tweets'] = $data->statuses_count;
	
	require_once('libs/TwitterAPIExchange.php');
	$settings = array(
	'oauth_access_token' => $_SESSION['oauth_token1'],
	'oauth_access_token_secret' => $_SESSION['oauth_token_secret1'],
	'consumer_key' => "MQT5hHDMRPOlcaqgLsEvJtF0Y",
	'consumer_secret' => "3zUAzwdJmMmFdkhRPwl07CXkNZQRAa53aEHsRPGYsR6IWrLoUi"
	);
	
	
	// Part 1: Logged in user's home timeline. TwitterAPIExchange is used.
	if(!isset($_GET['followers']))
	{
		$url = "https://api.twitter.com/1.1/statuses/home_timeline.json";
		$requestMethod = "GET";
		
		$user  = $data->screen_name;
		$count = 10;
		
		$getfield = "?screen_name=$user&count=$count";
		
		$twitter = new TwitterAPIExchange($settings);
		
		$string = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
		?>

		<!-- Tweets will be shown here in slider -->
		<div id="promonav">
			<ul id="promo">    
			<?php
			$count = 1;
			foreach($string as $items)
			{
				echo "<li><h4 style='color:green'>Tweet count: ".$count."</h4>";
				echo "<h3 style='color:blue'>Tweet: ". $items['text']."</h3>";
				echo "<h4 style='color:green'>Tweet Id: ". $items['user']['id_str']."<br />";
				echo "Retweet count: ".$items['retweet_count']."<br />";
				echo "Handle: @". $items['user']['screen_name']."<br />";
				echo "Handle name: ". $items['user']['name']."</h4><li />";
				$count++;
			}
			
			?>
			</ul>
		</div>
		<?php
	}
	?>
	<h3 style="color:Blue">Showing 10 Followers</h3>
	
	<?php 
		// Part 2: Followers section
		// Fetching 10 followers' data using TwitterAPIExchange
		// Followers' user timeline can be reached by clicking handle name
		
		$url = "https://api.twitter.com/1.1/followers/list.json";
		$requestMethod = "GET";
		
		$user  = $data->screen_name;
		$count = 10;
	
		$getfield = "?cursor=-1&screen_name=$user&skip_status=true&include_user_entities=false";

		$twitter = new TwitterAPIExchange($settings);

		$string = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
		$count = 1;
		foreach ($string['users'] as $items)
		{
			if ($count > 10)
				break;
			echo "<img src='".$items['profile_image_url_https']."' height='50' width='50' align='left' alt='Image did't load'/><br />";
			echo "Name: ".$items['name']."<br />";
			echo "Handle id: @<a href='#' onClick=ajaxLoad('".$items['id_str']."')>".$items['screen_name']."</a><br /><hr />";
			$count++;
		}
		
		
		// Now for followers search, creating xml file which will store follower name and id
		// Here maximum 5000 followers data can be fetched.
		// First ids of followers are fetched and array of 100 ids are created to fetch data 
		// from ids using users/lookup . followers/list.json can fetch maximum 300 followers.
		
		$xml = new DOMDocument();
		$xml_root = $xml->createElement('root');
		$xml->appendChild($xml_root);
		$xml->save("$user.xml");

		$url = "https://api.twitter.com/1.1/followers/ids.json";
		$requestMethod = "GET";
		
		$getfield = "?cursor=-1&screen_name=$user&count=5000&stringify_ids=true";

		$twitter = new TwitterAPIExchange($settings);

		$string = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
		$count = 1;
		$arrCount = 1;
		$tempCount = 1;
		$idString = "";
		foreach ($string['ids'] as $items)
		{
			if ($count < 101)
			{
				if ($count < 100)
					$idString = $idString."$items".",";
				else
					$idString = $idString."$items";
			}
			else if ($count == 101)
			{
				$url = "https://api.twitter.com/1.1/users/lookup.json";
				$requestMethod = "GET";
				
				$getfield = "?user_id=$idString";
				$twitter = new TwitterAPIExchange($settings);

				$string1 = json_decode($twitter->setGetfield($getfield)
				->buildOauth($url, $requestMethod)
				->performRequest(),$assoc = TRUE);
				
				$xml = new DOMDocument();
				$xml->load("$user.xml");
				$xml_root=$xml->documentElement;
				
				foreach ($string1 as $items){
					if ($items['protected'] !== true){
					$xml_usr = $xml->createElement('user');
					$xml_nm = $xml->createElement('name1');
					$xml_id = $xml->createElement('id1');
					$xml_nm->nodeValue = $items['name'];
					$xml_id->nodeValue = $items['id_str'];
					$xml_usr->appendChild($xml_nm);
					$xml_usr->appendChild($xml_id);
					$xml_root->appendChild($xml_usr);
					$arrCount++;
					}
				}
				$xml->save("$user.xml");
				$count = 0;
				$idString = "";
			}
			$count++;
			$tempCount++;
		}

		$url = "https://api.twitter.com/1.1/users/lookup.json";
		$requestMethod = "GET";
				
		$getfield = "?user_id=$idString";
		$twitter = new TwitterAPIExchange($settings);

		$string1 = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
				
		$xml = new DOMDocument();
		$xml->load("$user.xml");
		$xml_root=$xml->documentElement;
				
		foreach ($string1 as $items){
			$xml_usr = $xml->createElement('user');
			$xml_nm = $xml->createElement('name1');
			$xml_id = $xml->createElement('id1');
			$xml_nm->nodeValue = $items['name'];
			$xml_id->nodeValue = $items['id_str'];
			$xml_usr->appendChild($xml_nm);
			$xml_usr->appendChild($xml_id);
			$xml_root->appendChild($xml_usr);
			$arrCount++;
		}
		$xml->save($user.".xml");
	
	?>

	<!-- Search box. Click on followers name to show user timeline in slider without refresh -->
	
	<script>
         function showResult(str) {
			
            if (str.length == 0) {
               document.getElementById("searchtweet").innerHTML = "";
               document.getElementById("searchtweet").style.border = "0px";
               return;
            }
            
            if (window.XMLHttpRequest) {
               xmlhttp = new XMLHttpRequest();
            }else {
               xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            
            xmlhttp.onreadystatechange = function() {
				
               if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  document.getElementById("searchtweet").innerHTML = xmlhttp.responseText;
                  document.getElementById("searchtweet").style.border = "1px solid #A5ACB2";
               }
            }
            
            xmlhttp.open("GET","search.php?q="+str,true);
            xmlhttp.send();
         }
    </script>
	
	<form>
         <h2>Enter Follower Name to go to his/her timeline</h2>
         <input type = "text" size = "30" onkeyup = "showResult(this.value)">
         <div id = "searchtweet"></div>
	</form>
	
	<?php
	
		// Part 3: Tweet download section
		// Maximum 3200 tweets can be fetched which is ultimate limit of fetching.
		// They are stored in .xml file in and can be downloaded.
		
		$xml_tweet = new DOMDocument();
		$xml_root_tweet = $xml_tweet->createElement('root');
		$xml_tweet->appendChild($xml_root_tweet);
		$xml_tweet->save($_SESSION['user_name']."_tweets.xml");
		
		$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
		$requestMethod = "GET";
	
		$count_tweet = 0;
		$page_no = 1;
		$count = 1;
		while($count_tweet <= $_SESSION['total_tweets'] && $count_tweet < 3200)
		{
			$count_tweet += 200;
			$user  = $data->screen_name;
			$getfield = "?screen_name=$user&count=200&page=$page_no";
			$page_no++;
			$twitter = new TwitterAPIExchange($settings);
		
			$string = json_decode($twitter->setGetfield($getfield)
			->buildOauth($url, $requestMethod)
			->performRequest(),$assoc = TRUE);
			
			$xml_tweet = new DOMDocument();
			$xml_tweet->load($_SESSION['user_name']."_tweets.xml");
			$xml_root_tweet=$xml_tweet->documentElement;
			
			foreach($string as $items)
			{
				$xml_tw_tweet = $xml_tweet->createElement('tweet');
				$xml_cnt_tweet = $xml_tweet->createElement('count');
				$xml_data_tweet = $xml_tweet->createElement('text');
				$xml_rt_tweet = $xml_tweet->createElement('retweets');
				$xml_cnt_tweet->nodeValue = $count;
				$xml_data_tweet->nodeValue = $items['text'];
				$xml_rt_tweet->nodeValue = $items['retweet_count'];
				$xml_tw_tweet->appendChild($xml_cnt_tweet);
				$xml_tw_tweet->appendChild($xml_data_tweet);
				$xml_tw_tweet->appendChild($xml_rt_tweet);
				$xml_root_tweet->appendChild($xml_tw_tweet);
				$count++;
			}
			$xml_tweet->save($_SESSION['user_name']."_tweets.xml");
		}
	?>

	<a href="<?php echo $_SESSION['user_name']."_tweets.xml"; ?>" download="MyTweets"><h3 style="color:blue">Download tweets in xml format</h3></a>
	
	<?php
	
	include('search.php');
	include('index2.php');
} 
?>
</article>
</div>