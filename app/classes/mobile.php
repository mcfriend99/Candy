<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2017 Onehyr Technologies Limited
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

define('PLATFORM_WINDOWS', 'windows');
define('PLATFORM_IPHONE', 'iphone');
define('PLATFORM_IPOD', 'ipod');
define('PLATFORM_IPAD', 'ipad');
define('PLATFORM_BLACKBERRY', 'blackberry');
define('PLATFORM_BLACKBERRY_10', 'blackberry_10');
define('PLATFORM_SYMBIAN', 'symbian_series60');
define('PLATFORM_SYMBIAN_S40', 'symbian_series40');
define('PLATFORM_J2ME_MIDP', 'j2me_midp');
define('PLATFORM_ANDROID', 'android');
define('PLATFORM_ANDROID_TABLET', 'android_tablet');
define('PLATFORM_FIREFOX_OS', 'firefoxOS');
define('PLATFORM_MOBILE_GENERIC', 'mobile_generic');

class Mobile {

	public $userAgent = ''; //Shortcut to the browser User Agent String
	public $userAgentString = ''; //Shortcut to the browser User Agent String
	public $matchedPlatformName = ''; //Matched platform name. False otherwise.
	public $matchedUserAgentName = ''; //Matched UA String. False otherwise.

	function __construct(){
		$this->userAgentString = server('http_user_agent');

		$this->initForTest();
	}

	function initForTest(){

		$this->matchedPlatformName = '';
		$this->matchedUserAgentName = '';

		$this->userAgent = strtolower($this->userAgentString);
		$this->getPlatformName();
		$this->getMobileUserAgentName();
	}

	/**
	 * This method detects the mobile User Agent name.
	 *
	 * @return string The matched User Agent name, false otherwise.
	 */
	function getMobileUserAgentName() {

		if ( '' != $this->matchedUserAgentName )
			return $this->matchedUserAgentName;

		if ( '' == $this->userAgent )
			return false;

		if( $this->isChromeForIOS() )
			$this->matchedUserAgentName = 'chrome-for-ios';
		else if( $this->isTwitterForIpad() )
			$this->matchedUserAgentName =  'twitter-for-ipad';
		else if( $this->isTwitterForIphone() )
			$this->matchedUserAgentName =  'twitter-for-iphone';
		else if( $this->isIPhoneOrIPod() )
			$this->matchedUserAgentName = 'iphone';
		else if ( $this->isIPad() )
			$this->matchedUserAgentName = 'ipad';
		else if( $this->isAndroidTablet() )
			$this->matchedUserAgentName = 'android_tablet';
		else if( $this->isAndroid() )
			$this->matchedUserAgentName = 'android';
		else if( $this->isBlackberry10() )
			$this->matchedUserAgentName = 'blackberry_10';
		else if( strpos($this->userAgent, 'blackberry') > -1 )
			$this->matchedUserAgentName = 'blackberry';
		else if( $this->isBlackberryTablet() )
			$this->matchedUserAgentName = 'blackberry_tablet';
		else if( $this->isWindowsPhone7() )
			$this->matchedUserAgentName = 'win7';
		else if( $this->isWindowsPhone8() )
			$this->matchedUserAgentName = 'winphone8';
		else if( $this->isOperaMini() )
			$this->matchedUserAgentName = 'opera-mini';
		else if( $this->isOperaMobile() )
			$this->matchedUserAgentName = 'opera-mobi';
		else if( $this->isKindleFire() )
			$this->matchedUserAgentName = 'kindle-fire';
		else if( $this->isSymbianPlatform() )
			$this->matchedUserAgentName = 'series60';
		else if( $this->isFirefoxMobile() )
			$this->matchedUserAgentName = 'firefox_mobile';
		else if( $this->isFirefoxOS() )
			$this->matchedUserAgentName = 'firefoxOS';
		else if( $this->isFacebookForIphone() )
			$this->matchedUserAgentName = 'facebook-for-iphone';
		else if( $this->isFacebookForIpad() )
			$this->matchedUserAgentName = 'facebook-for-ipad';
		else if( $this->isWordPressForIos())
			$this->matchedUserAgentName = 'ios-app';
		else if( strpos($this->userAgent, 'iphone') > -1 )
			$this->matchedUserAgentName = 'iphone-unknown';
		else if( strpos($this->userAgent, 'ipad') > -1 )
			$this->matchedUserAgentName = 'ipad-unknown';

		return $this->matchedUserAgentName ;
	}

