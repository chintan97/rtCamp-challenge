<?php
// Follower search requests are processed here. Maximum 10 hints are shown.

if(isset($_GET['q']))
{
session_start();
require_once('libs/TwitterAPIExchange.php');
$un = $_SESSION['user_name'];
$xmlDoc = new DOMDocument();
$xmlDoc->load("$un.xml");
$books = $xmlDoc->getElementsByTagName("user");
$q = strtolower($_GET["q"]);
$count = 1;

if (strlen($q) > 0) {
    $hint = "";

    foreach ($books as $book) {
        $name = $book->getElementsByTagName("name1")->item(0)->nodeValue;
        $handle = $book->getElementsByTagName("handle1")->item(0)->nodeValue;
        $id = $book->getElementsByTagName("id1")->item(0)->nodeValue;
		$temp = preg_replace("/[^a-zA-Z]/", "", $name);
		$temp1 = preg_replace("/[^a-zA-Z]/", "", $id);

		if (  (strpos ( strtolower ( $temp ), $q ) !== false or strpos ( strtolower ( $temp1 ), $q ) !== false) and $count <= 10 ){
			
			// Hints are shown here.
			echo "<a href='#' onClick=ajaxLoad('$id')>$name, Handle: @$handle</a><br />";
			$count++;
		}
    }
		
	if ($count < 10)
	{
		$url = "https://api.twitter.com/1.1/users/search.json";
		$requestMethod = "GET";
		
		$settings = array(
		'oauth_access_token' => $_SESSION['oauth_token1'],
		'oauth_access_token_secret' => $_SESSION['oauth_token_secret1'],
		'consumer_key' => "MQT5hHDMRPOlcaqgLsEvJtF0Y",
		'consumer_secret' => "3zUAzwdJmMmFdkhRPwl07CXkNZQRAa53aEHsRPGYsR6IWrLoUi"
		);
		
		$temp_c = 10-$count;
		$getfield = "?q=$q&count=$temp_c";
		$twitter = new TwitterAPIExchange($settings);
		$string1 = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
		
		foreach($string1 as $items)
		{
			?>
			<a href='#' onClick=ajaxLoad('<?php echo $items['id_str'] ?>')><?php echo $items['name'] ?>, Handle: @<?php echo $items['screen_name'] ?></a><br />
			<?php
			$count++;
		}
	}
	if ($count == 1){
		echo "No follower/ public account matched!!!";
	}

}
}
?>

<script>
    function ajaxLoad(fid) {
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
            }else {
               xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            
			xmlhttp.onreadystatechange = function() {
				
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("promonav").innerHTML = xmlhttp.responseText;
			    start();
                }
            }
            
            xmlhttp.open("GET","index2.php?followers="+fid,true);
            xmlhttp.send();
         }
		 
		 function start(){
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
}
</script>
