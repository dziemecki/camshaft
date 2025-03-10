<?PHP
/* Globally required functions */
require_once("../site/site.conf.php");

define("_THEME_MAIN", "../themes/" . _THEME ."/main.tpl.php");
define("_THEME_NOWRAP", "../themes/" . _THEME ."/nowrap.tpl.php");

$_CORE_INCLUDE = array();
if(_ENABLE_HOSTED_INCLUDES){
	// Google\Microsoft hosted JQuery.
	$_CORE_INCLUDE[] = "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>";
	$_CORE_INCLUDE[] = "<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js'></script>";
	$_CORE_INCLUDE[] = "<link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css'>";
	$_CORE_INCLUDE[] = "<script src='http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js'></script>";
	$_CORE_INCLUDE[] = "<script src='https://code.jquery.com/jquery-migrate-3.0.0.min.js'></script>";
} else {
	// Local JQuery.  Move from 3rd party to root to use this.
	$_CORE_INCLUDE[] = "<script src='/jquery/external/jquery/jquery.js'></script>";
	$_CORE_INCLUDE[] = "<link rel='stylesheet' href='/jquery/jquery-ui.css'>";
	$_CORE_INCLUDE[] = "<script src='/jquery/jquery-ui.js'></script>";	
	$_CORE_INCLUDE[] = "<script src='/jquery/external/jquery/jquery.validate.min.js'></script>";
	$_CORE_INCLUDE[] = "<script src='/jquery/jquery-migrate-3.0.0.min.js'></script>";
}
$_CORE_INCLUDE[] = "<script type='text/javascript' src='/url/core-includes/bootstrap.func.js'></script>";
$_CORE_INCLUDE[] = "<script type='text/javascript' src='/url/core-includes/device_cap.js'></script>";
$_CORE_INCLUDE[] = "<link rel='stylesheet' href='/url/core-includes/main.css'>";

// autoload core classes
spl_autoload_register(function ($class_name) {
    require_once "../core/includes/" . $class_name . '.class.php';
});

// other required includes
require_once("../3rdparty/jwtHelper/jwt_helper.php");
require_once("../core/includes/log.func.php");

// Base url used to build links
$_BASE_URL = _PROTOCOL . '://' . _DOMAIN . '/';

// pregen variables
define("_PREGEN_FILE", "../site/pregen.json");
$strPregen = file_get_contents(_PREGEN_FILE);
$arrPregen = json_decode($strPregen, true);
define("_PREGEN_CORE", $arrPregen['core']);
define("_PREGEN_CUSTOM", $arrPregen['custom']);

// non-constant globals
$_TIME_STAMP = time();

// persistent credential maintenance
$_persistant_duration = (_PERSISTENT_LIFE * 60);

if(_PERSISTENT_METHOD == 1){
    $bolTimeoutException = false;
	if(isset($_COOKIE['token'])) {
		$bolTimeoutException = confirmAccess(_TIME_OUT_EXCEPTIONS);		
	}	
	if(isset($_COOKIE["token_cookie"]) && !$bolTimeoutException){
		setcookie("token_cookie", $_COOKIE["token_cookie"], $_persistant_duration, "/", _DOMAIN);
	}
}
if(_PERSISTENT_METHOD == 2){ 
	session_start();
	$bolTimeoutException = false;
	if(isset($_SESSION['token'])) {		
		$bolTimeoutException = confirmAccess(_TIME_OUT_EXCEPTIONS);		
	}	
	// Check if the timeout field exists.
	if(isset($_SESSION['timeout']) && !$bolTimeoutException) {
		// See if the number of seconds since the last
		// visit is larger than the timeout period.
		$duration = time() - (int)$_SESSION['timeout'];
		if($duration > $_persistant_duration) {
			// Destroy the session and restart it.
			killPersistent();
			setMessage("Session timeout");
			$new_page = "http://"._DOMAIN;
			//Redirect browser
			header("Location: $new_page"); 
			exit();
		}
	}
// Update the timout field with the current time.
$_SESSION['timeout'] = time();
}

// initialize content blocks
$rend_array = array();
$rend_array['head'] = "";
$rend_array['core_include'] = $_CORE_INCLUDE;
$rend_array['feature_include'] = array();
$rend_array['feature_include_low'] = array();
$rend_array['banner'] = "";
$rend_array['menu'] = array();
$rend_array['breadcrumbs'] = array();
$rend_array['page_name'] = "";
$rend_array['content'] = "";
$rend_array['footer'] = "";

// Persistent variable functions