	function getPlatformName() {

		if ( '' != $this->matchedPlatformName )
			return $this->matchedPlatformName;

		if ( '' == $this->userAgent )
			return false;

		if( strpos($this->userAgent, 'windows ce') > -1 || strpos($this->userAgent, 'windows phone') > -1) {
			$this->matchedPlatformName = PLATFORM_WINDOWS;
		} else if( strpos($this->userAgent, 'ipad') > -1 ) {
			$this->matchedPlatformName = PLATFORM_IPAD;
		} else if( strpos($this->userAgent, 'ipod') > -1 ) {
			$this->matchedPlatformName = PLATFORM_IPOD;
		} else if( strpos($this->userAgent, 'iphone') > -1 ) {
			$this->matchedPlatformName = PLATFORM_IPHONE;
		} else if( strpos($this->userAgent, 'android') > -1 ) {
			if ( $this->isAndroidTablet() )
				$this->matchedPlatformName = PLATFORM_ANDROID_TABLET;
			else
				$this->matchedPlatformName = PLATFORM_ANDROID;
		} else if( $this->isKindleFire() ) {
			$this->matchedPlatformName = PLATFORM_ANDROID_TABLET;
		} else if( $this->isBlackberry10() ) {
			$this->matchedPlatformName = PLATFORM_BLACKBERRY_10;
		} else if( strpos($this->userAgent, 'blackberry') > -1 ) {
			$this->matchedPlatformName = PLATFORM_BLACKBERRY;
		} else if( $this->isBlackberryTablet() ) {
			$this->matchedPlatformName = PLATFORM_BLACKBERRY;
		} else if( $this->isSymbianPlatform() ) {
			$this->matchedPlatformName = PLATFORM_SYMBIAN;
		} else if( $this->isSymbianS40Platform() ) {
			$this->matchedPlatformName = PLATFORM_SYMBIAN_S40;
		} else if( $this->isJ2MEPlatform() ) {
			$this->matchedPlatformName = PLATFORM_J2ME_MIDP;
		} else if ($this->isFirefoxOS()) {
			$this->matchedPlatformName = PLATFORM_FIREFOX_OS;
		} else if ($this->isFirefoxMobile()) {
			$this->matchedPlatformName = PLATFORM_MOBILE_GENERIC;
		}

		return $this->matchedPlatformName;
	}

	/**
	 * Detect the blackBerry OS version.
	 *
	 * Note: This is for smartphones only. Do not work on BB tablets.
	 *
	 */
	 function getBlackBerryOSVersion() {

		if ( '' == $this->userAgent )
			return false;

		if( $this->isBlackberry10() )
			return '10';

		if( strpos($this->userAgent, 'blackberry') == -1)
			return false;

		$rv = -1; // Return value assumes failure.
		if ( strpos($this->userAgent, 'webkit') > -1 ) { //detecting the BB OS version for devices running OS 6.0 or higher
			$re  = '/Version\/([\d\.]+)/i';
		} else {
			//blackberry devices <= 5.XX
			//BlackBerry9000/5.0.0.93 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/179
			$re  = '/BlackBerry\w+\/([\d\.]+)/i';
		}
		if (preg_match($re, $this->userAgent, $match) != false)
			$rv =  $match[0][0];

		if( -1 == $rv )
			return false;

		return $rv;
	}

	/**
	 * Detects if the current UA is iPhone Mobile Safari or another iPhone or iPod Touch Browser.
	 */
	function isIPhoneOrIPod() {

		if ( false == $this->userAgent )
			return false;

		$isIphone = ( strpos($this->userAgent, 'iphone') > -1 || strpos($this->userAgent, 'ipod') > -1 );
		$isSafari = ( strpos($this->userAgent, 'safari') > -1 );

		return( $isIphone && $isSafari );
	}

	/**
	 * Detects if the current device is an iPad.
	 */
	function isIPad() {

		if ( false == $this->userAgent )
			return false;

		return( strpos($this->userAgent, 'ipad') > -1 && strpos($this->userAgent, 'safari') > -1);
	}

