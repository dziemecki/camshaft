<?php
/*   
  User account management
*/
// get sub-feature
$subfeature = get("p2");
$req_access = array(1);
// Redirect unless a) user is admin, b) user is editing own prfile, or c) user is updating own profule
if(!confirmAccess($req_access) && $subfeature != "myprofile" && !($subfeature == "updatemyprofile" && get("p3") == getLUID())){
	setMessage("Invalid access attempt.");
	$new_page = "http://"._DOMAIN;
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}

function lookUpUID($strUName){
	$o_database = new db(); 
	//Set the query
	$o_database->query('SELECT uid FROM users WHERE uname = :uname');
	//Bind the data
	$o_database->bind(':uname', $strUName);
	//Run the query
	$row = $o_database->single();
	$UID = $row['uid'];
	//Release the object
	unset($o_database);	
	return $UID; 
} 

// initialize global variables
$users_includes = array();

$users_form_action = "";
$users_form = "<DIV class='body_form'>\r\n";
$users_form .= "<form class='cmxform' id='<!users_form_id!>' method='post' action='<!users_form_action!>'>\r\n";
$users_form .= "<fieldset>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='fname'>Firstname *</label>\r\n";
$users_form .= "<input id='fname' name='fname' type='text' value='<!fname!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='lname'>Lastname *</label>\r\n";
$users_form .= "<input id='lname' name='lname' type='text' value='<!lname!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='lname'>Middle Name</label>\r\n";
$users_form .= "<input id='mname' name='mname' type='text' value='<!mname!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='pname'>Preferred Name<br /><span class='form-description'>Name user goes by, if not first</span></label>\r\n";
$users_form .= "<input id='pname' name='pname' type='text' value='<!pname!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='uname'>Username *<br /><span class='form-description'>The user's login ID</span></label>\r\n";
$users_form .= "<input id='uname' name='uname' type='text' value='<!uname!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='upass1'>Password<!preq!></label>\r\n";
$users_form .= "<input id='upass1' name='upass1' type='password'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='upass2'>Confirm password<!preq!></label>\r\n";
$users_form .= "<input id='upass2' name='upass2' type='password'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='uphone'>Phone #</label>\r\n";
$users_form .= "<input id='uphone' name='uphone' type='text' value='<!uphone!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='umail'>Email *</label>\r\n";
$users_form .= "<input id='umail' name='umail' type='email' value='<!umail!>'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p class='form-group'>\r\n";
$users_form .= "<label for='intid'>Integration ID</label>\r\n";
$users_form .= "<input id='intid' name='intid' type='text' value='<!intid!>' <!intid-disable!>>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "<p>\r\n";
$users_form .= "<input class='submit' type='submit' value='Submit'>\r\n";
$users_form .= "</p>\r\n";
$users_form .= "</fieldset>\r\n";
$users_form .= "</form>\r\n";
$users_form .= "</DIV>\r\n";
$users_form_keys = array();
if($subfeature == "edit" || $subfeature == "myprofile"){
    if($subfeature == "myprofile"){$uid = getLUID();}	
	if($subfeature == "edit"){$uid = get("p3");}	
	$req_access = array(1);
	if($uid == getLUID()){
		$myprofile = true;
	}else{
		$myprofile = false;
	}
	if(confirmAccess($req_access) || $myprofile){
		$o_database = new db(); 
		//Set the query
		$o_database->query('SELECT uid, uname, uphone, umail, lname, fname, mname, pname, active, intid FROM users WHERE uid = :uid');
		//Bind the data
		$o_database->bind(':uid', $uid);
		//Run the query
		$row = $o_database->single();
		//Release the object
		unset($o_database);
		if(get("p4")=="a" && $uid != getLUID()){ //active and someone else
			$users_form .= "<DIV class='body_form'><FORM action='/users/deactivate/<!uid!>' method='post' id='deactivateUser'><input<!disabled!> type='button' class='user_form' value='Deactivate' onclick=displayElement('"."ui-dialog"."','"."deactivateUser";
			$users_form .= "');><INPUT type='hidden' name='uid' value='<!uid!>'><INPUT type='hidden' name='uname' value='<!uname!>'></FORM>";
			$users_form .= "<div id='dialog-confirm' title='Deactivate user?'>"; 
			$users_form .= "<p class='alert'><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>This user will be deactivated and will lose access immediately. Are you sure?</p>";	
			$users_form .= "</div></DIV>";
		}
		if(get("p4")=="i"){ //inactive
			$users_form .= "<DIV class='body_form'><FORM action='/users/activate/<!uid!>' method='post' id='activateUser'><input type='button' class='user_form' value='Activate' onclick=displayElement('"."ui-dialog"."','"."activateUser";
			$users_form .= "');><INPUT type='hidden' name='uid' value='<!uid!>'><INPUT type='hidden' name='uname' value='<!uname!>'></FORM>";
			$users_form .= "<div id='dialog-confirm' title='Activate user?'>"; 
			$users_form .= "<p class='alert'><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>This user will be activated and will gain access immediately. Are you sure?</p>";	
			$users_form .= "</div></DIV>";
		}
		// clean up blank values
		foreach ($row as $key => $value){
			if(is_numeric($value) && $value==0){
				$row[$key] = '';
			}
		}
        if ($myprofile){$users_form_keys['users_form_action'] = "/users/updatemyprofile/".$uid;}else{$users_form_keys['users_form_action'] = "/users/update/".$uid;}				
		$users_form_keys['users_form_id'] = "editUser";	
		$users_form_keys['uid'] = $row['uid'];
		$users_form_keys['uname'] = $row['uname'];
		$users_form_keys['uphone'] = $row['uphone'];
		$users_form_keys['umail'] = $row['umail'];
		$users_form_keys['lname'] = $row['lname'];
		$users_form_keys['fname'] = $row['fname'];
		$users_form_keys['mname'] = $row['mname'];
		$users_form_keys['pname'] = $row['pname'];
		$users_form_keys['intid'] = $row['intid'];
		$users_form_keys['preq'] = "";
		if(confirmAccess(array('administrator'))){$users_form_keys['intid-disable'] = "";}else{$users_form_keys['intid-disable'] = " DISABLED";}
		if($uid == "1"){$users_form_keys['disabled'] = " disabled";}else{$users_form_keys['disabled'] = "";}
		$users_content = replaceKeyStrings($users_form, $users_form_keys);
		if($myprofile){$rend_array['page_name'] = "Edit Profile";}else{$rend_array['page_name'] = "Edit User";}		
		$users_includes[] = "<script type='text/javascript' src='/url/core-includes/users.js'></script>";	
		$users_includes[] = "<script type='text/javascript' src='/url/core-includes/users.validate.js'></script>";
		$users_includes[] = "<link rel='stylesheet' type='text/css' href='/url/core-includes/users.css'>"; 
		$users_breadcrumbs = array();	
		if($subfeature != "myprofile"){
			$users_breadcrumbs[] = array("Site Configuration", "site_config");
			$users_breadcrumbs[] = array("User Account Management", "users");
			$users_breadcrumbs[] = array("All Users", "users-list");
		}
	}else{
		setMessage("Invalid access attempt");
		$new_page = "http://"._DOMAIN;
		//Redirect browser
		header("Location: $new_page"); 
		exit();
	}		
}