function setMessage($val_persistent){ 
	if(_PERSISTENT_METHOD == 1){ 
		setcookie("message", $val_persistent, 0, "/", _DOMAIN);
	}		
	if(_PERSISTENT_METHOD == 2){
		$_SESSION['message'] = $val_persistent;
	}
}
function displayMessage(){
	$_message = "";
	if(_PERSISTENT_METHOD == 1){
		if(isset($_COOKIE["message"])){
			if(strlen($_COOKIE["message"]) > 0){
				$_message = "<DIV class='message'>".$_COOKIE["message"]."</DIV>";
				setcookie("message", "", 0, "/", _DOMAIN);		
			}			
		}	
	}	
	if(_PERSISTENT_METHOD == 2){
		if(isset($_SESSION['message'])){
			if(strlen($_SESSION['message']) > 0){
				$_message = "<DIV class='message'>".$_SESSION['message']."</DIV>";	
				$_SESSION['message'] = "";
			}
		}	
	}
	return $_message;
}

function setPersistent($name_persistent, $val_persistent){
	if(_PERSISTENT_METHOD == 1){
		$cookie_life = time() + (60 * _PERSISTENT_LIFE);
		$val_encode =  jwt::encode($val_persistent, _TOKEN_KEY);
		setcookie($name_persistent, $val_encode, $cookie_life, "/", _DOMAIN);
	}		
	if(_PERSISTENT_METHOD == 2){
		$_SESSION[$name_persistent] = $val_persistent;
	}
} 

function getPersistent($name_persistent){
	if(_PERSISTENT_METHOD == 1){ 
		if(isset($_COOKIE[$name_persistent])){
			return jwt::decode($_COOKIE[$name_persistent], _TOKEN_KEY); 			
		} else {
			return false;
		}
	}		
	if(_PERSISTENT_METHOD == 2){
		if(isset($_SESSION[$name_persistent])){
			return $_SESSION[$name_persistent]; 
		} else {
			return false;		
		}
	}	
}

function killPersistent(){
	if(_PERSISTENT_METHOD == 1){ 
		setcookie("token_cookie", "", time()-3600, "/", _DOMAIN);
		setcookie("token_cookie", "", 1, _DOMAIN);
		setcookie ("token_cookie", false);
		unset($_COOKIE["token_cookie"]);	
	}		
	if(_PERSISTENT_METHOD == 2){
		session_unset();
		session_destroy();
	}	
}

// build menu array
function getMenu(){
  // create an array of accessible menu items	
  $o_database = new db(); 
  //Set the query
  $o_database->query('SELECT dname, maddr, mroles FROM menu WHERE active = "1" ORDER BY weight, dname');
  //Run the query and save it to the $rows array
  $mrows = $o_database->resultset();
  //Release the object
  unset($o_database);
  $tokendecode =  getPersistent("token");
  if(_PERSISTENT_METHOD == 1){
	$laccess = $tokendecode->laccess;
  }
  if(_PERSISTENT_METHOD == 2){
	$laccess = '';
	if(is_array($tokendecode)){
		$laccess = $tokendecode['laccess'];
	}		
  }  
  $menu = array();
  $bolTerminal = false;
  if(confirmAccess(array('terminal'))){
	$bolTerminal = true;
  }  
  if(isset($laccess)){ 
	  foreach($mrows as $mrow){
		if($bolTerminal && $mrow['dname'] == 'My Profile'){
			continue;
		}		  
		$mroles = explode(",",$mrow['mroles']);
		foreach($mroles as $role){	
			if(is_array($laccess) && in_array($role, $laccess)){		
				array_push($menu, array($mrow['dname'], $mrow['maddr']));
				break;
			}
		}
	  }
	  if(confirmAccess(array('terminal'))){
	
	  }
  }
  return $menu;
}

// returns ID of current logged on user
function getLUID(){
  $tokendecode =  getPersistent("token");
  if(_PERSISTENT_METHOD == 1){
	$luid = $tokendecode->luid;
  }
  if(_PERSISTENT_METHOD == 2){
	$luid = $tokendecode['luid'];
  }
  return $luid;  
}

// returns integration ID of current logged in user
function getIntID(){
	$o_database = new db(); 
    $o_database->query('SELECT intid FROM users WHERE uid = :uid');
    $o_database->bind(':uid', getLUID());
    $row = $o_database->single();
    unset($o_database);
	return $row['intid'];
}

