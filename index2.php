<?php
// 10 tweets will be fetched from follower's user timeline.
// If user has no tweets, nothing will be shown in slider.
// If user has less than 10 tweets, only those tweets will be shown.

require_once('libs/TwitterAPIExchange.php');
if (isset($_GET['followers']))
{
	session_start();
	$user = $_GET['followers'];
	
	$settings = array(
	'oauth_access_token' => "3255671077-DkbkYO7jlVzZniNyzfxxBkPIE4MqmKEaPRG9Ksk",
	'oauth_access_token_secret' => "LIqIwD22MkfETpqPuFjLYEzi1cMNfoghiazdHrO82YYYo",
	'consumer_key' => "MQT5hHDMRPOlcaqgLsEvJtF0Y",
	'consumer_secret' => "3zUAzwdJmMmFdkhRPwl07CXkNZQRAa53aEHsRPGYsR6IWrLoUi"
	);
	
	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	$requestMethod = "GET";
	
	$getfield = "?user_id=$user&count=10";

	$twitter = new TwitterAPIExchange($settings);

	$string = json_decode($twitter->setGetfield($getfield)
	->buildOauth($url, $requestMethod)
	->performRequest(),$assoc = TRUE);
	?>
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
	$limit = $_SESSION['total_tweets'];

	$url = "https://api.twitter.com/1.1/users/lookup.json";
	$requestMethod = "GET";
				
	$getfield = "?user_id=$user";
	$twitter = new TwitterAPIExchange($settings);
	$string1 = json_decode($twitter->setGetfield($getfield)
	->buildOauth($url, $requestMethod)
	->performRequest(),$assoc = TRUE);
	
	foreach($string1 as $items)
	{
		$user_id = $items['screen_name'];
		$tw = $items['statuses_count'];
	}
	$xml_tweet = new DOMDocument();
	$xml_root_tweet = $xml_tweet->createElement('root');
	$xml_tweet->appendChild($xml_root_tweet);
	$xml_tweet->save($user_id."_tweets.xml");
	
	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	$requestMethod = "GET";
	$count_tweet = 0;
	$page_no = 1;
	$count = 1;
	while($count_tweet <= $tw && $count_tweet < 3200)
	{
		$count_tweet += 200;
		$user  = $user_id;
		$getfield = "?screen_name=$user_id&count=200&page=$page_no";
		$page_no++;
		$twitter = new TwitterAPIExchange($settings);
	
		$string = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
		
		$xml_tweet = new DOMDocument();
		$xml_tweet->load($user_id."_tweets.xml");
		$xml_root_tweet=$xml_tweet->documentElement;
		
		foreach($string as $items)
		{
			if ($count >= 3200-$limit)
			{
				$_SESSION['total_tweets'] = 3200;
				$count_tweet = 3200;
				echo "<h4 style='color:red'>Twitter API limit is exceeded, now if you download another followers tweets->file will be empty. Login again to get new handles' tweets</h4>";
				break;
			}
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
		$xml_tweet->save($user_id."_tweets.xml");
	}
	if ($_SESSION['total_tweets'] != 3200)
	{
		$_SESSION['total_tweets'] = $count;
	}
	?>
	<a href="<?php echo $user_id."_tweets.xml"; ?>" download="<?php echo $user_id ?>.xml"><h3 style="color:blue">Download <?php echo $user_id ?>'s tweets in xml format</h3></a>
	<?php
}
?>
