<?php

/* [!!UNTESTED!!] CHANGES by lunakid:
+ Made $this->version a define (VERSION).
+ Made $this->errors really static (self::$errors).
+ Moved encodeAsciiToChar() out from the class to the namespace root as a general utility fn.
+ Moved getGif to the "public" section (and removed the Getter/Setter section).
+ Moved reset() closer up to the ctor.
+ Changed comments here & there.
+ Whitespaces: fixed some tab/space mismatch etc.
+ Changed {$i} indexes to [$i] in gifBlockCompare(). (Some more left.)
+ Renamed class to AnimGif (from GifCreator).
+ Renamed method getGif() to get().
*/

/**
 * Create an animated GIF from multiple images
 * 
 * @link https://github.com/lunakid/GifCreator
 * @author Sybio (Clément Guillemain / @Sybio01), lunakid (@GitHub, @Gmail, @SO etc.)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Clément Guillemain, Szabolcs Szász
 */


namespace GifCreator;

define(VERSION, '1.1-lunakid');

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
			//!! REMOVE ERR00 by auto-creating an array by default:
			'ERR00' => 'Not supported with only one source image.',
			'ERR01' => 'Source is not a GIF image.',
			'ERR02' => 'You have to give image resource variables, image URLs or image binary sources in the $frames array.',
			'ERR03' => 'Cannot make animation from animated GIF source.',
		);
	}

	/**
	 * Create the GIF string
	 * 
	 * @param array $frames An array of frame: can be file paths, resource image variables, binary sources or image URLs
	 * @param array $durations An array containing the duration of each frame
	 * @param integer $loop Number of GIF loops before stopping animation (Set 0 to get an infinite loop)
	 * 
	 * @return string The GIF string source
	 */
	public function create($frames = array(), $durations = array(), $loop = 0)
	{
		if (!is_array($frames) && !is_array($GIF_tim)) {
			throw new \Exception(VERSION.': '.self::$errors['ERR00']);
		}

		$this->loop = ($loop > -1) ? $loop : 0;
		$this->dis = 2;

		for ($i = 0; $i < count($frames); $i++) {
		  
			if (is_resource($frames[$i])) { // Resource var

				$resourceImg = $frames[$i];

				ob_start();
				imagegif($frames[$i]);
				$this->frameSources[] = ob_get_contents();
				ob_end_clean();

			} elseif (is_string($frames[$i])) { // File path or URL or Binary source code
			     
				if (file_exists($frames[$i]) || filter_var($frames[$i], FILTER_VALIDATE_URL)) { // File path
					$frames[$i] = file_get_contents($frames[$i]);                    
				}

				$resourceImg = imagecreatefromstring($frames[$i]);

				ob_start();
				imagegif($resourceImg);
				$this->frameSources[] = ob_get_contents();
				ob_end_clean();
		 
			} else { // Fail
				throw new \Exception(VERSION.': '.self::$errors['ERR02'].' ('.$mode.')');
			}

			if ($i == 0) {
				$colour = imagecolortransparent($resourceImg);
			}

			if (substr($this->frameSources[$i], 0, 6) != 'GIF87a' && substr($this->frameSources[$i], 0, 6) != 'GIF89a') {
				throw new \Exception(VERSION.': '.$i.' '.self::$errors['ERR01']);
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
		}//for

		if (isset($colour)) {
			$this->colour = $colour;	    
		} else {
			$red = $green = $blue = 0;
			$this->colour = ($red > -1 && $green > -1 && $blue > -1) ? ($red | ($green << 8) | ($blue << 16)) : -1;
		}

		$this->gifAddHeader();

		for ($i = 0; $i < count($this->frameSources); $i++) {
		  
			$this->addGifFrames($i, $durations[$i]);
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
			$this->gif .= "!\377\13NETSCAPE2.0\3\1".encodeAsciiToChar($this->loop)."\0";
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

		$Global_rgb = substr($this->frameSources[0], 13, 3 * (2 << (ord($this->frameSources[0] { 10 }) & 0x07)));
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
 * Encode an ASCII char into a string char
 * 
 * $param integer $char ASCII char
 * 
 * @return string
 */
function encodeAsciiToChar($char)
{
	return (chr($char & 0xFF).chr(($char >> 8) & 0xFF));
}