// given required access, returns whether or not user should see page content
function confirmAccess($req_roles){
  $tokendecode =  getPersistent("token");
  $o_database = new db();
  $o_database->query('SELECT rid FROM roles WHERE rname = :rname');
  $laccess = array();
  if($tokendecode){
	  if(_PERSISTENT_METHOD == 1){
		$laccess = $tokendecode->laccess;
	  }
	  if(_PERSISTENT_METHOD == 2){
		  $laccess = $tokendecode['laccess'];  
	  }
  }	  
  $allow = false;
  foreach($req_roles as $role){
	// if not an int, look up the rid and convert
	if(!is_numeric($role)){
		$o_database->bind(':rname', $role);
		$row = $o_database->single();
		$role =  $row['rid'];
	}
	// compare required role with cached roles
	if(!is_array($laccess)){$laccess = array();}
	if(in_array($role, $laccess)){
			$allow = true;
			break;
	}
  }
  unset($o_database);
  return $allow;   
  //return true; 
}

// convert location strings into directory structure
function convertDirs($dir_str){
	$dir_conv = "../";
	if($wk_str = explode("-",$dir_str)){
		foreach ($wk_str as $dir){
		  $dir_conv = $dir_conv . $dir . "/"; 
		}	  
	}
	return $dir_conv;
}

// renders output of PHP include to string
function renderPhpToString($file, $vars=null)
{
    if (is_array($vars) && !empty($vars)) {
       extract($vars);
    }
    ob_start();
    include $file;
    return ob_get_clean();
}

// replaces key fields from an array in long string
function replaceKeyStrings($str, $arr=null){
	foreach ($arr as $key=>$val){
		$str = str_replace("<!".$key."!>",$val ?? '',$str);
	}
	return $str;
}

// (Somewhat) safe &_POST function.
//  Use only outside HTML tags
function post($name=NULL, $value=false)
{
    if (isset($_POST[$name])){
		$content=(!empty($_POST[$name]) || $_POST[$name] == 0 ? trim($_POST[$name]) : (!empty($value) && !is_array($value) ? trim($value) : false));
		$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
		$content = htmlentities($content, ENT_QUOTES, 'UTF-8');
		if(strlen($content)>0){
			return $content;
		} else {
			return false;
		}
	}else{
		return false;
	}
}

// Smart (safe) $_GET function
function get($name=NULL, $value=false)
{
	 if (isset($_GET[$name])){
		$content=(!empty($_GET[$name]) ? trim($_GET[$name]) : (!empty($value) && !is_array($value) ? trim($value) : false));
		if(is_numeric($content))
			return preg_replace("@([^0-9])@Ui", "", $content);
		else if(is_bool($content))
			return ($content?true:false);
		else if(is_float($content))
			return preg_replace("@([^0-9\,\.\+\-])@Ui", "", $content);
		else if(is_string($content))
		{
			if(filter_var ($content, FILTER_VALIDATE_URL))
				return $content;
			else if(filter_var ($content, FILTER_VALIDATE_EMAIL))
				return $content;
			else if(filter_var ($content, FILTER_VALIDATE_IP))
				return $content;
			else if(filter_var ($content, FILTER_VALIDATE_FLOAT))
				return $content;
			else
				return preg_replace("@([^a-zA-Z0-9\+\-\_\*\@\$\!\;\.\?\#\:\=\%\/\ ]+)@Ui", "", $content);
		}else{
		   return false; 	
		}
	 }else{
		return false; 
	 }
}

// Stringify all passed vars to prevent injection
function sqlStringify($strIn){
         return "'".str_replace("'","''",$strIn)."'";
}

function cmsfEncode($string){	
	$arrDecode = array(
		'\\' => '_e1',
		'&' => '_e2',
		'/' => '_e3',
		'"' => '_e4',
		"'" => '_e5',
		'#' => '_e6',
		',' => '_e7',
		'(' => '_e8',
		')' => '_e9'
	);
	$rtnstring = strtr($string,$arrDecode);	
	
	return $rtnstring; 
}

function cmsfDecode($string){
	$arrDecode = array(
		'_e1' => '\\',
		'_e2' => '&',
		'_e3' => '/',
		'_e4' => '"',
		'_e5' => "'",
		'_e6' => '#',
		'_e7' => ',',
		'_e8' => '(',
		'_e9' => ')',
		'_e10' => " "
	);
	$rtnstring = strtr($string,$arrDecode);
	return $rtnstring; 
}

function fmtCurrency($n_money){
	return '$' . number_format($n_money, 2);
}

// Custom PDO Exception reporting	
class CustomException extends PDOException {
    public function __construct($message=null, $code=null) {
        $this->message = $message;
        $this->code = $code;
    }  
} 
?>