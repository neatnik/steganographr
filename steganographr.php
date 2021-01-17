<?php

#error_reporting(E_ALL);
#ini_set('display_errors', '1');

// Convert a string into binary data
function str2bin($text){
	$bin = array();
	for($i=0; strlen($text)>$i; $i++)
		$bin[] = decbin(ord($text[$i]));
	return implode(' ', $bin);
}

// Wrap a string with a distinct boundary
function wrap($string) {
	return "\xEF\xBB\xBF".$string."\xEF\xBB\xBF"; // Unicode Character 'ZERO WIDTH NON-BREAKING SPACE' (U+FEFF) 0xEF 0xBB 0xBF
}

// Unwrap a string if the distinct boundary exists
function unwrap($string) {
	$tmp = explode("\xEF\xBB\xBF", $string);
	if(count($tmp) == 1) return false; // If the string doesn't contain the boundary, return false
	return $tmp[1]; // Otherwise, return the unwrapped string
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

// Prepare variables
$public = isset($_POST['public']) ? $_POST['public'] : null;
$private = isset($_POST['private']) ? $_POST['private'] : null;
$encoded = isset($_POST['encoded']) ? $_POST['encoded'] : null;

/* Main forms */

$content = '<form action="/steganographr/#results" method="post">
	<fieldset>
		<legend>Hide</legend>
		<p>Hide a private message within a public message.</p>
		<p>
			<label for="public">Public message</label>
			<textarea id="public" name="public">'.$public.'</textarea>
		</p>
		<p>
			<label for="private">Private message</label>
			<textarea id="private" name="private">'.$private.'</textarea>
		</p>
		<p>
			<button type="submit"><i class="fas fa-eye-slash"></i> Steganographize</button>
		</p>
	</fieldset>
</form>

<form action="/steganographr/#results" method="post">
	<fieldset>
		<legend>Reveal</legend>
		<p>Reveal the private message hidden within a public message.</p>
		<p>
			<label for="encoded">Public message</label>
			<textarea id="encoded" name="encoded">'.$encoded.'</textarea>
		</p>
		<p>
			<button type="submit"><i class="fas fa-eye"></i> Desteganographize</button>
		</p>
	</fieldset>
</form>

';

if(isset($_POST['public'])) {
	$content .= '<section id="results" class="bubble notice"><h2>Steganographized Message</h2>';
	
	// Grab the public message string and break it up into characters
	$public = $_POST['public'];
	$public = mb_str_split($public);
	
	// Find the half-way point in the string
	$half = round(count($public) / 2);
	
	// Grab the private message
	$private = $_POST['private'];
	
	// Convert it to binary data
	$private = str2bin($private);
	
	// And convert that into a string of zero-width characters
	$private = bin2hidden($private);
	
	// Finally, wrap it with a distinct boundary character
	$private = wrap($private);
	
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
	
	// Display a <textarea> containing the public message with the hidden private embedded
	$content .= '<textarea style="height: 3em;">'.$public.'</textarea>';
	$content .= '<p>Copy the text above, and your private message will come along for the ride.</p>';	

}

if(isset($_POST['encoded'])) {
	// Unhide the message
	$unwrapped = unwrap($_POST['encoded']);
	
	// If it's not wrapped, process the full string as received
	if(!$unwrapped) {
		$message = bin2str(hidden2bin($_POST['encoded']));
	}
	// Otherwise, process only the unwrapped string
	else {
		$message = bin2str(hidden2bin($unwrapped));
	}
	
	// Display the hidden private message
	$content .= '<h2 id="results">Private Message</h2>';
	if(strlen($message) < 2) {
		$content .= '<div class="message"><div class="message-icon"><i class="fas fa-fw fa-exclamation-circle"></i></div><div class="message-text">No private message was found in that text.</div>
		</div></p>';
	}
	else {
		$content .= '<div class="message"><div class="message-icon"><i class="fas fa-fw fa-check-circle"></i></div><div class="message-text">'.htmlentities($message).'</div>
		</div></p>';
	}
}

end:

echo $content;