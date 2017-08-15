<?php
// Follower search requests are processed here. Maximum 20 hints are shown.

if(isset($_GET['q']))
{
session_start();
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
        $id = $book->getElementsByTagName("id1")->item(0)->nodeValue;
		$temp = preg_replace("/[^a-zA-Z]/", "", $name);
		$temp1 = preg_replace("/[^a-zA-Z]/", "", $id);

		if (  (strpos ( strtolower ( $temp ), $q ) !== false or strpos ( strtolower ( $temp1 ), $q ) !== false) and $count <= 20 ){
			
			// Hints are shown here.
			echo "<a href='#' onClick=ajaxLoad('$id')>$name</a><br />";
			$count++;
		}
    }
		
	if ($count == 1){
		echo "No follower matched!!!";
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