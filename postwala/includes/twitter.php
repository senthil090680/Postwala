<?php
////////////////////////////////////////////////////////////
//Post to Twitter
////////////////////////////////////////////////////////////
//usage (requires curl):
//echo get_short_url("http://es.php.net/manual/en/function.strcspn.php");
//echo post_to_twitter("test from php twitter","http://en.php.net/manual/en/function.strcspn.php");

function get_short_url($url) {//modified from http://james.cridland.net/code/bitly.html
	if (BIT_USER!="" && BIT_API!=""){
		$api_call = file_get_contents("http://api.j.mp/shorten?version=2.0.1&longUrl=".$url."&login=".BIT_USER."&apiKey=".BIT_API);
		$bitlyinfo=json_decode(utf8_encode($api_call),true);
		if ($bitlyinfo['errorCode']==0) {
			return $bitlyinfo['results'][urldecode($url)]['shortUrl'];
		} 
		else return $url;
	}
	else return $url;
}


function post_to_twitter($message,$link){ //modified from http://morethanseven.net/2007/01/20/posting-to-twitter-using-php/
	if (TWITTER!="" && TWITTER_PWD!=""){//only if the username password are set
		// Set username and password
		$username = TWITTER;
		$password = TWITTER_PWD;
		// The message you want to send, control size and create link
		$msg_size=140;//max size for twitt msg
		$link=get_short_url($link);//make the link shorter if it's set
		$msg_size-=(strlen($link)+1);//size that we have for the message after the link inserted +1 to leave an space between
		if(strlen($message)>$msg_size) $message=substr($message, 0, $msg_size);//crop the message then fits the url
		$message.=" ".$link;//message with the link
		
		// The twitter API address
		$twitterurl = 'http://twitter.com/statuses/update.xml';
		// Set up and execute the curl process
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, "$twitterurl");
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");
		curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$password");
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		// check for success or failure
		if (empty($buffer)) return true;
		else  return false;
	}
	else return false;
}

?>