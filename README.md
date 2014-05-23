> *NOTE: This is a fork of [Clément Guillemain](https://github.com/Sybio)'s nice [GifCreator class](https://github.com/Sybio/GifCreator), with some API changes (class & some methods renamed, more flexible parameters etc.), better error handling, several small corrections, code cosmetics & other minor improvements scattered all across.*

### About 

AnimGif is a PHP class to create animated GIFs -- just list the source images (in various forms), and that's it!


### Usage

**1. Creation:**

```php
// Use an array containing file paths, resource vars (initialized with imagecreatefromXXX), 
// image URLs or binary image data.
$frames = array(
    imagecreatefrompng("/../images/pic1.png"),      // resource var
    "/../images/pic2.png",                          // image file path
    file_get_contents("/../images/pic3.jpg"),       // image binary data
    "http://thisisafakedomain.com/images/pic4.jpg", // URL
);

// Or, load the image files from a dir (and sort them):
//$frames = "../images";

// Optionally, set the duration (in 1/100s units) for each frame
$durations = array(20, 30, 10, 10);

// Or, with the shorthand form (omitting the repetitions):
$durations = array(20, 30, 10);

// Initialize and create the GIF!
$anim = new GifCreator\AnimGif();
$anim->create($frames, $durations);

// Or, for just use a default 100ms even delay:
//$anim->create($frames);

// Or, for 5 repeats & then stop:
//$anim->create($frames, $durations, 5); // default: infinite looping
```

**2. Get the result:**

You can now get the animated GIF binary:

```php
$gif = $anim->get();
```

**3. Use it:**

Then you might send it directly to the browser:

```php
header("Content-type: image/gif");
echo $gif;
exit;
```

Or save it to a file:

```php
file_put_contents("animated.gif", $gif);
```


### Behavior

- Transparency is based on the first frame. [!!NOT VERIFIED: "It will be saved only if you give multiple frames with the same transparent background"]
- The dimensions of the generated GIF are based on the first frame, too. If you need to resize your images to get the same dimensions, you can use this class: https://github.com/Sybio/ImageWorkshop.


### Dependencies

* PHP 5.3 (for namespace support & whatnot; noone still shamelessly uses PHP < 5.3, right?!)
* GD (`imagecreatefromstring`, `imagegif`, `imagecolortransparent`)


### Credits

* László Zsidi: All the tough parts come from his [GIFEncoder.class.php](http://www.phpclasses.org/package/3163) (also found [here, in a Gist](https://gist.github.com/allometry/1438842)). Thanks, Laci!
* Clément Guillemain: for the very handy, redesigned (& "classified") API, extensions and nice docs!
* Matthew Flickinger: for his amazing, unbeatable [GIF format dissection page](http://www.matthewflickinger.com/lab/whatsinagif/bits_and_bytes.asp).
