
<?PHP
/* Login and security functionality */

$feature = get("p1");
if(!$feature){$feature = "access";}
$subfeature = get("p2");
// initialize globals
$content = "";
// valid local subfeatures
$access_subs = array("create", "submit", "logout", "forgot", "reset");
$access_includes = array();

// generate token via function, rather than sign in
function buildToken($luid){
	$o_database = new db(); 
	//Set the query for access info
    $o_database->query('SELECT rid FROM userroles WHERE uid = :uid');
    //Bind the data
    $o_database->bind(':uid', $luid);
    //Run the query and save it to the $rows array
    $access_rows = $o_database->resultset();
    //Release the object
    unset($o_database);
    // build access array
    $laccess = array();
    foreach ($access_rows as $access){
	  array_push($laccess, $access['rid']);
	}
    $token = array();
	$token['luid'] = $luid;
	$token['laccess'] = $laccess;
	setPersistent("token", $token);	
}

if($feature == "link"){
  $o_database = new db(); 
  //Set the query for user info
  $o_database->query('SELECT uid, uname, pname, upass FROM users WHERE uname = :uname');
  //Bind the data
  $o_database->bind(':uname', get("p2"));
  //Run the query
  $user_row = $o_database->single();
  //Set the query for access info
  $o_database->query('SELECT rid FROM userroles WHERE uid = :uid');
  //Bind the data
  $o_database->bind(':uid', $user_row['uid']);
  //Run the query and save it to the $rows array
  $access_rows = $o_database->resultset();
  //Release the object
  unset($o_database);
   
  // build access array
  $laccess = array();
  foreach ($access_rows as $access){
	array_push($laccess, $access['rid']);
  } 
  
  if(password_verify(get("p3"), $user_row['upass'])){
	// successful login.  Redirect to default page.
	$token = array();
	$token['luid'] = $user_row['uid'];
	$token['laccess'] = $laccess;
	setPersistent("token", $token);
	$req_access = array('terminal');
	if(confirmAccess($req_access)){
    	//Redirect browser
		$sTarget_Vars = get("p4");	
		$sNewVars = "";
		if(strlen($sTarget_Vars) > 0){
			// Uses "_." as the delimeter to avoid conflict with other possible subfeature delimiters
			$sNewVars = str_replace("_.","/",$sTarget_Vars);		
		}	
		if(strlen($sNewVars) > 0){
			$new_page = "http://"._DOMAIN."/".$sNewVars;
		}else{
			$new_page = "http://"._DOMAIN."/home";
		}		
	    header("Location: $new_page"); 
	    exit();
	}else{
		killPersistent();
		setMessage("Invalid credentials");
	    $new_page = "http://"._DOMAIN."/access";
		header("Location: $new_page"); /* Redirect browser */
		exit();  		
	}	
  }else{
	// unsuccessful login.  Redirect to login page with an error message
	setMessage("Invalid credentials");
	$new_page = "http://"._DOMAIN."/access";
	//Redirect browser
	header("Location: $new_page"); 
	exit();
  }
}

if($subfeature == "create"){
// create account, then login form
// note that this functionality can be blocked by config
echo "This feature not yet available.  Please contact your site administrator." 	;
}

if($subfeature == "submit"){
  // logout action, then login form	
  $o_database = new db(); 
  //Set the query for user info
  $o_database->query('SELECT uid, uname, pname, upass FROM users WHERE uname = :uname');
  //Bind the data
  $o_database->bind(':uname', post("uname"));
  //Run the query
  $user_row = $o_database->single();
  //Set the query for access info
  $o_database->query('SELECT rid FROM userroles WHERE uid = :uid');
  //Bind the data
  $o_database->bind(':uid', $user_row['uid']);
  //Run the query and save it to the $rows array
  $access_rows = $o_database->resultset();
  //Release the object
  unset($o_database);
   
  // build access array
  $laccess = array();
  foreach ($access_rows as $access){
	array_push($laccess, $access['rid']);
  } 
  if(password_verify(post("pwd"), $user_row['upass'])){
	// successful login.  Redirect to default page.
	$token = array();
	$token['luid'] = $user_row['uid'];
	$token['laccess'] = $laccess;
	setPersistent("token", $token);	
	
	$target_url = getPersistent('target_url');
	if(strlen($target_url) > 0){
		$new_page = $target_url;
		setPersistent('target_url','');
	}else{
		$new_page = "http://"._DOMAIN;
	}

	
	
	//Redirect browser
	header("Location: $new_page"); 
	exit();
  }else{
	// unsuccessful login.  Redirect to login page with an error message
	setMessage("Invalid credentials");
	$new_page = "http://"._DOMAIN."/access";
	//Redirect browser
	header("Location: $new_page"); 
	exit();
  }
}

if($subfeature == "logout"){
// logout action, then login form
  killPersistent();
  $new_page = "http://"._DOMAIN."/access";
  header("Location: $new_page"); /* Redirect browser */
  exit();  
}
  
// submit request for link to reset password 
if($subfeature == "forgot"){
	// reset password request
	$reset_form = "<DIV class='instructions'>Upon submit, a link to a page where you can reset your password will sent to the account email account on record.</DIV>";	
	$reset_form .= "<DIV class='body_form'>";
	$reset_form .= "<FORM action='http://"._DOMAIN."/access/request/uname' method='post'>";
	$reset_form .= "<DIV class='text-center'>User ID:</DIV>";
	$reset_form .= "<DIV class='text-center'><input type='text' name='uname'></DIV>";
	$reset_form .= "<DIV class='text-center'><input class='submit' type='submit' value='Submit'></DIV>";	
	$reset_form .= "</FORM>";	
	$reset_form .= "<P class='text-center'>or ...</P>";
	$reset_form .= "<FORM action='http://"._DOMAIN."/access/request/ umail' method='post'>";	
	$reset_form .= "<DIV class='text-center'>Email Address:</DIV>";
	$reset_form .= "<DIV class='text-center'><input class='.element-center' type='text' name='umail'></DIV>";	
	$reset_form .= "<DIV class='text-center'><input class='submit' type='submit' value='Submit'></DIV>";
	$reset_form .= "</FORM>";	
	$reset_form .= "</DIV>";	
	$content = $reset_form;
	$rend_array['page_name'] = "Reset password";
	$rend_array['body_title'] = "Reset password";   
}

