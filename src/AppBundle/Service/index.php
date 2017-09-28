<?php
echo "<h2>Fetch 20 last tweetes</h2>";

//https://github.com/J7mbo/twitter-api-php
require_once('vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php');

$settings = array(
    'oauth_access_token' => "1282845319-wHI65pttNZBNKYcutLPIQsKn5LDx3IIloN3C7Eh",
    'oauth_access_token_secret' => "avCYZte53pd83n2pcog1QH09khbiywBUN2fajgDmlJ3c8",
    'consumer_key' => "BRfG1MMSs0kVNNsKAa1gGOZU1",
    'consumer_secret' => "8da6nKYVl0ru1eHfYOhIj46lHn6JwoYm5SSBdH0X7CXQQFPTeZ"
);

//$url = 'https://api.twitter.com/1.1/followers/ids.json';
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?screen_name=vedranavidovic&count=20';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$tweets = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();
	
/*$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);
if($string["errors"][0]["message"] != "") {echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";exit();}
foreach($string as $items)
    {
        echo "Time and Date of Tweet: ".$items['created_at']."<br />";
        echo "Tweet: ". $items['text']."<br />";
        echo "Tweeted by: ". $items['user']['name']."<br />";
        echo "Screen name: ". $items['user']['screen_name']."<br />";
        echo "Followers: ". $items['user']['followers_count']."<br />";
        echo "Friends: ". $items['user']['friends_count']."<br />";
        echo "Listed: ". $items['user']['listed_count']."<br /><hr />";
    }
*/
echo '<pre>';
var_dump(json_decode ($tweets));
echo '<pre>';
?>