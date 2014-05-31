<?php
// Example for creating a slideshow from images in the "./img/" dir.

require "../src/GifCreator/AnimGif.php";

$anim = new GifCreator\AnimGif();


// Load all images from "./img/", in ascending order,
// apply the given delays, and save the result...

$anim	-> create("img/", array(300, 500)) // first 3s, then 5s for all the others
	-> save("anim.gif");

?>
