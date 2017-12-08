> *NOTE: This is a fork of [Clément Guillemain](https://github.com/Sybio)'s nice [GifCreator class](https://github.com/Sybio/GifCreator), with some API changes (class rename, new & updated methods, more flexible (and robust) parameter handling etc.), better error handling, several small corrections, code cosmetics & other improvements scattered all across.*

### About 

AnimGif is a PHP class to create animated GIFs -- just list the source images (in various forms), and that's it!


### Usage

**1. Inputs:**

```php
// Use an array containing file paths, resource vars (initialized with imagecreatefromXXX), 
// image URLs or binary image data.
$frames = array(
    imagecreatefrompng("/../images/pic1.png"),      // resource var
    "/../images/pic2.png",                          // image file path
    file_get_contents("/../images/pic3.jpg"),       // image binary data
    "http://thisisafakedomain.com/images/pic4.jpg", // URL
);

// Or: load images from a dir (sorted, skipping .files):
//$frames = "../images";

// Optionally: set different durations (in 1/100s units) for each frame
$durations = array(20, 30, 10, 10);

// Or: you can leave off repeated values from the end:
//$durations = array(20, 30, 10); // use 10 for the rest
// Or: use 'null' anywhere to re-apply the previous delay:
//$durations = array(250, null, null, 500);
```

**2. Create the GIF:**

``` php
$anim = new GifCreator\AnimGif();
$anim->create($frames, $durations);

// Or: using the default 100ms even delay:
//$anim->create($frames);

// Or: loop 5 times, then stop:
//$anim->create($frames, $durations, 5); // default: infinite looping
```

**3. Get/use the result:**

You can now get the animated GIF binary:

```php
$gif = $anim->get();
```

...and e.g. send it directly to the browser:

```php
header("Content-type: image/gif");
echo $gif;
exit;
```

Or just save it to a file:

```php
$anim->save("animated.gif");
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