if($subfeature == "deactivate" or $subfeature == "activate"){
	$o_database = new db();
	//Set the query
    $o_database->query('UPDATE users SET active = :active, change_date = :change_date, change_uid = :change_uid WHERE uid = :uid'); 
	//Bind the data
	if($subfeature == "deactivate"){
        $o_database->bind(':active', '0');				
	} else {
		$o_database->bind(':active', '1');
	}
	$o_database->bind(':change_date', $_TIME_STAMP);
	$o_database->bind(':change_uid', getPersistent('luid'));	
	$o_database->bind(':uid', post('uid'));		
	if($o_database->execute()){

		setMessage("User " . post('uname') . " ". $subfeature . "d");
		$new_page = "http://"._DOMAIN."/users/list";
		//Redirect browser
		header("Location: $new_page"); 
		exit();
	} else {
		setMessage("An error has occurred.");		
		$new_page = "http://"._DOMAIN."/users/edit/".post('uid');
		//Redirect browser
		header("Location: $new_page"); 
		exit();	
	}
}

if($subfeature == "new"){	
	$users_form_keys['users_form_action'] = "/users/insert";
	$users_form_keys['users_form_id'] = "createUser";
	$users_form_keys['uname'] = "";
	$users_form_keys['umail'] = "";
	$users_form_keys['uphone'] = "";
	$users_form_keys['lname'] = "";
	$users_form_keys['fname'] = "";
	$users_form_keys['mname'] = "";
	$users_form_keys['pname'] = "";
	$users_form_keys['intid'] = "";
	$users_form_keys['intid-disable'] = "";
	$users_form_keys['preq'] = " *";
	$users_content = replaceKeyStrings($users_form, $users_form_keys);
	$rend_array['page_name'] = "Create user";
    $users_includes[] = "<script type='text/javascript' src='/url/core-includes/users.js'></script>";	
	$users_includes[] = "<script type='text/javascript' src='/url/core-includes/users.validate.js'></script>";	
    $users_includes[] = "<link rel='stylesheet' type='text/css' href='/url/core-includes/users.css'>"; 
	$users_breadcrumbs = array();
    $users_breadcrumbs[] = array("Site Configuration", "site_config");
    $users_breadcrumbs[] = array("User Account Management", "users");
}