	/**
	*  Detects if the current UA is Chrome for iOS
	*
	*/
	function isChromeForIOS() {

		if ( false == $this->userAgent )
			return false;

		return( $this->isIPhoneOrIPod() && strpos($this->userAgent, 'crios/') > -1);
	}

	/**
     * Detects if the current browser is the Native Android browser.
     * @return boolean true if the browser is Android otherwise false
     */
	 function isAndroid() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'android') > -1 ) {
			if ( $this->isOperaMini() || $this->isOperaMobile() || $this->isFirefoxMobile() )
				return false;
			else
				return true;
		}
		return false;
	}

	/**
	 * Detects if the current browser is the Native Android Tablet browser.
	 * 	Assumes 'Android' should be in the user agent, but not 'mobile'
	 *
	 * @return boolean true if the browser is Android and not 'mobile' otherwise false
	 */
	 function isAndroidTablet() {

		if ( false == $this->userAgent )
			return false;

		if( strpos($this->userAgent, 'android') > -1 && strpos($this->userAgent, 'mobile') == -1) {
			if ( $this->isOperaMini() || $this->isOperaMobile() || $this->isFirefoxMobile() )
				return false;
			else
				return true;
		}
		return false;
	}

	/**
	 * Detects if the current browser is Opera Mobile
	 *
	 * What is the difference between Opera Mobile and Opera Mini?
	 * - Opera Mobile is a full Internet browser for mobile devices.
	 * - Opera Mini always uses a transcoder to convert the page for a small display.
	 * (it uses Opera advanced server compression technology to compress web content before it gets to a device.
	 *  The rendering engine is on Opera's server.)
	 *
	 * Opera/9.80 (Windows NT 6.1; Opera Mobi/14316; U; en) Presto/2.7.81 Version/11.00"
	 */
	function isOperaMobile() {

		if ( false == $this->userAgent )
			return false;

		return( strpos($this->userAgent, 'opera') > -1 && strpos($this->userAgent, 'mobi') > -1);
	}

	/**
	 * Detects if the current browser is Opera Mini
	 *
	 * Opera/8.01 (J2ME/MIDP; Opera Mini/3.0.6306/1528; en; U; ssr)
	 * Opera/9.80 (Android;Opera Mini/6.0.24212/24.746 U;en) Presto/2.5.25 Version/10.5454
	 * Opera/9.80 (iPhone; Opera Mini/5.0.019802/18.738; U; en) Presto/2.4.15
	 * Opera/9.80 (J2ME/iPhone;Opera Mini/5.0.019802/886; U; ja) Presto/2.4.15
	 * Opera/9.80 (J2ME/iPhone;Opera Mini/5.0.019802/886; U; ja) Presto/2.4.15
	 * Opera/9.80 (Series 60; Opera Mini/5.1.22783/23.334; U; en) Presto/2.5.25 Version/10.54
	 * Opera/9.80 (BlackBerry; Opera Mini/5.1.22303/22.387; U; en) Presto/2.5.25 Version/10.54
	 *
	 */
	function isOperaMini() {

		if ( false == $this->userAgent )
			return false;

		return( strpos($this->userAgent, 'opera') > -1 && strpos($this->userAgent, 'mini') > -1);
	}


	/**
	 * isBlackberry10() can be used to check the User Agent for a BlackBerry 10 device.
	 */
	function isBlackberry10() {

		if ( false == $this->userAgent )
			return false;

		return( strpos($this->userAgent, 'bb10') > -1 && strpos($this->userAgent, 'mobile') > -1);
	}

	/**
	 * isBlackberryTablet() can be used to check the User Agent for a RIM blackberry tablet
	 * The user agent of the BlackBerry® Tablet OS follows a format similar to the following:
	 * Mozilla/5.0 (PlayBook; U; RIM Tablet OS 1.0.0; en-US) AppleWebKit/534.8+ (KHTML, like Gecko) Version/0.0.1 Safari/534.8+
	 *
	 */
	 function isBlackberryTablet() {

		if ( false == $this->userAgent )
			return false;

		return( strpos($this->userAgent, 'playbook') > -1 && strpos($this->userAgent, 'rim tablet') > -1);
	}

	/**
	 * Detects if the current browser is a Windows Phone 7 device.
	 * ex: Mozilla/4.0 (compatible; MSIE 7.0; Windows Phone OS 7.0; Trident/3.1; IEMobile/7.0; LG; GW910)
	 */
	function isWindowsPhone7() {

		if ( false == $this->userAgent )
			return false;

		return ( strpos($this->userAgent, 'windows phone os 7') > -1 );
	}

	/**
	 * Detects if the current browser is a Windows Phone 8 device.
	 * ex: Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; ARM; Touch; IEMobile/10.0; <Manufacturer>; <Device> [;<Operator>])
	 */
	function isWindowsPhone8() {

		if ( false == $this->userAgent )
			return false;

		return ( strpos($this->userAgent, 'windows phone 8') > -1 );
	}

	/**
	 *
	 * Detects if the device platform is J2ME.
	 *
	 */
	function isJ2MEPlatform() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'j2me/midp') > -1 )
			return true;

		if ( strpos($this->userAgent, 'midp') > -1 && strpos($this->userAgent, 'cldc') > -1 )
			return true;

		return false;
	}

	/**
	 *
	 * Detects if the device platform is the Symbian Series 40.
	 * Nokia Browser for Series 40 is a proxy based browser, previously known as Ovi Browser.
	 * This browser will report 'NokiaBrowser' in the header, however some older version will also report 'OviBrowser'.
	 *
	 */
	function isSymbianS40Platform() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'series40') > -1 ) {
			if ( strpos($this->userAgent, 'nokia') > -1 || strpos($this->userAgent, 'ovibrowser') > -1 || strpos($this->userAgent, 'nokiabrowser') > -1)
				return true;
		}

		return false;
	}

	/**
	 *
	 * Detects if the device platform is the Symbian Series 60.
	 *
	 */
	function isSymbianPlatform() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'webkit') > -1 ) {
			//First, test for WebKit, then make sure it's either Symbian or S60.
			if ( strpos($this->userAgent, 'symbian') > -1 || strpos($this->userAgent, 'series60') > -1 )
				return true;
			else
				return false;
		} else if ( strpos($this->userAgent, 'symbianos') > -1 && strpos($this->userAgent, 'series60') > -1 ) {
			return true;
		} else if ( strpos($this->userAgent, 'nokia') > -1 && strpos($this->userAgent, 'series60') > -1 ) {
			return true;
		} else if (  strpos($this->userAgent, 'opera mini') > -1) {
			if( strpos($this->userAgent, 'symbianos') > -1 || strpos($this->userAgent, 'symbos') > -1 || strpos($this->userAgent, 'series 60') > -1 )
				return true;
		}

		return false;
	}

	/**
	 * Detects if the current browser is the Kindle Fire Native browser.
	 *
	 * Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-us; Silk/1.1.0-84) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16 Silk-Accelerated=true
	 * Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-us; Silk/1.1.0-84) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16 Silk-Accelerated=false
	 *
	 * @return boolean true if the browser is Kindle Fire Native browser otherwise false
	 */
	function isKindleFire() {

		if ( false == $this->userAgent )
			return false;

		return( strpos($this->userAgent, 'silk/') > -1 && strpos($this->userAgent, 'silk-accelerated=') > -1);
	}

	/**
	 * Detects if the current browser is Firefox Mobile (Fennec)
	 *
	 * http://www.userAgentstring.com/pages/Fennec/
	 * Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.1.1) Gecko/20110415 Firefox/4.0.2pre Fennec/4.0.1
	 * Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1b2pre) Gecko/20081015 Fennec/1.0a1
	 */
	function isFirefoxMobile() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'fennec') > -1 )
			return true;

		return false;
	}

	/**
	 * Detects if the current browser is the native FirefoxOS browser
	 *
	 * Mozilla/5.0 (Mobile; rv:14.0) Gecko/14.0 Firefox/14.0
	 *
	 */
	function isFirefoxOS() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'mozilla') > -1 && strpos($this->userAgent, 'mobile') > -1 && strpos($this->userAgent, 'gecko') > -1 &&  strpos($this->userAgent, 'firefox') > -1 )
			return true;

		return false;
	}


	/**
	 * Detects if the current UA is Facebook for iPad
	 * - Facebook 4020.0 (iPad; iPhone OS 5.0.1; en_US)
	 * - Mozilla/5.0 (iPad; U; CPU iPhone OS 5_0 like Mac OS X; en_US) AppleWebKit (KHTML, like Gecko) Mobile [FBAN/FBForIPhone;FBAV/4.0.2;FBBV/4020.0;FBDV/iPad2,1;FBMD/iPad;FBSN/iPhone OS;FBSV/5.0;FBSS/1; FBCR/;FBID/tablet;FBLC/en_US;FBSF/1.0]
	 * - Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Mobile/10A403 [FBAN/FBIOS;FBAV/5.0;FBBV/47423;FBDV/iPad2,1;FBMD/iPad;FBSN/iPhone OS;FBSV/6.0;FBSS/1; FBCR/;FBID/tablet;FBLC/en_US]
	 */
	function isFacebookForIpad() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'ipad') == -1 )
			return false;

		if ( strpos($this->userAgent, 'facebook') > -1 || strpos($this->userAgent, 'fbforiphone') > -1 ||  strpos($this->userAgent, 'fban/fbios;') > -1 )
			return true;

		return false;
	}

	/**
	 * Detects if the current UA is Facebook for iPhone
	 * - Facebook 4020.0 (iPhone; iPhone OS 5.0.1; fr_FR)
	 * - Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_0 like Mac OS X; en_US) AppleWebKit (KHTML, like Gecko) Mobile [FBAN/FBForIPhone;FBAV/4.0.2;FBBV/4020.0;FBDV/iPhone3,1;FBMD/iPhone;FBSN/iPhone OS;FBSV/5.0;FBSS/2; FBCR/O2;FBID/phone;FBLC/en_US;FBSF/2.0]
	 * - Mozilla/5.0 (iPhone; CPU iPhone OS 5_1_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B206 [FBAN/FBIOS;FBAV/5.0;FBBV/47423;FBDV/iPhone3,1;FBMD/iPhone;FBSN/iPhone OS;FBSV/5.1.1;FBSS/2; FBCR/3ITA;FBID/phone;FBLC/en_US]
	 */
	function isFacebookForIphone() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'iphone') == -1 )
			return false;

		if ( strpos($this->userAgent, 'facebook') > -1 && strpos($this->userAgent, 'ipad') == -1  )
			return true;
		else if ( strpos($this->userAgent, 'fbforiphone') > -1 && strpos($this->userAgent, 'tablet') == -1  )
			return true;
		else if ( strpos($this->userAgent, 'fban/fbios;') > -1 && strpos($this->userAgent, 'tablet') == -1  ) //FB app v5.0 or higher
			return true;

		return false;
	}

	/**
	 *  Detects if the current UA is Twitter for iPhone
	 *
	 * Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_5 like Mac OS X; nb-no) AppleWebKit/533.17.9 (KHTML, like Gecko) Mobile/8L1 Twitter for iPhone
	 * Mozilla/5.0 (iPhone; CPU iPhone OS 5_1_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B206 Twitter for iPhone
	 */
	function isTwitterForIphone() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'ipad') > -1 )
			return false;

		if ( strpos($this->userAgent, 'twitter for iphone') > -1 )
			return true;

		return false;
	}

	/**
	 * Detects if the current UA is Twitter for iPad
	 *
	 * Old version 4.X - Mozilla/5.0 (iPad; U; CPU OS 4_3_5 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Mobile/8L1 Twitter for iPad
	 * Ver 5.0 or Higher - Mozilla/5.0 (iPad; CPU OS 5_1_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B206 Twitter for iPhone
	 */
	function isTwitterForIpad() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'twitter for ipad') > -1 )
			return true;

		if ( strpos($this->userAgent, 'ipad') > -1 && strpos($this->userAgent, 'twitter for iphone') > -1  )
			return true;

		return false;
	}


	/**
	 *  Detects if the current UA is WordPress for iOS
	 */
	function isWordPressForIos() {

		if ( false == $this->userAgent )
			return false;

		if ( strpos($this->userAgent, 'wp-iphone') > -1 )
			return true;

		return false;
	}
}



