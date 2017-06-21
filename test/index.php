<?php

	namespace GifCreator;
	
	require_once '../src/GifCreator/GifCreator.php';

	// Create an array containing file paths, resource var (initialized with imagecreatefromXXX), 
	// image URLs or even binary code from image files.
	// All sorted in order to appear.
	$frames = array(
	    "1.png",
	    "2.png",
	);

	// Create an array containing the duration (in millisecond) of each frames (in order too)
	$durations = array(40, 80, 40);

	// Initialize and create the GIF !
	$gc = new GifCreator();
	$gc->create($frames, $durations, 5);


	$gifBinary = $gc->getGif();

	if (isset($_GET['save'])) {
		file_put_contents('final.gif', $gifBinary);
	}else{
		header('Content-type: image/gif');
		header('Content-Disposition: filename="butterfly.gif"');
		echo $gifBinary;
		exit;
	}

