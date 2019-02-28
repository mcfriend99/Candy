<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2018 Ore Richard Muyiwa
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	Candy-PHP
 * @author		Ore Richard Muyiwa
 * @copyright      2017 Ore Richard Muyiwa
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://candy-php.com/
 * @since	Version 1.0.0
 */

if(!defined('CANDY')){
	header('Location: /');
}


/**
 *
 * Candy image utilities.
 *
 * Candy image functions accept sizes to be specified in different formats.
 * 1. As numbers such as 125, 640 or 1280 etc.
 * 2. As em values. Such as 16em, 17em, 25em etc.
 * 3. As rem values such as 14rem, 28rem tec.
 * 4. As pixel values such as 200px, 45px tec.
 * 5. As a percentage of the original such as 50%, 45%, 30% etc.
 *
 */


/**
 *
 * Gets the new coordinates of an image crop.
 *
 * @param $image
 * @param $target_width
 * @return array
 */
function get_image_crop_coordinate($image, $target_width){

    if(is_resource($image)){
        $w = imagesx($image);
        $h = imagesy($image);
	} else {
    	list($w, $h) = getimagesize($image);
	}

    $target_width = real_size($target_width, $w);
    
	$left = 0; $top = 0; $width = 0; $height = 0;
    $p_height = ($target_width / 4) * 3;

    if($w > $h){
        $ratio = ceil($target_width / $w);

        $height = $h * $ratio;
        $width = $target_width;
        $top = ($p_height - $height) / 2;
        $left = 0;
    } else {

        $ratio = ceil($p_height / $h);

        $width = $w * $ratio;
        $height = $p_height;
        $top = 0;
        $left = ($target_width - $width) / 2;
    }

    return ["width"=>$width, "height"=>$height, "left"=>$left, "top"=>$top];
}

/**
 *
 * Returns a resize image resource.
 *
 * @param $imgSrc             File path or image resource of source image.
 * @param $width              Expected width.
 * @param int $height         Expected height. If equals zero, the image will be automatically determined.
 * @return resource           An image resource composing of the resized image.
 */
function get_resized_image($imgSrc, $width, $height = 0) { //$imgSrc can be a file or resource - Returns an image resource.
    
    $resource = false;
    
    //getting the image dimensions
	if(is_resource($imgSrc)){
		$myImage = $imgSrc;
        $width_orig = imagesx($myImage);
        $height_orig = imagesy($myImage);
        $resource = true;
	} else {
    	$myImage = imagecreatefromstring(file_get_contents($imgSrc));
        list($width_orig, $height_orig) = getimagesize($imgSrc);
	}
    
    if(!is_resource($myImage) || (!$resource && !@getimagesize($imgSrc)))
        throw new Exception('The image supplied to <strong>get_cropped_image</strong> is not valid.');
    
    if($height == 0) $height = $width;
    
    
    $width = real_size($width, $width_orig);
    $height = real_size($height, $height_orig);
    
    
    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig) {
       $new_height = $width/$ratio_orig;
       $new_width = $width;
    } else {
       $new_width = $height*$ratio_orig;
       $new_height = $height;
    }

    $x_mid = $new_width/2;  //horizontal middle
    $y_mid = $new_height/2; //vertical middle

    $process = imagecreatetruecolor(round($new_width), round($new_height));

    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
    $thumb = imagecreatetruecolor($width, $height);
    imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($width/2)), ($y_mid-($height/2)), $width, $height, $width, $height);

    try{
        imagedestroy($process);
        imagedestroy($myImage);
    } catch(Exception $e){

    }
    return apply_filters('resized_image', $thumb);
}

/**
 *
 * Returns a thumbnail of an image file or resource.
 *
 * @param $imgSrc
 * @param $width
 * @param int $height              The height of the thumbnail. Use zero (0) or leave empty to make width and height equal.
 * @return resource
 */