// send user a reset link 
if($subfeature == "request"){
	// Confirm valid request
	$search_field = get("p3");
    $o_database = new db(); 
    //Set the query for user info
	if($search_field == 'uname'){
		$o_database->query('SELECT uid, uname, umail FROM users WHERE uname = :uname');	
		//Bind the data
		$o_database->bind(':uname', post("uname"));	
	}
	if($search_field == 'umail'){
		$o_database->query('SELECT uid, uname, umail FROM users WHERE umail = :umail');	
		//Bind the data
		$o_database->bind(':umail', post("umail"));			
	}
	if($user_row = $o_database->single()){
		// valid entry
		// set the link hash
		$reset_hash = md5($user_row['uname'] . date("ymdhis",$_TIME_STAMP));
		// insert the hash key
        $o_db = new db();  		
		$o_db->query('UPDATE users SET reset_key = :reset_key WHERE uid = :uid');
		$o_db->bind(':reset_key', date("his",$_TIME_STAMP));
		$o_db->bind(':uid', $user_row['uid']);
		$o_db->execute();
	
		// build email
		$to      = $user_row['umail'];
		$subject = 'Reset for '. $user_row['uname'];
		$message = "Please use the following link to reset your password: \r\n\r\n ";
		$message .= $_BASE_URL .'access/reset/' . $to . '/'. $reset_hash . "\r\n\r\n";
		$message .= "This link is only good for today.";	
		$headers = 'From: ' . _ADMIN_EMAIL_FROM . "\r\n" .
		'Reply-To: ' . _ADMIN_EMAIL_REPLY . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		mail($to, $subject, $message, $headers);				
		
		// Set response message
		setMessage("Request submitted.  Check your email in a moment.");
		$new_page = $_BASE_URL ."/access";
		//Redirect browser
		header("Location: $new_page"); 
		exit();
	} else {
		// Set response message
		setMessage("Search value not found.");
		$new_page = $_BASE_URL ."/access";
		//Redirect browser
		header("Location: $new_page"); 
		exit();
	}
}

// send user a profile page for reset
if($subfeature == "reset"){
	// Find user and validate hash
	$reset_email = get("p3");
	$reset_hash = get("p4");
    $o_database = new db(); 
    //Set the query for user info
	$o_database->query('SELECT uid, uname, active, reset_key FROM users WHERE umail = :umail');	
	//Bind the data
	$o_database->bind(':umail', $reset_email);
	if($user_row = $o_database->single()){
      // create the hash
      $curr_hash = md5($user_row['uname'] . date("ymd",$_TIME_STAMP).$user_row['reset_key']);	
	  if($curr_hash ==  $reset_hash){  
		if($user_row['active'] == 1){ // active account test
		    buildToken($user_row['uid']);
		    $new_page = $_BASE_URL ."users/myprofile/";	
			//$new_page = $_BASE_URL ."users/myprofile/" . $user_row['uid'] . "/a";	
		} else {
			setMessage("This account is no longer active.");
		    $new_page = $_BASE_URL ."access";		
		} 
	  }else{ // hash missmatch
		// Set response message
		setMessage("Invalid link. It may be expired.");
		$new_page = $_BASE_URL ."/access";		  
	  }		
	}else{ // account not found (probably)
		// Set response message
		setMessage("Invalid link.");
		$new_page = $_BASE_URL ."/access";
	}
	//Redirect browser
	header("Location: $new_page");
	exit();
}

if(!$subfeature){
	// login form
	$login_form = "<DIV class='body_form'>";
	$login_form .= "<FORM action='/access/submit' method='post'>";
	$login_form .= "<DIV class='centerblock'>User ID:</DIV>";
	$login_form .= "<input class='centerblock'  type='text' name='uname'>";
	$login_form .= "<DIV class='centerblock'>Password:</DIV>";
	$login_form .= "<input class='centerblock' type='password' name='pwd'>";
	$login_form .= "<p><input class='submit' type='submit' value='Submit'></p>";
	$login_form .= "<DIV class='centertext'>[<a href='/access/forgot'>Forgot your password?</a>]</DIV>";
	$login_form .= "</FORM>";	
	$login_form .= "</DIV>";

	$content = $login_form;
	$access_includes[] = "<link rel='stylesheet' type='text/css' href='/url/core-includes/access.css'>"; 
	$rend_array['page_name'] = "Please login";
	$rend_array['body_title'] = "Please login";
}

if($subfeature && !in_array($subfeature,$access_subs)){
	setMessage("Invalid credentials");
    $target_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
        === 'on' ? "https" : "http") . "://" .
        $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	setPersistent('target_url', $target_url);	
	if(isset($_COOKIE["token_cookie"])){
	  $rend_array['menu'] = getMenu();
	}
	$new_page = "http://"._DOMAIN;
	//Redirect browser
	header("Location: $new_page"); 
	exit();	
}

// set replacable block content
$rend_array['feature_include'] = $access_includes;
$rend_array['content'] = $content;

// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);

// output page content
echo $out;
?>