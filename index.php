<?PHP
/* Primary gateway into project.  All subsequent functionality called through his page. */
// Error display and logging.  Should be commented out on a live site.
error_reporting(-1); // reports all errors
ini_set("display_errors", "1"); // 1 shows all errors
ini_set("log_errors", 1);
ini_set("error_log", "/site/logs/php-error.log");

require_once("../core/includes/bootstrap.func.php");

// access override
// Use only if the admin is locked out of the site.
/* $token = array();
	$token['luid'] = "1";
	$token['laccess'] = array('1', '2', '3');
	setPersistent("token", $token); */
// delete session/cookies
//killPersistent();

$feature = get("p1");
if(strpos(strtolower($feature), ".php") > 0){
	$feature = rtrim(strtolower($feature), ".php");
}
if(!$feature){$feature = "home";}

// reset token if coming from link
if($feature == "link"){
	if(_PERSISTENT_METHOD == 1){ 	
		unset($_COOKIE["token_cookie"]);	
	}		
	if(_PERSISTENT_METHOD == 2){		
		session_unset();
	}	 
}
if(file_exists("../core/$feature.php")){
	$repo = "core";
} 
elseif(file_exists("../custom/$feature")) {
	$repo="custom";	
}
else {
	$repo = false;
}

if(!getPersistent("token") && $feature != 'url'){  // not logged in yet
  require_once('../core/access.php');
} elseif(!getPersistent("token") && $feature == 'url') { 
  if(file_exists("../core/url.php")){
		  include("../core/url.php"); 			  
  } 
} else {
  // find target page
  if($feature){
	  if($repo == "core"){
	    if(file_exists("../core/$feature.php")){
		  include("../core/$feature.php"); 		  
	    }
	  }	elseif ($repo == "custom") {
		  $subfeature = get("p2");
		  if(file_exists("../custom/$feature/$subfeature.php")){
			include("../custom/$feature/$subfeature.php"); 		  
		  }elseif(file_exists("../custom/$feature/$feature.php")){
		    include("../custom/$feature/$feature.php"); 
		  }else{
			echo "Invalid page request";    
		  }	
	    }
  }else{
	  echo "Invalid page request"; 
  }	
}
?>