function get_thumbnail($imgSrc, $width, $height = 0){
    
    $resource = false;
    
    //getting the image dimensions
	if(is_resource($imgSrc)){
		$myImage = $imgSrc;
        $width_orig = imagesx($myImage);
        $height_orig = imagesy($myImage);
        $resource = true;
	} else {
    	$myImage = imagecreatefromstring(file_get_contents($imgSrc));
        list($width_orig, $height_orig) = getimagesize($imgSrc);
	}
    
    if(!is_resource($myImage) || (!$resource && !@getimagesize($imgSrc)))
        throw new Exception('The image supplied to <strong>get_cropped_image</strong> is not valid.');
    
    
    $thumbnail_width = real_size($width, $width_orig);
    
    if($height == 0)
        $thumbnail_height = $thumbnail_width;
    else
        $thumbnail_height = real_size($height, $height_orig);

    
    $ratio_orig = $width_orig/$height_orig;

    if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
       $new_height = $thumbnail_width/$ratio_orig;
       $new_width = $thumbnail_width;
    } else {
       $new_width = $thumbnail_height*$ratio_orig;
       $new_height = $thumbnail_height;
    }

    $x_mid = $new_width/2;  //horizontal middle
    $y_mid = $new_height/2; //vertical middle

    $process = imagecreatetruecolor(round($new_width), round($new_height));

    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
    $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
    imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

    try{
        imagedestroy($process);
        imagedestroy($myImage);
    } catch(Exception $e){

    }
    return apply_filters('thumbnail', $thumb);
}

/**
 *
 * Gets an image cropped into the required size from the stated offsets.
 * Set height = 0 to allow height to be automatically calculated.
 *
 * If both left and top offset are set to zero, this will work the same as get_resized_image().
 *
 * @param $imgSrc
 * @param $thumbnail_width
 * @param int $thumbnail_height
 * @param int $left_offset
 * @param int $top_offset
 * @return resource
 */
function get_cropped_image($imgSrc, $thumbnail_width, $thumbnail_height = 0, $left_offset = 0, $top_offset = 0) {
    
    $resource = false;
    
    //getting the image dimensions
	if(is_resource($imgSrc)){
		$myImage = $imgSrc;
        $width_orig = imagesx($myImage);
        $height_orig = imagesy($myImage);
        $resource = true;
	} else {
    	$myImage = imagecreatefromstring(file_get_contents($imgSrc));
        list($width_orig, $height_orig) = getimagesize($imgSrc);
	}
    
    if(!is_resource($myImage) || (!$resource && !@getimagesize($imgSrc)))
        throw new Exception('The image supplied to <strong>get_cropped_image</strong> is not valid.');
    
    
    $left_offset = real_size($left_offset, $width_orig);
    $top_offset = real_size($top_offset, $height_orig);
    
    if($thumbnail_height == 0){
        
        if($top_offset > 0)
            $new_height = real_size($thumbnail_width, $height_orig - $top_offset);
        else
            $new_height = real_size($thumbnail_width, $height_orig);
    }
    
    $new_width = real_size($thumbnail_width, $width_orig - $left_offset);

    $process = imagecreatetruecolor(round($new_width), round($new_height));

    imagecopyresampled($process, $myImage, 0, 0, $left_offset, $top_offset, $new_width, $new_height, $width_orig - $left_offset, $height_orig - $top_offset);

    try{
        imagedestroy($myImage);
    } catch(Exception $e){

    }
    return apply_filters('cropped_image', $process);
}

/**
 *
 * Fix orientation in jpeg files with wrong orientation.
 *
 * @param $filename
 * @param bool $throw_error
 */
function fix_jpeg_orientation($filename, $throw_error = true) {
    $exif = exif_read_data($filename);
    if (!empty($exif['Orientation'])) {
        $image = imagecreatefromjpeg($filename);
        
        if(!is_resource($image) || !@getimagesize($filename)) {
            if($throw_error)
                throw new Exception('The image supplied to <strong>fix_jpeg_orientation</strong> is not valid.');
            else return;
        }
        
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        imagejpeg($image, $filename);
        imagedestroy($image);
    }
}

/**
 *
 * Reduces the quality of an image by the specified percentage.
 *
 * @param $img
 * @param int $percentage
 * @param string $type
 * @return bool|resource        True or False, if $img is a file or a resource if $img is an image resource
 */
function reduce_image_quality($img, $percentage = 20, $type = 'jpeg'){
    
    $resource = false;
    
    if(is_resource($img)){
		$myImage = $img;
        $resource = true;
	} else {
    	$myImage = imagecreatefromstring(file_get_contents($img));
	}
    
    if(!is_resource($myImage) || (!$resource && !@getimagesize($img)))
        throw new Exception('The image supplied to <strong>reduce_image_quality</strong> is not valid.');

    $myImage = apply_filters('reduced_image', $myImage);
    
    switch($type){
        case 'png': 
            if($resource)
                return imagepng($myImage, null, 10 - ($percentage / 10));
            else
                return imagepng($myImage, $img, 10 - ($percentage / 10));
        case 'gif': 
            if($resource)
                return imagegif($myImage);
            else
                return imagegif($myImage, $img);
        default:
            if($resource)
                return imagejpeg($myImage, null, 100 - $percentage);
            else
                return imagejpeg($myImage, $img, 100 - $percentage);
    }
}


