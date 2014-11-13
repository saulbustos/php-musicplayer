php-musicplayer
===============

This is a little PHP interface i wrote some years ago for managing and playing mp3 playlist in a server. I have included
a .swf player but you can use your own, as long it supports XSPF playlists standard format.

Installing instructions:

- Copy all the files in a server, one level below your mp3 folders

- Edit index.php and replace the server and path names by your own (this file is pointing to www.starblank.com, 
make sure you change it)

- Navigate to server/path/index.php, the program will automatically read your mp3 folders and create playlists

- NOTE: As this program haven't got integrated autenthication it's highly recommended you protect the path using 
apache basic auth or similar (or whatever you prefer)

- Have fun

