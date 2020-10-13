<?php

header("Content-type: text/html; charset=utf-8");

// Convert a string into binary data
function str2bin($text){
	$bin = array();
	for($i=0; strlen($text)>$i; $i++)
		$bin[] = decbin(ord($text[$i]));
	return implode(' ', $bin);
}

// Convert binary data into a string
function bin2str($bin){
	$text = array();
	$bin = explode(' ', $bin);
	for($i=0; count($bin)>$i; $i++) 
		$text[] = chr(@bindec($bin[$i]));
	return implode($text);
}

// Convert the ones, zeros, and spaces of the hidden binary data to their respective zero-width characters 
function bin2hidden($str) {
	$str = str_replace(' ', "\xE2\x81\xA0", $str); // Unicode Character 'WORD JOINER' (U+2060) 0xE2 0x81 0xA0
	$str = str_replace('0', "\xE2\x80\x8B", $str); // Unicode Character 'ZERO WIDTH SPACE' (U+200B) 0xE2 0x80 0x8B
	$str = str_replace('1', "\xE2\x80\x8C", $str); // Unicode Character 'ZERO WIDTH NON-JOINER' (U+200C) 0xE2 0x80 0x8C
	return $str;
}

// Convert zero-width characters to hidden binary data
function hidden2bin($str) {
	$str = str_replace("\xE2\x81\xA0", ' ', $str); // Unicode Character 'WORD JOINER' (U+2060) 0xE2 0x81 0xA0
	$str = str_replace("\xE2\x80\x8B", '0', $str); // Unicode Character 'ZERO WIDTH SPACE' (U+200B) 0xE2 0x80 0x8B
	$str = str_replace("\xE2\x80\x8C", '1', $str); // Unicode Character 'ZERO WIDTH NON-JOINER' (U+200C) 0xE2 0x80
	return $str;
}

if(!isset($_GET['public']) && !isset($_GET['private']) && !isset($_GET['decode'])) {
	echo '<!DOCTYPE html>
<meta charset="utf-8">
<title>Steganographr API</title>
<h1>Steganographr API</h1>
<p>Hello and thanks for your interest in this API. Here’s some quick documentation:</p>
<h2>Encoding</h2>
<p>You can encode messages by passing both <code>public</code> and <code>private</code> parameters, e.g.:</p>
<blockquote><code>GET <a href="https://neatnik.net/steganographr/api?public=It+sure+is+hot+today&private=meet+me+behind+the+office+at+4+pm">https://neatnik.net/steganographr/api?public=It+sure+is+hot+today&private=meet+me+behind+the+office+at+4+pm</a></code></blockquote>
<p>The response will be the encoded text, e.g.:</p>
<blockquote>It sure is ‌‌​‌‌​‌⁠‌‌​​‌​‌⁠‌‌​​‌​‌⁠‌‌‌​‌​​⁠‌​​​​​⁠‌‌​‌‌​‌⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​​​‌​⁠‌‌​​‌​‌⁠‌‌​‌​​​⁠‌‌​‌​​‌⁠‌‌​‌‌‌​⁠‌‌​​‌​​⁠‌​​​​​⁠‌‌‌​‌​​⁠‌‌​‌​​​⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​‌‌‌‌⁠‌‌​​‌‌​⁠‌‌​​‌‌​⁠‌‌​‌​​‌⁠‌‌​​​‌‌⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​​​​‌⁠‌‌‌​‌​​⁠‌​​​​​⁠‌‌​‌​​⁠‌​​​​​⁠‌‌‌​​​​⁠‌‌​‌‌​‌hot today</blockquote>
<h2>Decoding</h2>
<p>You can decode messages by passing the <code>decode</code> parameter, e.g.:</p>
<blockquote><code>GET <a href="https://neatnik.net/steganographr/api?decode=It+sure+is+‌‌​‌‌​‌⁠‌‌​​‌​‌⁠‌‌​​‌​‌⁠‌‌‌​‌​​⁠‌​​​​​⁠‌‌​‌‌​‌⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​​​‌​⁠‌‌​​‌​‌⁠‌‌​‌​​​⁠‌‌​‌​​‌⁠‌‌​‌‌‌​⁠‌‌​​‌​​⁠‌​​​​​⁠‌‌‌​‌​​⁠‌‌​‌​​​⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​‌‌‌‌⁠‌‌​​‌‌​⁠‌‌​​‌‌​⁠‌‌​‌​​‌⁠‌‌​​​‌‌⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​​​​‌⁠‌‌‌​‌​​⁠‌​​​​​⁠‌‌​‌​​⁠‌​​​​​⁠‌‌‌​​​​⁠‌‌​‌‌​‌hot+today">https://neatnik.net/steganographr/api?decode=It+sure+is+‌‌​‌‌​‌⁠‌‌​​‌​‌⁠‌‌​​‌​‌⁠‌‌‌​‌​​⁠‌​​​​​⁠‌‌​‌‌​‌⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​​​‌​⁠‌‌​​‌​‌⁠‌‌​‌​​​⁠‌‌​‌​​‌⁠‌‌​‌‌‌​⁠‌‌​​‌​​⁠‌​​​​​⁠‌‌‌​‌​​⁠‌‌​‌​​​⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​‌‌‌‌⁠‌‌​​‌‌​⁠‌‌​​‌‌​⁠‌‌​‌​​‌⁠‌‌​​​‌‌⁠‌‌​​‌​‌⁠‌​​​​​⁠‌‌​​​​‌⁠‌‌‌​‌​​⁠‌​​​​​⁠‌‌​‌​​⁠‌​​​​​⁠‌‌‌​​​​⁠‌‌​‌‌​‌hot+today</a></code></blockquote>
<p>The response will be the private message, e.g.:</p>
<blockquote>meet me behind the office at 4 pm</blockquote>
<h2>Additional information</h2>
<p>You can use an interactive version of the service at <a href="https://neatnik.net/steganographr/">https://neatnik.net/steganographr/</a>. If you have any questions, feel free to contact <a href="mailto:adam@neatnik.net">adam@neatnik.net</a>.</p>
<hr>
<em>Last modified: '.date("r", filemtime(__FILE__)).'</em>

';
	exit;
}

