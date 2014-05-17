<?php

/* CHANGES of the "lunakid fork":

TODO:
! See CHANGES.txt.

DONE:
+ Renamed encodeAsciiToChar() to word2bin() & fixed its description.
+ Added error + check: 'ERR04' => 'Loading from URLs is disabled by PHP.'.
+ file_exists() -> @is_readable() (Better to take no risk of any PHP output
  in a raw GIF transfer...)
+ Oops, also need to fix the default delay. And then also change it to 100ms.
  (Because browsers seem to have difficulties handling too fast animations.)
+ Anim delay doesn't seem to be set in ms, at all. :-o
  -> Yeah, they ARE NOT in millisecs! See: http://www.w3.org/Graphics/GIF/spec-gif89a.txt
  Fixing the docs.
1.1:
+ Fixed ERR01 "Source is not a GIF image.": there's a .png in the examples!
  -> And it does support non-GIF files actually!
  Moved the error check to resource inputs only, and changed it to
  "Resource is not a GIF image.".
+ create() should iterate $frames with foreach() not with for, assuming direct
  indexes from 0 to < count.
  (The array keys can be anything, and should not affect the results.)
+ Removed unused $mode from reporting ERR02.
+ $duration now has a meaningful default in create().
+ $frames was incorrectly made an optional arg. of create().
+ Support uniform timing without an array.
+ Separate ERR00 from $durations not being an array.
+ Fixed leftover $GIF_tim in create().
+ Renamed method getGif() to get().
+ Renamed class to AnimGif (from GifCreator).
+ Made $this->version a define (VERSION).
+ Made $this->errors really static (self::$errors).
+ Moved encodeAsciiToChar() out from the class to the namespace root as a general utility fn.
+ Moved getGif to the "public" section (and removed the Getter/Setter section).
+ Moved reset() closer up to the ctor.
+ Changed comments here & there.
+ Whitespaces: fixed some tab/space mismatch etc.
+ Changed {$i} indexes to [$i] in gifBlockCompare(), just for fun. ;)
*/

/**
 * Create an animated GIF from multiple images
 * 
 * @link https://github.com/lunakid/AnimGif
 * @author Sybio (Clément Guillemain / @Sybio01), lunakid (@GitHub, @Gmail, @SO etc.)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Clément Guillemain, Szabolcs Szász
 */

namespace GifCreator;

define('VERSION', '1.1+');

class AnimGif
{
	/**
	* @var string The generated (binary) image
	*/
	private $gif;

	/**
	* @var boolean Has the image been built or not
	*/
	private $imgBuilt;

	/**
	* @var array Frames string sources
	*/
	private $frameSources;

	/**
	* @var integer Gif loop
	*/
	private $loop;

	/**
	* @var integer Gif dis [!!?]
	*/
	private $dis;

	/**
	* @var integer Gif color
	*/
	private $colour;

	/**
	* @var array
	*/
	private static $errors;
 
	// Methods
	// ===================================================================================
    
	public function __construct()
	{
		$this->reset();

		// Static data
		self::$errors = array(
			'ERR00' => 'Cannot make animation from a single frame.',
			'ERR01' => 'Resource is not a GIF image.',
			'ERR02' => 'Only image resource variables, file paths, URLs or binary bitmap data are accepted.',
			'ERR03' => 'Cannot make animation from animated GIF.',
			'ERR04' => 'Loading from URLs is disabled by PHP.',
			'ERR05' => 'Failed to load or invalid image "%s".',
		);
	}

