# Steganographr

Hide text in plain sight using invisible zero-width characters. It’s digital steganography made simple. Inspired by [Zach Aysan](https://www.zachaysan.com/writing/2017-12-30-zero-width-characters).

You can view a live demo of the tool at https://neatnik.net/steganographr/.

## How it works

Steganographr works by converting your private message into binary data, and then converting that binary data into zero-width characters (which can then be hidden in your public message).

These characters are used:

* WORD JOINER (U+2060)
* ZERO WIDTH SPACE (U+200B)
* ZERO WIDTH NON-JOINER (U+200C)