if(!isset($_GET['decode']) && isset($_GET['public']) && !isset($_GET['private'])) {
	die("<strong>Failed:</strong> You need to specify your private message in the <code>private</code> parameter.");
}

if(!isset($_GET['decode']) && isset($_GET['private']) && !isset($_GET['public'])) {
	die("<strong>Failed:</strong> You need to specify your public message in the <code>public</code> parameter.");
}

if(isset($_GET['public']) && strlen($_GET['public']) >= 2) {
	
	// Grab the public message string and break it up into characters
	$public = $_GET['public'];
	$public = mb_str_split($public);
	
	// Find the half-way point in the string
	$half = round(count($public) / 2);
	
	// Grab the private message
	$private = $_GET['private'];
	
	// Convert it to binary data
	$str = str2bin($private);
	
	// And convert that into a string of zero-width characters
	$private = bin2hidden($str);
	
	// Inject the encoded private message into the approximate half-way point in the public string
	$i = 0;
	$tmp = array();
	if(count($public) == 1) {
		$tmp[0] = $public[0];
		$tmp[1] = $private;
	}
	else {
		foreach($public as $char) {
			if($i == $half) {
				$tmp[] = $private;
			}
			$tmp[] = $char;
			$i++;
		}
	}
	
	// Reassemble the public string
	$public = implode('', $tmp);
	
	die($public);
	
	$out['steganographized'] = $public;
}

else if(isset($_GET['public'])) {
	die('<strong>Failed:</strong> Your public message must be at least two characters.');
}

if(isset($_GET['decode'])) {
	// Unhide the message
	$message = bin2str(hidden2bin($_GET['decode']));
	
	// Display the hidden private message
	if(strlen($message) < 2) {
		die('<strong>Notice:</strong> No private message was found.');
	}
	else {
		$message = htmlentities($message);
		$bits = str_split($message);
		$out = null;
		foreach($bits as $char) {
			if(ord($char) == 0) continue;
			$out .= $char;
		}
		die($out);
	}
}
