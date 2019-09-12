<?php
namespace etherra;

class Util {

	static function emailValid($email){
		return preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',$email);
	}

	static function ensureFolderExists($path){
		if (!is_dir($path)){
			self::mkDirs($path);
		}
	}
	
	static function mkDirs($strPath){
        if (is_dir($strPath)) return true;
        $pStrPath = dirname($strPath);
        if (!self::mkDirs($pStrPath)) return false;
        return mkdir($strPath);
	}
	
	
    /**
	 * deletes path recursively
	 * please use with caution!!!
	 */
	static function deleteFolder($dir){
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) { 
					if ($file != "." && $file != "..") {
						if (is_file($dir.'/'.$file)) {
							unlink($dir.'/'.$file);
						}
						else {
							self::deleteFolder($dir.'/'.$file);
						}
					}
				}
				closedir($dh);
				rmdir($dir);
			}
		}		
	}


    static function GetScaledImage($im, $max_width, $max_height){
        $width = imagesx($im);
        $height = imagesy($im);
        //get new height and width
        if ($width*$max_height/$height>=$max_width){
            $new_height = $height*$max_width/$width;
            $new_width = $width*$max_width/$width;
        }else{
            $new_height = $height*$max_height/$height;
            $new_width = $width*$max_height/$height;
        }
        $new_im  = imagecreatetruecolor ($new_width, $new_height);
        imagecopyresampled($new_im,$im,0,0,0,0,$new_width,$new_height,$width,$height);
        return $new_im;
    }

    static function GetCropedImage($im, $max_width, $max_height){
        $width = imagesx($im);
        $height = imagesy($im);

        if ($width*$max_height/$height<=$max_width){
            $new_height = $height*$max_width/$width;
            $new_width = $width*$max_width/$width;
        }else{
            $new_height = $height*$max_height/$height;
            $new_width = $width*$max_height/$height;
        }

        $im2 = imagecreatetruecolor ($new_width, $new_height);
        imagecopyresampled($im2,$im,0,0,0,0,$new_width,$new_height,$width,$height);

        $src_x=($new_width-$max_width)/2;
        $src_y=($new_height-$max_height)/2;

        $new_im = imagecreatetruecolor ($max_width, $max_height);
        imagecopyresampled($new_im,$im2,0,0,$src_x,$src_y,$max_width,$max_height,$max_width,$max_height);
        return $new_im;
    }


   static function GetScaledImage2($fname_src, $fname_dest, $max_width, $max_height)
	{
		$thumb = new Imagick();
		$thumb->readImage($fname_src);
      $thumb->setCompressionQuality(95);
		
		//if 

		$thumb->thumbnailImage($max_width, $max_height, true);

      $thumb->setImageCompressionQuality(95);
		$thumb->writeImage($fname_dest);
		$thumb->destroy();
	}


   static function GetCropedImage2($fname_src, $fname_dest, $max_width, $max_height)
	{
		$thumb = new Imagick();
		$thumb->readImage($fname_src);
		$thumb->cropThumbnailImage($max_width-4,$max_height-4);

		/* if ($max_width < 300) $thumb->sharpenImage(4, 1); */

		$thumb->roundCorners(5, 5);


		$bg = new Imagick();
		$bg->newImage($max_width, $max_height, new ImagickPixel('white'));
		$bg->roundCorners(5, 5);
		$bg->compositeImage($thumb, $thumb->getImageCompose(), 2, 2);
		$bg->writeImage($fname_dest);

		$thumb->destroy();
		$bg->destroy();
	}



    
}

