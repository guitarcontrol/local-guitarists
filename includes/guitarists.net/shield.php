<?php
    /**
    * Data Shield Class
    *
    * Data Shield for protecting and validating data when transmitting
    * between pages and forms.
    *
    * Copyright (C) 2005 Oliver Lillie
    * 
    * This library is free software; you can redistribute it and/or
    * modify it under the terms of the GNU Lesser General Public
    * License as published by the Free Software Foundation; either
    * version 2.1 of the License, or (at your option) any later version.
    *
    * This library is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    * Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public
    * License along with this library; if not, write to the Free Software
    * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
    *
    * @link http://www.buggedcom.co.uk/
    * @author Oliver Lillie, buggedcom <publicmail at buggedcom dot co dot uk>
    * @history -----------------------------------------------------------------
    * 0.6 07/07/2005 - Added timeout param so link is only valid for a certain
    *                    amount of time after which it fails.
    *                 - Added zlib checking so not used if not available
    * 0.5 04/07/2005 - Added private function for removing globals and magic
    *                    quotes. set in the constructor
    * 0.4 01/07/2005 - Added expunge and make_secure functions
    * 0.3 29/06/2005 - Marc Wöen has made suggestions and improved some stuff
    *                 - Added new shield type - PURE_GET
    *                 - removed random seeding
    *                 - added filename safe rfc3548 encoding and decoding
    *                 - changed default algorithm to twofish, apparently it is 30% 
    *                    faster
    * 0.2 28/06/2005 - Typos corrected thanks to Greg Winterstein
    * 0.1 26/06/2005 - Created
    */

    class shield {
    
        /**
        * PUBLIC VARS
        **/

        /**
        * hash key used to encrypt the url data.
        *
        * @var string
        */
         var $HASH_KEY         = 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aV';
         
        /**
        * Digital signature md5'd into the var for validation
        *
        * @var string
        */
         var $DIGITAL_SIG     = 'I once had a pet called Jim';
                  
        /**
        * The algorithm to be used by mcrypt. Marc Wöen believes that twofish
        * is a faster and lighter algorithm.
        *
        * @var string
        */
        var $ALGORITHM        = 'twofish';

        /**
        * The mode to be used by mcrypt
        *
        * @var string
        */
        var $MODE            = 'ecb';
        
        /**
        * The name of the var that the encoded data will take
        *
        * @var string
        */
        var $VAR_NAME        = 'shield';
        
        /**
        * PRIVATE VARS
        **/

        /**
        * Init the var used to store the directory of the script
        */
        var $_DIR;

        /**
        * Init the var used to store if the server is zlib compatible
        */
        var $_ZLIB;

        /**
        * Init the expunge data array, used to collect merge var names
        */
        var $_VAR_NAMES;

        /**
        * PUBLIC FUNCTIONS
        **/
        
        /**
        * Constructor
        *
        * @access public
        * @param $remove_register_globals bool Removes defined globals if register_globals is active
        * @param $remove_magic_quotes bool Removes magic quotes if magic quotes are active
        * @return void
        **/
        function shield($remove_register_globals=true, $remove_magic_quotes=false)
        {
            # check to see if class is secure
            $this->_check_secure();
            # check for valid mycrypt stuff
            $this->_validate_algorithm();
            # create the _DIR value
            $this->_DIR = dirname(__FILE__);
            # check for zip compat
            $this->_ZLIB = function_exists('gzdeflate');
            # run the clean up funcs if required
            if($remove_register_globals) $this->_remove_globals();
            if($remove_magic_quotes) $this->_remove_magic_quotes();
        }
        
        /**
        * protect
        *
        * protects data you want to transmit
        *
        * @access public 
        * @param mixed $data Variables you want to encrypt for transfering to another page
        * @param string $shield_type The type of return string
          *         PLAIN        - returns a plain string for input into a form value for submission
          *                        ie vreinvg3rihviv234rv23vjmosvfe
          *                        => <input type="hidden" name="shield" value="vreinvg3rihviv234rv23vjmosvfe" />
        *         GET         - returns a string for an url sending data
        *                         ie ?shield=vreinvg3rihviv234rv23vjmosvfe
        *                        => http://www.myhost.com/submit.php?shield=vreinvg3rihviv234rv23vjmosvfe
        *         PURE_GET     - returns a string for in the form var_name=encrypted_value
        *                         ie shield=vreinvg3rihviv234rv23vjmosvfe
        *                      Added by Marc Wöen
          *         INPUT        - returns a ready made form input string
          *                        ie <input type="hidden" name="shield" value="vreinvg3rihviv234rv23vjmosvfe" />
        * @param string $var_name The name of the value of the return string
        * @param string|boolean $digital_sig If you want to use a different digital signature 
        *         other than the default class one, make this the sig string, otherwise
        *         if false the default signature is used.
        * @param integer $time If you wish to create a timeout period for this var then set this to
        *          the number of milliseconds you want the var to be valid for. If left at '0' then
        *         no timeout setting will be used.
          * @return string
        **/
        function protect($data, $shield_type='PLAIN', $time=0, $var_name=false, $digital_sig=false)
        {
            # check to see if class is secure
            $this->_check_secure();

            # if data is not array make array and flag
            if(!is_array($data)) $data = array('DATA'=>$data, '_FLAG'=>1);
            
            # add the digital sig
            $data['_DIGITAL_SIG'] = !$digital_sig ? md5($this->_DIR).md5($this->DIGITAL_SIG) : md5($digital_sig);
            
            # add the timeout
            if($time>0)
            {
                $data['_TIMEOUT'] = time()+$time;
            }
            
            # run the encryption 
            $str = $this->_encrypt($data);
            
            # get the var name
            $var_name = !$var_name ? $this->VAR_NAME : $var_name;
            
            # switch through the var names
            switch($shield_type)
            {
                # Marc Wöen - Added PURE_GET to make it more easy to submit 
                # encoded and not encoded simultaneously
                case 'PURE_GET' : 
                    $str = $var_name . '=' . $str;
                    break;
                 case 'GET' : 
                    $str = '?' . $var_name . '=' . $str;
                    break;
                case 'INPUT' :
                    $str = '<input type="hidden" name="' . $var_name . '" value="' . $str . '" />';
                    break;
            }

            # return the string
            return $str;
        }
        
        /**
        * expose
        *
        * expose the data that has been protected and if required inserts it
        * into the appropriate super global arrays, it also
        * returns that protected transmitted data back into useable data
        *
        * @access public 
        * @param $str mixed The protected string, or false to get it from the $_REQUEST global var
        * @param $var_name string The name of the value of the return string
        * @param $digital_sig string|boolean If you want to use a different digital signature 
        *         other than the default class one, make this the sig string, otherwise
        *         if false the default signature is used.
          * @return mixed
        **/
        function expose($str=false, $var_name=false, $digital_sig=false, $modify=true)
        {
            # check to see if class is secure
            $this->_check_secure();

            # get the var name
            $var_name = !$var_name ? $this->VAR_NAME : $var_name;

            # get the data
            if(!$str) $str = $_REQUEST[$var_name];
            
            # run the decryption 
            $data = $this->_decrypt($str);

            # check for valid digital signature
            if($data['_DIGITAL_SIG'] != (!$digital_sig ? md5($this->_DIR).md5($this->DIGITAL_SIG) : md5($digital_sig)))
            {
                unset($_REQUEST[$var_name]);
                unset($_POST[$var_name]);
                unset($_GET[$var_name]);
                return 'SIG_404';
            }

            # check for timeout
            if(isset($data['_TIMEOUT']) && $data['_TIMEOUT'] < time()) 
            {
                unset($_REQUEST[$var_name]);
                unset($_POST[$var_name]);
                unset($_GET[$var_name]);
                return 'TIMEOUT';
            }
            
            # delete the validation data
            unset($data['_DIGITAL_SIG']);
            unset($data['_TIMEOUT']);
            
            # check for none array flag
            if(isset($data['_FLAG'])) {    
                # note if data has been flagged then that means the data encrypted was not an array
                # thus the data is not returned to the _GET of _POST array but just returned as the value
                $data = $data['DATA'];
            } else {
                # if the modify is true then it will modify the GET or POST
                # super globals
                if($modify) {
                    # modify the global properties
                    if(isset($_GET[$var_name])) {
                        unset($_GET[$var_name]);
                        $_GET = array_merge($_GET, $data);
                    }
                    if(isset($_POST[$var_name])) {
                        unset($_POST[$var_name]);
                        $_POST = array_merge($_POST, $data);
                    }
                    # modify the request data
                    unset($_REQUEST[$var_name]);
                    $_REQUEST = array_merge($_REQUEST, $data);
                    # loop through the names for the expunge data
                    foreach($data as $key=>$value) {
                        $this->_VAR_NAMES[] = $key;
                    }
                }
            }
                        
            # return the data
            return $data;
        }
        
        /**
        * expunge
        *
        * removes the data from the super global arrays
        *
        * @access public 
          * @return void
        **/
        function expunge($make_secure=false) {
            # check there are vars names to delete so no errors are thrown
            if(count($this->_VAR_NAMES) > 0) {
                # loop through the properties and remove from global arrays
                foreach($this->_VAR_NAMES as $key=>$var_name) {
                    if(isset($_GET[$var_name])) {
                        unset($_GET[$var_name]);
                    }
                    if(isset($_POST[$var_name])) {
                        unset($_POST[$var_name]);
                    }
                    if(isset($_REQUEST[$var_name])) {
                        unset($_REQUEST[$var_name]);
                    }
                }
            }
            # remove the varnames
            unset($this->_VAR_NAMES);
            # make the class secure
            if($make_secure) $this->make_secure();
        }

        /**
        * make_secure
        *
        * deletes all class values and makes class void to prevent re-writing of
        * a key;
        *
        * @access public 
        * @return void
        **/
        function make_secure() {
            # walkthrough and delete the class vars
            foreach(array_keys(get_object_vars($this)) as $value) {
                unset($this->$value);
            }
            # define that class is secure
            define('_SECURE_', 1);
        }
        
        /**
        * PRIVATE FUNCTIONS
        **/

        /**
        * _encrypt
        *
        * encrypts the key
        *
        * @access private 
        * @param $src_array array The data array that contains the key data
          * @return string Returns the encrypted string
        **/
        function _encrypt($src_array) {            
            # check to see if class is secure
            $this->_check_secure();
            # typo corrected < thanks to Greg Winterstein
            # openup mcrypt
            $td     = mcrypt_module_open($this->ALGORITHM, '', $this->MODE, '');
            $iv     = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            # process the key
            $key     = substr($this->HASH_KEY, 0, mcrypt_enc_get_key_size($td));
            # init mcrypt
            mcrypt_generic_init($td, $key, $iv);
            
            # encrypt data
            # Use gzip to reduce URL size introduced by Marc Wöen
            # lib checking added later
            $data     = $this->_ZLIB ? gzdeflate(serialize($src_array)) : serialize($src_array);
            $crypt     = mcrypt_generic($td, $data);
        
            # shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            
            # return the key
            # MW. Use RFC 3548 "Base 64 Encoding with URL and Filename 
            # Safe Alphabet"introduced by Marc Wöen
            return $this->_rfc3548_encode(trim($crypt));
        }
        
        /**
        * _decrypt
        *
        * decrypts the key
        *
        * @access private 
        * @param $enc_string string The key string that contains the data
          * @return array Returns decrypted array
        **/
        function _decrypt($enc_string) {
            # check to see if class is secure
            $this->_check_secure();
            # Use RFC 3548 "Base 64 Encoding with URL and Filename 
            # Safe Alphabet" introduced by Marc Wöen
            $enc_string = $this->_rfc3548_decode($enc_string);
            # typo corrected < thanks to Greg Winterstein
            # openup mcrypt
            $td     = mcrypt_module_open($this->ALGORITHM, '', $this->MODE, '');
            $iv     = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            # process the key
            $key     = substr($this->HASH_KEY, 0, mcrypt_enc_get_key_size($td));
            # init mcrypt
            mcrypt_generic_init($td, $key, $iv);

            # decrypt the data and return
            $decrypt = mdecrypt_generic($td, $enc_string);

            # shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            # return the key
            # gzip compression added by Marc Wöen
            # lib checking added later
            $decrypt = $this->_ZLIB ? gzinflate($decrypt) : $decrypt;
            return unserialize($decrypt);
        }
        
        /**
        * _remove_globals
        *
        * remove globals if register_globals is on
        * borrowed from http://www.phpguru.org/#58
        *
        * @access private 
        * @return void
        **/
        function _remove_globals() {
            if (ini_get('register_globals')) { 
                foreach ($_REQUEST as $k => $v) { 
                    unset($GLOBALS[$k]); 
                } 
            } 
        }

        /**
        * _remove_magic_quotes
        *
        * remove magic quotes if magic_quotes_gpc is on
        * borrowed from http://www.phpguru.org/#58
        *
        * @access private 
        * @return void
        **/
        function _remove_magic_quotes() {
            if (ini_get('magic_quotes_gpc')) { 
                foreach (array('_GET', '_POST', '_COOKIE') as $super) { 
                    foreach ($GLOBALS[$super] as $k => $v) { 
                        $GLOBALS[$super][$k] = _stripslashes_r($v); 
                    } 
                } 
            } 
        }

        /**
        * _stripslashes_r
        *
        * Recursive stripslashes. array_walk_recursive seems to have great 
        * trouble with stripslashes().
        * borrowed from http://www.phpguru.org/#58
        *
        * @access private 
        * @param  mixed $str String or array 
        * @return mixed      String or array with slashes removed 
        **/
        function _stripslashes_r($str) { 
            if (is_array($str)) { 
                foreach ($str as $k => $v) { 
                    $str[$k] = stripslashes_r($v); 
                } 
                return $str; 
            } else { 
                return stripslashes($str); 
            } 
        } 
        
        /**
        * _trigger_error
        *
        * triggers an error
        *
        * @access private 
        * @param $message string The string to read in the error.
        * @param $fatal boolean If true script termination occurs.
        * @return void
        **/
        function _trigger_error($message, $fatal=true) {
                trigger_error("<br /><br /><span style='color: #F00;font-weight: bold;'>".$message."<br /><br /></span>", E_USER_ERROR);
                if($fatal) exit;
        }
        
        /**
        * _validate_algorithm
        *
        * validates the mcrypt settings
        *
        * @access private 
        * @return void
        **/
        function _validate_algorithm() {
            if(!function_exists('mcrypt_module_open')) $this->_trigger_error("In order to function this script needs to use the PHP Library '<a href='http://www.php.net/mcrypt' target='_blank'>Mcrypt<a/>'. Unfortunately Mcrypt is not present on your PHP installation. Please contact your server administrator for further advice.");
            if(!in_array($this->ALGORITHM, mcrypt_list_algorithms())) $this->_trigger_error("In order to function this script uses the PHP Library '<a href='http://www.php.net/mcrypt' target='_blank'>Mcrypt<a/>'. Unfortunately Mcrypt whilst present on your PHP installation does not have access to the '".$this->ALGORITHM."' algorithm. Please contact your server administrator for further advice.");
            if(!in_array($this->MODE, mcrypt_list_modes())) $this->_trigger_error("In order to function this script uses the PHP Library '<a href='http://www.php.net/mcrypt' target='_blank'>Mcrypt<a/>'. Unfortunately Mcrypt whilst present on your PHP installation does not have access to the '".$this->MODE."' mode. Please contact your server administrator for further advice.");
        }
        
        
        /**
        * _rfc3548_encode
        *
        * replaces double base64_encoding
        * credits to Marc Wöen
        *
        * @access private 
        * @return void
        **/
        function _rfc3548_encode($str) {
            return str_replace('/','_',str_replace('+','-',base64_encode($str)));
        }
        
        /**
        * _rfc3548_decode
        *
        * replaces double base64_decoding
        * credits to Marc Wöen
        *
        * @access private 
        * @return void
        **/
        function _rfc3548_decode($str) {
            return base64_decode(str_replace('_','/',str_replace('-','+',$str)));
        }
        
        /**
        * _check_secure
        *
        * checks to see if the class has been made secure
        *
        * @access private 
        **/
        function _check_secure() {
            if(defined('_SECURE_')) $this->_trigger_error('Shield Class has been made secure. This script has been terminated.');
        }
    }
?>