	/**
	 * Create the GIF string
	 * 
	 * @param array $frames An array of frame: can be file paths, resource image variables, binary sources or image URLs
	 * @param array|number $durations The duration (in 1/100s) of each frame, or a single integer for each one.
	 * @param integer $loop Number of GIF loops before stopping animation (Set 0 to get an infinite loop)
	 * 
	 * @return string The GIF string source
	 */
	public function create($frames, $durations = 10, $loop = 0)
	{
		if (!is_array($frames)) {
			throw new \Exception(VERSION.': '.self::$errors['ERR00']);
		}

		$this->loop = ($loop > -1) ? $loop : 0;
		$this->dis = 2;

		$i = 0;
		foreach ($frames as $frame) {
			if (is_resource($frame)) { // Resource var

				$resourceImg = $frame;

				ob_start();
				imagegif($frame);
				$this->frameSources[] = ob_get_contents();
				ob_end_clean();

				if (substr($this->frameSources[$i], 0, 6) != 'GIF87a' && substr($this->frameSources[$i], 0, 6) != 'GIF89a') {
					throw new \Exception(VERSION.': '.$i.' '.self::$errors['ERR01']);
				}
	
			} elseif (is_string($frame)) { // File path or URL or Binary source code
			     
				if (@is_readable($frame)) { // file path
					$bin = file_get_contents($frame);                    
				} else if (filter_var($frame, FILTER_VALIDATE_URL)) {
 					if (ini_get('allow_url_fopen')) {
						$bin = @file_get_contents($frame);
					} else {
						throw new \Exception(VERSION.': '.$i.' '.self::$errors['ERR04']);
					}
				} else {
					$bin = $frame;
				}
				
				if (! ($bin && ($resourceImg = imagecreatefromstring($frame))) )
				{
					throw new \Exception(VERSION.': '.$i.' ' 
						. sprintf(self::$errors['ERR05'], substr($frame, 0, 200))); //!! $frame may be binary data, not a name!
				}

				ob_start();
				imagegif($resourceImg);
				$this->frameSources[] = ob_get_contents();
				ob_end_clean();
		 
			} else { // Fail
				throw new \Exception(VERSION.': '.self::$errors['ERR02']);
			}

			if ($i == 0) {
				$colour = imagecolortransparent($resourceImg);
			}

			for ($j = (13 + 3 * (2 << (ord($this->frameSources[$i] { 10 }) & 0x07))), $k = TRUE; $k; $j++) {
			 
				switch ($this->frameSources[$i] { $j }) {
				    
					case '!':
		    				if ((substr($this->frameSources[$i], ($j + 3), 8)) == 'NETSCAPE') {
			    
							throw new \Exception(VERSION.': '.self::$errors['ERR03'].' ('.($i + 1).' source).');
						}
			
						break;
			
					case ';':
		    				$k = false;
						break;
				}
			}

			unset($resourceImg);

			++$i;
		}//foreach

		if (isset($colour)) {
			$this->colour = $colour;	    
		} else {
			$red = $green = $blue = 0;
			$this->colour = ($red > -1 && $green > -1 && $blue > -1) ? ($red | ($green << 8) | ($blue << 16)) : -1;
		}

		$this->gifAddHeader();

		for ($i = 0; $i < count($this->frameSources); $i++) {

			$this->addGifFrames($i, is_array($durations) ? $durations[$i] : $durations);
		}

		$this->gifAddFooter();

		return $this->gif;
	}

	/**
	 * Get the final GIF image string
	 * 
	 * @return string
	 */
	public function get()
	{
		return $this->gif;
	}
    
	// Internals
	// ===================================================================================

	/**
	 * Reset and clean the current object (only used by the ctor. currently)
	 */
	public function reset()
	{
		$this->frameSources;
		$this->gif = 'GIF89a'; // the GIF header
		$this->imgBuilt = false;
		$this->loop = 0;
		$this->dis = 2;
		$this->colour = -1;
	}
	    
	/**
	 * Add the header gif string in its source
	 */
	public function gifAddHeader()
	{
		$cmap = 0;

		if (ord($this->frameSources[0] { 10 }) & 0x80) {
		  
			$cmap = 3 * (2 << (ord($this->frameSources[0] { 10 }) & 0x07));

			$this->gif .= substr($this->frameSources[0], 6, 7);
			$this->gif .= substr($this->frameSources[0], 13, $cmap);
			$this->gif .= "!\377\13NETSCAPE2.0\3\1".word2bin($this->loop)."\0";
		}
	}
    
