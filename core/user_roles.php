<?php
/*   
  user role management
*/
// get sub-feature
$subfeature = get("p2");

// validate access
$req_access = array(1);
if(!confirmAccess($req_access)){
	setMessage("Invalid access attempt.");
	$new_page = "http://"._DOMAIN;
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}

// initialize global variables
$menu_includes = array();

// main page

// maintain roles items
// This duplicates direct access to the roles table, which makes this a low priority feature
if($subfeature == "maint"){
	$rend_array['page_name'] = "User Role Maintenance";
	$user_roles_content = "coming soon";	
	$user_roles_breadcrumbs = array();
	$user_roles_breadcrumbs[] = array("Site Configuration", "site_config");		
	$user_roles_breadcrumbs[] = array("User Roles Management", "user_roles");
}

// set roles for a user
if($subfeature == "editroles"){
	$uid = get("p3");
	$form = "<DIV class=\"center\">\r\n";  
	$form .= "<form class='cmxform' id='frmAssign' name='frmAssign'>\r\n";	
	$o_database = new db(); 
	$o_database2 = new db(); 
	// get all roles
	$o_database->query("SELECT rid, rname FROM roles");
	$rsRoles = $o_database->resultset();
	// get current assigned roles
	$o_database2->query("SELECT userroles.rid, users.uname FROM userroles INNER JOIN users ON users.uid = userroles.uid WHERE userroles.uid = $uid");
	$rsUserRoles = $o_database2->resultset();
	$arrRoles = array();
	$strRoles = '';
	$uname = '';
	foreach ($rsUserRoles as $userRole){
			$arrRoles[] = $userRole['rid'];
			$strRoles .= $userRole['rid'].","; 
			$uname = $userRole['uname']; 
	}
	$strRoles = rtrim($strRoles,','); // remove last comma	
	unset($o_database2);	
	$options = "";
	$cntOptions = 0;
	foreach ($rsRoles as $role){
		$cntOptions++;
		$selected = "";
		if(in_array($role['rid'],$arrRoles)){$selected = " SELECTED = 'true'";}
		$options .= "<OPTION value='".$role['rid']."'$selected >".$role['rname']."</OPTION>\r\n";
	}
	//$cntOptions = 2;
	$form .= "<fieldset id='set_options'>\r\n";
	$form .= "<SELECT id='frmAssignOptions' name='frmAssignOptions' multiple=true' class=\"center\" size = ".$cntOptions.">";
	//$form .= "<SELECT id='frmAssignOptions' name='frmAssignOptions' multiple=true' class='center multiselect' size = ".$cntOptions.">";
    $form .= "\r\n".$options;	
	$form .= "</SELECT>\r\n";
	$form .= "</fieldset>\r\n";	
	$form .= "<fieldset id='set_buttons'>\r\n";
	$form .= "<p class='form-group center'>\r\n";
	$form .= "<input type='button' id='frmAssignbtnSave' name='btnSave' value='Save'>\r\n";
	$form .= "<input type='button' id='frmAssignbtnClear' name='btnClear' value='Clear'>\r\n";
	$form .= "<input type='button' id='frmAssignbtnAll' name='btnAll' value='All'>\r\n";
	$form .= "</p>\r\n";
	$form .= "</fieldset>\r\n";
	$form .= "<input type='hidden' id='frmAssignURoles' name='frmAssignURoles' value='".$strRoles."'>\r\n";	
	$form .= "<input type='hidden' id='frmAssignUID' name='frmAssignUID' value='".$uid."'>\r\n";
	$form .= "</form>\r\n";
	$form .= "</div>\r\n";
	//Release the object
	unset($o_database);		
	$rend_array['page_name'] = "Roles for \"$uname\"";
	$user_roles_content = $form;	
	$user_roles_breadcrumbs = array();
	$user_roles_breadcrumbs[] = array("Site Configuration", "site_config");		
	$user_roles_breadcrumbs[] = array("User Roles Management", "user_roles");
	$user_roles_breadcrumbs[] = array("User Roles Assignment", "user_roles-listusers");
}

