<?php
/*   
  Main console for site management
*/

$req_access = array(1);
if(!confirmAccess($req_access)){
	$_SESSION['_message'] = "Invalid access attempt";
	$new_page = "http://"._DOMAIN;
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}

// build admin menu
$site_config_menu = array();
array_push($site_config_menu, array('Modules', 'modules'));
array_push($site_config_menu, array('Menu Items', 'menu'));
array_push($site_config_menu, array('Users', 'users'));
array_push($site_config_menu, array('User Roles', 'user_roles'));

$site_config_content = "<table class='content'>";
foreach ($site_config_menu as $item){
	$site_config_content = $site_config_content . "<tr><td class='content'><a href='".convertDirs($item[1])."'>".$item[0]."</a></td></tr>";
}
$site_config_content = $site_config_content . "</table>";
	

// set replacable block content
$rend_array['page_name'] = "Site Configuration";
$rend_array['menu'] = getMenu();
$rend_array['content'] = $site_config_content;

// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);

// output page content
echo $out;
?>