	/**
	 * Add the frame sources to the GIF string
	 * 
	 * @param integer $i
	 * @param integer $d
	 */
	public function addGifFrames($i, $d)
	{
		$Locals_str = 13 + 3 * (2 << (ord($this->frameSources[ $i ] { 10 }) & 0x07));

		$Locals_end = strlen($this->frameSources[$i]) - $Locals_str - 1;
		$Locals_tmp = substr($this->frameSources[$i], $Locals_str, $Locals_end);

		$Global_len = 2 << (ord($this->frameSources[0 ] { 10 }) & 0x07);
		$Locals_len = 2 << (ord($this->frameSources[$i] { 10 }) & 0x07);

		$Global_rgb = substr($this->frameSources[ 0], 13, 3 * (2 << (ord($this->frameSources[ 0] { 10 }) & 0x07)));
		$Locals_rgb = substr($this->frameSources[$i], 13, 3 * (2 << (ord($this->frameSources[$i] { 10 }) & 0x07)));

		$Locals_ext = "!\xF9\x04".chr(($this->dis << 2) + 0).chr(($d >> 0 ) & 0xFF).chr(($d >> 8) & 0xFF)."\x0\x0";

		if ($this->colour > -1 && ord($this->frameSources[$i] { 10 }) & 0x80) {
		  
			for ($j = 0; $j < (2 << (ord($this->frameSources[$i] { 10 } ) & 0x07)); $j++) {
			 
				if (ord($Locals_rgb { 3 * $j + 0 }) == (($this->colour >> 16) & 0xFF) &&
					ord($Locals_rgb { 3 * $j + 1 }) == (($this->colour >> 8) & 0xFF) &&
					ord($Locals_rgb { 3 * $j + 2 }) == (($this->colour >> 0) & 0xFF)
				) {
					$Locals_ext = "!\xF9\x04".chr(($this->dis << 2) + 1).chr(($d >> 0) & 0xFF).chr(($d >> 8) & 0xFF).chr($j)."\x0";
					break;
				}
			}
		}
        
		switch ($Locals_tmp { 0 }) {
		  
			case '!':
            
				$Locals_img = substr($Locals_tmp, 8, 10);
				$Locals_tmp = substr($Locals_tmp, 18, strlen($Locals_tmp) - 18);
                                
				break;
                
			case ',':
            
				$Locals_img = substr($Locals_tmp, 0, 10);
				$Locals_tmp = substr($Locals_tmp, 10, strlen($Locals_tmp) - 10);
                                
				break;
		}
        
		if (ord($this->frameSources[$i] { 10 }) & 0x80 && $this->imgBuilt) {
		  
			if ($Global_len == $Locals_len) {
			 
				if ($this->gifBlockCompare($Global_rgb, $Locals_rgb, $Global_len)) {
				    
					$this->gif .= $Locals_ext.$Locals_img.$Locals_tmp;
                    
				} else {
				    
					$byte = ord($Locals_img { 9 });
					$byte |= 0x80;
					$byte &= 0xF8;
					$byte |= (ord($this->frameSources[0] { 10 }) & 0x07);
					$Locals_img { 9 } = chr($byte);
					$this->gif .= $Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp;
				}
                
			} else {
			 
				$byte = ord($Locals_img { 9 });
				$byte |= 0x80;
				$byte &= 0xF8;
				$byte |= (ord($this->frameSources[$i] { 10 }) & 0x07);
				$Locals_img { 9 } = chr($byte);
				$this->gif .= $Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp;
			}
            
		} else {
		  
			$this->gif .= $Locals_ext.$Locals_img.$Locals_tmp;
		}
        
		$this->imgBuilt = true;
	}
    
	/**
	 * Add the gif string footer char
	 */
	public function gifAddFooter()
	{
		$this->gif .= ';';
	}
    
	/**
	 * Compare two block and return the version
	 * 
	 * @param string $globalBlock
	 * @param string $localBlock
	 * @param integer $length
	 * 
	 * @return integer
	 */
	public function gifBlockCompare($globalBlock, $localBlock, $length)
	{
		for ($i = 0; $i < $length; $i++) {
		  
			if ($globalBlock [ 3 * $i + 0 ] != $localBlock [ 3 * $i + 0 ] ||
			    $globalBlock [ 3 * $i + 1 ] != $localBlock [ 3 * $i + 1 ] ||
			    $globalBlock [ 3 * $i + 2 ] != $localBlock [ 3 * $i + 2 ]) {
				
				return 0;
			}
		}

		return 1;
	}
    
}

/**
 * Convert an integer to 2-byte little-endian binary data
 * 
 * @param integer $word Number to encode
 * 
 * @return string of 2 bytes representing @word as binary data
 */
function word2bin($word)
{
	return (chr($word & 0xFF).chr(($word >> 8) & 0xFF));
}
