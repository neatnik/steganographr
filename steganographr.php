<?php

/*  
	 ___ _                                              _        
	/ __| |_ ___ __ _ __ _ _ _  ___  __ _ _ _ __ _ _ __| |_  _ _ 
	\__ \  _/ -_) _` / _` | ' \/ _ \/ _` | '_/ _` | '_ \ ' \| '_|
	|___/\__\___\__, \__,_|_||_\___/\__, |_| \__,_| .__/_||_|_|  
	            |___/               |___/         |_|            
	
	Hide messages within other messages using invisible characters
	
	by Neatnik LLC
	https://neatnik.net
	
	MIT License
	
	Copyright (c) 2019 Neatnik LLC
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.

*/

// Prepare variables
$public = isset($_POST['public']) ? $_POST['public'] : null;
$private = isset($_POST['private']) ? $_POST['private'] : null;
$encoded = isset($_POST['encoded']) ? $_POST['encoded'] : null;

// Convert a string into binary data
function str2bin($text){
    $bin = array();
    for($i=0; strlen($text)>$i; $i++)
        $bin[] = decbin(ord($text[$i]));
    return implode(' ',$bin);
}

// Convert binary data into a string
function bin2str($bin){
    $text = array();
    $bin = explode(' ', $bin);
    for($i=0; count($bin)>$i; $i++)
        $text[] = chr(bindec($bin[$i]));
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

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<meta property="og:title" content="Steganographr">
<meta property="og:url" content="https://neatnik.net/steganographr/">
<meta property="og:description" content="Hide text in plain sight using invisible zero-width characters. Digital steganography made simple.">
<title>Steganographr</title>
</head>
<body>

<main>

<h1>Steganographr</h1>

<p>Hide text in plain sight using invisible zero-width characters. It’s digital steganography made simple. Inspired by <a href="https://www.zachaysan.com/writing/2017-12-30-zero-width-characters">Zach Aysan</a>.</p>

<p>Enter a public message, then a private message, and then click the button to hide your private message within your public message. If you’ve received a public message, you can reveal the private message here as well. <a href="#about">How does it work?</a></p>

<section>

<div style="display: grid; grid-auto-rows: 1fr; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); grid-gap: 2em;">

<form action="?" method="post">
<fieldset>
<legend>Hide</legend>
<div class="group">
<label for="public">Public message</label>
<textarea name="public"><?php echo $public; ?></textarea>
</div>
<div class="group">
<label for="private">Private message</label>
<textarea name="private"><?php echo $private; ?></textarea>
</div>
<p><button type="submit"><i class="fas fa-pencil-alt"></i> Steganographize</button></p>
</fieldset>
</form>

<form action="?" method="post">
<fieldset>
<legend>Reveal</legend>
<div class="group">
<label for="encoded">Public message</label>
<textarea name="encoded" style="height: 11.5em;"><?php echo $encoded; ?></textarea>
</div>
<p><button type="submit"><i class="fas fa-eye"></i> Desteganographize</button></p>
</form>
</div>

</div>

</section>

<?php

if(isset($_POST['public'])) {
    echo '<section class="notice"><h2>Steganographized Message</h2>';
    
    // Grab the public message string and break it up into characters
    $public = $_POST['public'];
    $public = str_split($public);
    
    // Find the half-way point in the string
    $half = round(count($public) / 2);
    
    // Grab the private message
    $private = $_POST['private'];
    
    // Convert it to binary data
    $str = str2bin($private);
    
    // And convert that into a string of zero-width characters
    $private = bin2hidden($str);
    
    // Inject the encoded private message into the approximate half-way point in the public string
    $public[$half] = $public[$half].$private;
    
    // Reassemble the public string
    $public = implode('', $public);
    
    // Display a <textarea> containing the public message with the hidden private embedded
    echo '<textarea style="height: 5em;">'.$public.'</textarea>';
    echo '<p>Copy the text above, and your private message will come along for the ride.</p>';    
    echo '</div>';
}

if(isset($_POST['encoded'])) {
    // Unhide the message
    $message = bin2str(hidden2bin($_POST['encoded']));
    
    // Display the hidden private message
    echo '<section class="notice"><h2>Private Message</h2>';
    if(strlen($message) < 2) {
        echo '<p class="alert"><i class="fas fa-exclamation-triangle"></i> No private message was found.</p>';
    }
    else {
        echo '<p style="font-weight: bold;">'.htmlentities($message).'</p>';
    }
    echo '</section>';
}

?>

</section>

<section id="about" class="attention">
<h2>About Steganographr</h2>
<p>Steganographr works by converting your private message into binary data, and then converting that binary data into zero-width characters (which can then be hidden in your public message). These characters are used:</p>
<ul>
    <li>WORD JOINER (U+2060)</li>
    <li>ZERO WIDTH SPACE (U+200B)</li>
    <li>ZERO WIDTH NON-JOINER (U+200C)</li>
</ul>
</section>

</main>

</body>
</html>