<?php
/*   
  user menu management
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

// set roles for menu items
if($subfeature == "roles"){
	$mid = $subfeature = get("p3");
	$form = "<DIV class=\"center\">\r\n";  
	$form .= "<form class='cmxform' id='frmAssign' name='frmAssign'>\r\n";	
	$o_database = new db(); 
	$o_database2 = new db(); 
	// get all roles
	$o_database->query("SELECT rid, rname FROM roles");
	$roles = $o_database->resultset();
	// get current menu item
	$o_database2->query("SELECT mroles, dname FROM menu WHERE mid = $mid");
	$itemRow = $o_database2->single();
	$dname = $itemRow['dname'];
	$arrRoles = explode(",",$itemRow['mroles']); 
	unset($o_database2);	
	$options = "";
	$cntOptions = 0;
	foreach ($roles as $role){
		$cntOptions++;
		$selected = "";
		if(in_array($role['rid'],$arrRoles)){$selected = " SELECTED = 'true'";}
		$options .= "<OPTION value='".$role['rid']."'$selected >".$role['rname']."</OPTION>\r\n";
	}
	$form .= "<fieldset id='set_options'>\r\n";
	$form .= "<SELECT id='frmAssignOptions' name='frmAssignOptions' multiple=true' class=\"center\" size = \"cntOptions\">\r\n$options";	
	$form .= "</SELECT>\r\n";
	$form .= "</fieldset>\r\n";	
	$form .= "<fieldset id='set_buttons'>\r\n";
	$form .= "<p class='form-group center'>\r\n";
	$form .= "<input type='button' id='frmAssignbtnSave' name='btnSave' value='Save'>\r\n";
	$form .= "<input type='button' id='frmAssignbtnClear' name='btnClear' value='Clear'>\r\n";
	$form .= "<input type='button' id='frmAssignbtnAll' name='btnAll' value='All'>\r\n";
	$form .= "</p>\r\n";
	$form .= "</fieldset>\r\n";
	$form .= "<input type='hidden' id='frmAssignMRoles' name='frmAssignMRoles' value='".$itemRow['mroles']."'>\r\n";	
	$form .= "<input type='hidden' id='frmAssignMID' name='frmAssignMID' value='".$mid."'>\r\n";
	$form .= "</form>\r\n";
	$form .= "</div>\r\n";
	//Release the object
	unset($o_database);		
	$rend_array['page_name'] = "Roles for \"$dname\"";
	$menu_content = $form;	
	$menu_breadcrumbs = array();
	$menu_breadcrumbs[] = array("Site Configuration", "site_config");		
	$menu_breadcrumbs[] = array("Menu management", "menu");
	$menu_breadcrumbs[] = array("Menu item assignment", "menu-assign");
}

// maintain menu items
if($subfeature == "setroles"){	
    $o_database = new db();
	//Set the query
    $o_database->query('UPDATE menu SET mroles = :mroles WHERE mid = :mid');
	//Bind the data
	$o_database->bind(':mroles', post('frmAssignMRoles'));
	$o_database->bind(':mid', post('frmAssignMID'));
	//Execute the query and add default user role
	if($o_database->execute()){
		$strMessage = "Role update completed";
	}else{
		$strMessage = "ERROR:  The Role update was NOT saved";
	}	
	setMessage($strMessage);
	$new_page = "http://"._DOMAIN."/menu/assign";
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}

// assign menu items
if($subfeature == "assign"){
	$table = "<TABLE class='center'>\r\n";
	$table .= "<TR class='header'><TD class='header'>Menu Item</TD></TR>\r\n";
	$o_database = new db(); 
	//Set the query
	$o_database->query('SELECT * FROM menu WHERE active = "1" ORDER BY dname');
	//Run the query
	$rows = $o_database->resultset();
	foreach ($rows as $row){
		$table .= "<TR><TD><a href='/menu/roles/".$row['mid']."'>".$row['dname']."</a></TD></TR>\r\n";
	}
	$table .= "</TABLE>\r\n";
	//Release the object
	unset($o_database);	
	$rend_array['page_name'] = "Menu item assignment";
	$menu_content = $table;	
	$menu_breadcrumbs = array();
	$menu_breadcrumbs[] = array("Site Configuration", "site_config");		
	$menu_breadcrumbs[] = array("Menu management", "menu");
}

// base menu
if(!$subfeature){
	// build menu
	$menu_menu = array();
	array_push($menu_menu, array('Assign menu Items', 'menu-assign'));
	$menu_content = "<table class='content'>";
	foreach ($menu_menu as $item){
		$menu_content .= "<tr><td class='content'><a href='".convertDirs($item[1])."'>".$item[0]."</a></td></tr>";
	}
	$rend_array['page_name'] = "Menu Management";
	$menu_content .= "</table>";	
	$menu_breadcrumbs = array();
	$menu_breadcrumbs[] = array("Site Configuration", "site_config");	
}

// set replacable block content
$menu_includes[] = "<script type='text/javascript' src='/url/core-includes/menu.js'></script>";	
$menu_includes[] = "<link rel='stylesheet' type='text/css' href='/url/core-includes/menu.css'>"; 
$rend_array['feature_include'] = $menu_includes;
$rend_array['menu'] = getMenu();
$rend_array['content'] = $menu_content;
$rend_array['breadcrumbs'] = $menu_breadcrumbs;

// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);

// output page content
echo $out;

?>