// maintain user roles
if($subfeature == "setroles"){
    $uid = post('frmAssignUID'); 
	$arrRoles = explode(",",post('frmAssignURoles')); 
    $o_database = new db();
	// delete current roles
	$o_database->query('DELETE FROM userroles WHERE uid = :uid');
	$o_database->bind(':uid', $uid);
	$o_database->execute();
	// add new roles
	$bolError = false;
	//while (list($key, $role) = each($arrRoles)) {
	foreach ($arrRoles as $role) {
		$sqlInsert = "INSERT INTO userroles (uid, rid, create_date, change_uid) VALUES (:uid, :rid, :create_date, :change_uid)";
		$o_database->query($sqlInsert);
		$o_database->bind(':uid', $uid);
		$o_database->bind(':rid', $role);
		$o_database->bind(':create_date', $_TIME_STAMP);
		$o_database->bind(':change_uid', getPersistent('luid'));		
		if(!$o_database->execute()){
			$bolError = true;
		}			
	}		
	//Set the query
    $o_database->query('UPDATE menu SET mroles = :mroles WHERE mid = :mid');
	//Bind the data
	$o_database->bind(':mroles', post('frmAssignMRoles'));
	$o_database->bind(':mid', post('frmAssignMID'));
	//Execute the query and add default user role
	if(!$bolError){
		$strMessage = "Role update completed";
	}else{
		setMessage("ERROR:  Insert error.  Please review changes.");
	}	
	setMessage($strMessage);
	$new_page = "http://"._DOMAIN."/user_roles/editroles/".$uid;
	//Redirect browser
	header("Location: $new_page"); 
	exit();
	//return;
}

// assign user roles
if($subfeature == "listusers"){
	$table = "<TABLE class='center'>\r\n";
	$table .= "<TR class='header'><TD class='header'>Users</TD></TR>\r\n";
	$o_database = new db(); 
	//Set the query
	$qry = "SELECT users.uid, users.lname, users.fname, users.pname ";
	$qry .= "FROM users WHERE users.active = 1 ORDER BY users.lname, users.fname"; 
	$o_database->query($qry);	
	//Run the query
	$rows = $o_database->resultset();
	foreach ($rows as $row){
		$pname = $row['pname'];
		if(!(strlen($pname) > 0) || $pname == 0){$pname = $row['fname'];}
		$table .= "<TR><TD><a href='/user_roles/editroles/".$row['uid']."'>".$row['lname'].", ".$pname."</a></TD></TR>\r\n";
	}
	$table .= "</TABLE>\r\n";
	//Release the object
	unset($o_database);	
	$rend_array['page_name'] = "User Group Assignment";
	$user_roles_content = $table;	
	$user_roles_breadcrumbs = array();
	$user_roles_breadcrumbs[] = array("Site Configuration", "site_config");		
	$user_roles_breadcrumbs[] = array("Menu management", "menu");
}

// base menu 
if(!$subfeature){
	// build menu admin menu
	$user_roles_menu = array();
	//array_push($user_roles_menu, array('Maintain User Roles', 'user_roles-maint')); >>>>> NOT A PRIORITY FEATURE ADD
	array_push($user_roles_menu, array('Assign User Roles', 'user_roles-listusers'));
	$user_roles_content = "<table class='content'>";
	foreach ($user_roles_menu as $item){
		$user_roles_content .= "<tr><td class='content'><a href='".convertDirs($item[1])."'>".$item[0]."</a></td></tr>";
	}
	$rend_array['page_name'] = "User Roles Management";
	$user_roles_content .= "</table>";	
	$user_roles_breadcrumbs = array();
	$user_roles_breadcrumbs[] = array("Site Configuration", "site_config");	
}

// set replacable block content
$user_roles_includes[] = "<script type='text/javascript' src='/url/core-includes/user_roles.js'></script>";	
$user_roles_includes[] = "<link rel='stylesheet' type='text/css' href='/url/core-includes/user_roles.css'>"; 
$rend_array['feature_include'] = $user_roles_includes;
$rend_array['menu'] = getMenu();
$rend_array['content'] = $user_roles_content;
$rend_array['breadcrumbs'] = $user_roles_breadcrumbs;

// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);

// output page content
echo $out;

?>