if($subfeature == "update" || $subfeature== "updatemyprofile"){
	$o_database = new db();
	//Set the query
	if (strlen(post('upass1')) > 0 && post('upass1') == post('upass2')){
		$o_database->query('UPDATE users SET uname = :uname, upass = :upass, uphone = :uphone, umail = :umail, active = :active, lname = :lname, fname = :fname, mname = :mname, pname = :pname, create_date = :create_date, change_date = :change_date, change_uid = :change_uid, intid = :intid WHERE uid = :uid');
	} else {
		$o_database->query('UPDATE users SET uname = :uname, uphone = :uphone, umail = :umail, active = :active, lname = :lname, fname = :fname, mname = :mname, pname = :pname, create_date = :create_date, change_date = :change_date, change_uid = :change_uid, intid = :intid WHERE uid = :uid');
	}	
	//Bind the data
	$o_database->bind(':uid', get('p3'));
	$o_database->bind(':uname', post('uname'));
	if (strlen(post('upass1')) > 0 && post('upass1') == post('upass2')){
		$o_database->bind(':upass', password_hash(post('upass1'), PASSWORD_BCRYPT));
	}	
    $o_database->bind(':uphone', post('uphone'));	
	$o_database->bind(':umail', post('umail'));
	$o_database->bind(':active', '1');
	$o_database->bind(':lname', post('lname'));
	$o_database->bind(':fname', post('fname'));
	$o_database->bind(':mname', post('mname'));
	$o_database->bind(':pname', post('pname'));
	$o_database->bind(':create_date', $_TIME_STAMP);
	$o_database->bind(':change_date', $_TIME_STAMP);
	$o_database->bind(':change_uid', getPersistent('luid'));
	$o_database->bind(':intid', post('intid'));
	//Execute the query
	if($o_database->execute()){
		setMessage("User Updated");
		if($subfeature == "updatemyprofile"){$new_page = "http://"._DOMAIN."/home/";}else{$new_page = "http://"._DOMAIN."/users/list/";}		
	} else {
		setMessage("ERROR:  The User Was NOT Updated");
		$new_page = "http://"._DOMAIN."/users/edit/";
	}
	//Redirect browser
	header("Location: $new_page"); 
	exit();	
}

if($subfeature == "insert"){	
    $o_database = new db();
	//Set the query
    $o_database->query('INSERT INTO users (uname, upass, uphone, umail, active, lname, fname, mname, pname, create_date, change_date, change_uid, intid) VALUES (:uname, :upass, :uphone, :umail, :active, :lname, :fname, :mname, :pname, :create_date, :change_date, :change_uid, :intid)');
	//Bind the data
	$o_database->bind(':uname', post('uname'));
	$o_database->bind(':upass', password_hash(post('upass1'), PASSWORD_BCRYPT));
	$strUPhone = post('uphone'); 
	if (strlen($strUPhone) < 1){$strUPhone = "";}
	$o_database->bind(':uphone',$strUPhone);
	$o_database->bind(':umail', post('umail'));
	$o_database->bind(':active', '1');
	$o_database->bind(':lname', post('lname'));
	$o_database->bind(':fname', post('fname'));
	$strMName = post('mname'); 
	if (strlen($strMName) < 1){$strMName = "";}	
	$o_database->bind(':mname', $strMName);	
	$strPName = post('pname'); 
	if (strlen($strPName) < 1){$strPName = "";}	
	$o_database->bind(':pname', $strPName);
	$o_database->bind(':create_date', $_TIME_STAMP);
	$o_database->bind(':change_date', $_TIME_STAMP);
	$o_database->bind(':change_uid', getPersistent('luid'));
	$o_database->bind(':intid', post('intid'));
	//Execute the query and add default user role
	if($o_database->execute()){
		$o_database2 = new db();
		$intUID = lookUpUID(post('uname'));
		//echo $intUID;
		$o_database2->query('INSERT INTO userroles (uid, rid, create_date, change_uid) VALUES (:uid, "3", :create_date, :change_uid)');
		$o_database2->bind(':uid', $intUID);
		$o_database2->bind(':create_date', $_TIME_STAMP);
		$o_database2->bind(':change_uid', getPersistent('luid'));
		$o_database2->bind(':uid', $intUID);
//echo 'INSERT INTO userroles (uid, rid, create_date, change_uid) VALUES (:uid, "3", :create_date, :change_uid';		
		if($o_database2->execute()){
			setMessage("User Created");
			$new_page = "http://"._DOMAIN."/users/list/";			
		}else{
			setMessage("ERROR:  The User Was NOT Created");
			$new_page = "http://"._DOMAIN."/users/new/";
		}
		
	} else {
		setMessage("ERROR:  The User Was NOT Created");
		$new_page = "http://"._DOMAIN."/users/new/";
	}	
	unset($o_database);
	unset($o_database2);
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}

