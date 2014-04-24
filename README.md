NOTE: This is mostly just a "stylistic" fork of the nice https://github.com/Sybio/GifCreator,
for some code cosmetics (incl. some API name changes), some wording fixes (partly inherited from the original [GIFEncoder](https://gist.github.com/allometry/1438842) from Laci Zsidi), and (possibly upcoming) customizations. 
The rest below is mostly just Clément Guillemain's README (apart from API name updates, 
slight wording changes, adding the Deps. & Credits sections etc.).

# ================================
# AnimGif
# ================================

AnimGif is a PHP class to create an animated GIF from multiple images.

### For what?

This class helps you create an animated GIF image: give multiple images and their duration and that's it!

### Usage

**1 - Creation:**

```php
// Create an array containing file paths, resource var (initialized with imagecreatefromXXX), 
// image URLs or even binary code from image files.
// All sorted in order to appear.
$frames = array(
    imagecreatefrompng("/../images/pic1.png"), // Resource var
    "/../images/pic2.png", // Image file path
    file_get_contents("/../images/pic3.jpg"), // Binary source code
    'http://thisisafakedomain.com/images/pic4.jpg', // URL
);

// Create an array containing the duration (in millisecond) of each frames (in order too)
$durations = array(40, 80, 40, 20);

// Initialize and create the GIF !
$anim = new GifCreator\AnimGif();
$anim->create($frames, $durations, 5);
```
The 3rd parameter of create() method allows you to choose the number of loop of your animated gif before it stops.
In the previous example, I chose 5 loops. Set 0 (zero) to get an infinite loop.

**2 - Get the result:**

You can now get the animated GIF binary:

```php
$gifBinary = $anim->get();
```

**3 - Use it:**

Then you can send it to the browser:

```php
header('Content-type: image/gif');
header('Content-Disposition: filename="butterfly.gif"');
echo $gifBinary;
exit;
```

Or save it as a GIF file:

```php
file_put_contents('/myfolder/animated_picture.gif', $gifBinary);
```

### Behavior

- The transparency is based on the first given frame. It will be saved only if you give multiple frames with same transparent background.
- The dimensions of the generated GIF are based on the first frame. If you need to resize your frames to get the same dimension, you can use 
this class: https://github.com/Sybio/ImageWorkshop

### Credits

* László Zsidi: Important parts of his "GIFEncoder.class.php" by have been reused. (Thanks, Laci!)
* Clément Guillemain: for the very handy, redesigned (& "classified") API, extensions and nice docs.

### Dependencies

* PHP 5.3 (for namespace support)
* GD (`imagecreatefromstring`, `imagecolortransparent` etc.)
