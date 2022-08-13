<?PHP
/* Default landing page for logged in user 
03/03/2020 DZ Added menu buttons for station class users
*/

$o_database = new db(); 
//Set the query
$o_database->query('SELECT fname, pname FROM users WHERE uid = :uid');
//Bind the data
$o_database->bind(':uid', getLUID());
//Run the query
$row = $o_database->single();
//Release the object
unset($o_database);

$pname = $row['pname'];
if(strlen($pname) == 0 || is_numeric($pname)){$pname = $row['fname'];} 

// set replacable block content
$rend_array['menu'] = getMenu();
$req_access = array('terminal');
if(confirmAccess($req_access)){
	$strMnuBtns = "<div class='center'>";
	foreach ($rend_array['menu'] as $mitem){
		$target = $mitem[0];
		$link = $mitem[1];		
		$strMnuBtns .= "<p><input type='button' class='station' value='". $target ."' link='".$link."'></p>";		
	}
	$strMnuBtns .= '</div>';
	$rend_array['content'] = $strMnuBtns;
	$home_includes[] = "<script type='text/javascript' src='/url/core-includes/home.js'></script>";
	$rend_array['feature_include'] = $home_includes;
}else{
	$rend_array['content'] = "<p class='center'>Welcome to Camshaft, $pname!</p>";
}


// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);

// output page content
echo $out;
?>