if($subfeature == "list"){
  //display list of active users, inactive users
  // generate user lists
  // logout action, then login form	
  $o_database = new db(); 
  //To select a single row:
  //Set the query
  $o_database->query('SELECT uid, lname, fname, pname, active FROM users ORDER BY lname, fname');
  //Run the query and save it to the $rows array
  $rows = $o_database->resultset();
  //Release the object
  unset($o_database);
  $users_active_users = "";
  $users_inactive_users = "";	  
  foreach ($rows as $user){
	  $users_row = "";
	  if(empty($user['pname'])){  
		 $dname = $user['fname'];
	  } else {
		 $dname = $user['pname'];	
	  }	  
	  $users_row .= "<TR><TD class='users'><A href='/users/edit/".$user['uid']."/<!users_status!>'>".$user['lname'].", ".$dname."</A></TD></TR>";
	  if ($user['active'] == "1"){
		$users_active_users  .= str_replace("<!users_status!>","a",$users_row);
	  }	  
	  if ($user['active'] == "0"){
		 $users_inactive_users  .= str_replace("<!users_status!>","i",$users_row); 
	  }
  }

  //build tabs
  //  First tabs list last, all other in order
  $users_tabs = "<DIV id='tabs'>";
  $users_tabs .= "  <ul>";
  $users_tabs .= "    <li><a href='#tabs-1'>Active Users</a></li>";
  $users_tabs .= "    <li><a href='#tabs-2'>InactiveUsers</a></li>";
  $users_tabs .= "  </ul>";
  $users_tabs .= "  <div id='tabs-1'>";
  $users_tabs .= "    <DIV class='tab-content'><TABLE>".$users_active_users."</TABLE></DIV>";
  $users_tabs .= "  </div>";
  $users_tabs .= "  <div id='tabs-2'>";
  $users_tabs .= "    <DIV class='tab-content'><TABLE>".$users_inactive_users."</TABLE></DIV>";
  $users_tabs .= "  </div>";
  $users_tabs .= "</DIV>";
 
  $users_content = $users_tabs;
  $rend_array['page_name'] = "All Users";
  $users_includes[] = "<script type='text/javascript' src='/url/core-includes/users.js'></script>";	
  $users_includes[] = "<link rel='stylesheet' type='text/css' href='/url/core-includes/users.css'>"; 
  $users_breadcrumbs = array();
  $users_breadcrumbs[] = array("Site Configuration", "site_config");
  $users_breadcrumbs[] = array("User Account Management", "users");
}

if(!$subfeature){
// build users admin menu
$users_menu = array();
array_push($users_menu, array('New User', 'users-new'));
array_push($users_menu, array('Edit Users', 'users-list'));

$users_content = "<table class='content'>";
foreach ($users_menu as $item){
	$users_content = $users_content . "<tr><td class='content'><a href='".convertDirs($item[1])."'>".$item[0]."</a></td></tr>";
}
$rend_array['page_name'] = "User account management";
$users_content = $users_content . "</table>";
$users_breadcrumbs = array();
$users_breadcrumbs[] = array("Site Configuration", "site_config");
}

// set replacable block content
$rend_array['feature_include'] = $users_includes;
$rend_array['menu'] = getMenu();
$rend_array['content'] = $users_content;
$rend_array['breadcrumbs'] = $users_breadcrumbs;

// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);

// output page content
echo $out;

?>