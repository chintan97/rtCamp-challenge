<?php
// 10 tweets will be fetched from follower's user timeline.
// If user has no tweets, nothing will be shown in slider.
// If user has less than 10 tweets, only those tweets will be shown.

require_once('libs/TwitterAPIExchange.php');
if (isset($_GET['followers']))
{
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
}
?>