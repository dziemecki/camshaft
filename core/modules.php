<?php
/*   
  Console for module management
*/

// get sub-module
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

//GLOBAL vars
$modules_ajax = false;
$modules_breadcrumbs = array();
$modules_content = '';

$req_access = array(1);
if(!confirmAccess($req_access)){
	$_SESSION['_message'] = "Invalid access attempt";
	$new_page = "http://"._DOMAIN;
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}

// functions
function lookUpModule($strModule){
	$o_database = new db(); 
	//Set the query
	$o_database->query('SELECT * FROM features WHERE fid = :fid');
	//Bind the data
	$o_database->bind(':fid', $strModule);
	//Run the query
	$row = $o_database->single();
	//Release the object
	unset($o_database);	
	return $row; 
}
// given a module, returns array of associated role ids
function getMRoles($strModule){
	$dir = '../custom/';
	$info_file = $dir . $strModule  . "/" . $strModule . ".info"; // json module metadata
	$strInfo = file_get_contents($info_file); 
	$arrInfo = json_decode($strInfo, true);
	$arrName = $arrInfo['roles'];	
	$o_database = new db();
	$arrReturn = array();
	foreach ($arrName as $key=>$value){
		//Set the query
		$o_database->query('SELECT rid FROM roles WHERE rname = :rname');
		//Bind the data
		$o_database->bind(':rname', $key);
		//Run the query
		$row = $o_database->single();
		$arrReturn[] = $row['rid'];
	} 
	//Release the object
	unset($o_database);	
	return $arrReturn; 		
}

function pregenModules($strModule,$operation){
	$strModFile = "custom-$strModule-files";
	if($operation==='add'){
		$dirModFiles = "../custom/$strModule/files";
		if (file_exists($dirModFiles)) {
			// confirm it's not already logged
			$arrModuleDirs = _PREGEN_CORE['feature_dirs'];
			write_log(1);
			if(!in_array($strModFile,$arrModuleDirs)){
				// add it
				$arrModuleDirs[] = $strModFile;
				// write it back
				$strPregen = file_get_contents(_PREGEN_FILE);
				$arrPregen = json_decode($strPregen, true);
				write_log(implode($arrModuleDirs,"!"));
				$arrPregen['core']['feature_dirs'] = $arrModuleDirs;
				$strPregen = json_encode($arrPregen);
				file_put_contents(_PREGEN_FILE, $strPregen);								
			}			
		}
	}
	if($operation==='delete'){	
		$arrModuleDirs = _PREGEN_CORE['feature_dirs'];
		if(in_array($strModFile,$arrModuleDirs)){
			// remove it
			if (($key = array_search($strModFile, $arrModuleDirs)) !== false) {
				unset($arrModuleDirs[$key]);
			}			
			// write it back
			$strPregen = file_get_contents(_PREGEN_FILE);
			$arrPregen = json_decode($strPregen, true);
			$arrPregen['core']['feature_dirs'] = $arrModuleDirs;
			$strPregen = json_encode($arrPregen);
			file_put_contents(_PREGEN_FILE, $strPregen);								
		}			
	}	
}

if(!$subfeature){
	// build modules list
	$modules_list = array();
	// _pc look for all direcories in root/custom
	$dirs = array_filter(glob('../custom/*'), 'is_dir');
	$modules_content .= "<table class='content'>";
	foreach ($dirs as $dir){
		$module = substr($dir,10);
		$info_file = $dir . "/" . $module . ".info"; // json module metadata
		if(file_exists($info_file)){
			$strInfo = file_get_contents($info_file); 
			$arrInfo = json_decode($strInfo, true);
			if(isset($arrInfo['name'])){$strName = $arrInfo['name'];}
			$arrModule = lookUpModule($module);
			$strActive = "Inactive";
			if($arrModule && $arrModule['active']){$strActive = "Active";}
			//// same thing for a module config file ?
			$modules_content .= "<tr style='text-align:left;'><td><a href='manage/$module'>".$strName."</a></td><td>$strActive</td></tr>\r\n";	
		}		
	}
	$modules_content .= "</table>";
	$rend_array['page_name'] = "Modules Administration";
	$modules_breadcrumbs[] = array("Site Configuration", "site_config"); 
}	
// create an array items for name and description

if($subfeature === "manage"){
	$module = get("p3");
	$arrModule = lookUpModule($module);
	$strSubmit = "Activate";
	if(isset($arrModule['active']) && $arrModule['active']){$strSubmit = "Deactivate";}	
	$dir = '../custom/';
	$info_file = $dir . $module  . "/" . $module . ".info"; // json module metadata
	$strInfo = file_get_contents($info_file); 
	$arrInfo = json_decode($strInfo, true);
	if(isset($arrInfo['name'])){$strName = $arrInfo['name'];}
	$modules_content .= "<form action='/modules/edit/$module' id='frmEditModule'><table class='content' style='width:75%;'>";
	foreach ($arrInfo as $key => $value){
		$strVal = $value;
		if(is_array($value)){
			if(sizeof($value) == 0){
				$strVal = 'n/a';
			}else{
				$strVal = '';
				foreach ($value as $k => $v){
					$strVal .= $k .": ". $v."<br />";
				}
			}	
		}
		$modules_content .= "<tr style='text-align:left;'><td>$key:</td><td>$strVal</td></tr>\r\n";	
	}
	$rend_array['page_name'] = "Manage ".$strName;		
	$modules_content .= "<tr><td colspan='2'><input type='submit' value='$strSubmit'><!frmEditModulePurge!></td></tr>\r\n";	
	$modules_content .= "</table></form>\r\n";	
	if($strSubmit == "Activate"){
		$strfrmEditModulePurge = "<input type='button' value='Purge' onclick='location.href=\"/modules/purge/confirm/$module\"'>";		
	}else{
		$strfrmEditModulePurge = '';
	}
	$modules_content_form_keys = array(); 
	$modules_content_form_keys['frmEditModulePurge'] = $strfrmEditModulePurge; 
	$modules_content = replaceKeyStrings($modules_content, $modules_content_form_keys);
	$modules_breadcrumbs[] = array("Site Configuration", "site_config"); 	
	$modules_breadcrumbs[] = array("Modules Administration", "modules"); 	
}

if($subfeature === "edit"){
	$module = get("p3");
	$arrModule = lookUpModule($module);
	$dir = '../custom/';
	$info_file = $dir . $module  . "/" . $module . ".info"; // json module metadata
	$strInfo = file_get_contents($info_file); 
	$arrInfo = json_decode($strInfo, true);
	$o_database = new db();
	$strOperation = '';
	$bolSuccess = false;
	if(is_null($arrModule) || !isset($arrModule['active'])){
		$strOperation = "new";
		$o_database->query('INSERT INTO features (fid, active, vars) VALUES (:fid, :active, :vars)');
		//Bind the data
		$o_database->bind(':fid', $module);		
		$o_database->bind(':active', true);		
		$o_database->bind(':vars', NULL);
		
	}else{
		$o_database->query('UPDATE features SET active = :active WHERE fid = :fid'); 
		//Bind the data
		$o_database->bind(':fid', $module);
		if($arrModule['active'] && $arrModule['active']){
			$o_database->bind(':active', false);
			$strOperation = "deactivate";			
		}else{
			$o_database->bind(':active', true);
			$strOperation = "activate";			
		}
	}
	if($o_database->execute()){
		// modules update successful
		//Release the object
		unset($o_database);
		// Update roles
		$o_database = new db();
		$arrRoles = $arrInfo['roles'];	
		$bolRoles = true;
		if($strOperation!='deactivate'){ // role deactivation is handled in separate 'purgeroles' subfeature.		    
			//update pregen
			pregenModules($module,'add');
			//insert roles
			$bolRoles = false;
			foreach($arrRoles as $key => $value){
				$o_database->query('INSERT INTO roles (rname, rdesc) VALUES (:rname, :rdesc)');
				$o_database->bind(':rname', $key);	
				$o_database->bind(':rdesc', $value);	
				try{
					$o_database->execute();
					$bolRoles = true;					
				}catch(PDOException $e){
					// unique key constraint errors can be ignored.
					$emessage = $e->getMessage( );
					if(strpos($emessage, 'Duplicate entry') > 0){
						// ignore attempts to re-insert roles
						$bolRoles = true;												
					}else{
						// some other error occured
						$bolRoles = false;
					}	
				}
			}			
		}else{
			//update pregen
			pregenModules($module,'delete');
		}
		// Update menus
		if($bolRoles){
			//Release the object
			unset($o_database);
			$o_database = new db();
			$strMenu = $arrInfo['menuname'];
			if($strOperation == 'deactivate'){
				$o_database->query('DELETE FROM menu WHERE mname = :mname');
				$o_database->bind(':mname', $module);
			}else{
				// first get the current mroles
				$arrMRoles = getMRoles($module);
				$srtMRoles = implode(",",$arrMRoles);
				$srtMRoles = "1," . $srtMRoles;
				// second, insert the menu entry
				$o_database->query('INSERT INTO menu (mname, dname, maddr, weight, mroles, active) VALUES (:mname, :dname, :maddr, :weight, :mroles, :active)');
				$o_database->bind(':mname', $module);	
				$o_database->bind(':dname', $strMenu);
				$o_database->bind(':maddr', $module);
				$o_database->bind(':weight', 1);
				$o_database->bind(':mroles', $srtMRoles);
				$o_database->bind(':active', true);
			}
			if($o_database->execute()){$bolSuccess = true;};
		}
	}
	//Release the object
	unset($o_database);	
	if($bolSuccess){
		setMessage($module ." updated.");
		$new_page = "http://"._DOMAIN."/modules/manage/$module";
		//Redirect browser
		header("Location: $new_page"); 
		exit();		
	}else{
		setMessage("An error has occurred.");		
		$new_page = "http://"._DOMAIN."/modules/manage/$module";
		//Redirect browser
		header("Location: $new_page"); 
		exit();			
	}	
}
// This will remove all roles and userroles for modules no longer installed
if($subfeature === "purge"){
	$subsubfeature = get("p3");
	$module = get("p4");
	$modules_content = $subsubfeature; 
	if($subsubfeature == "confirm"){
		$modules_content = "<div style='width:80%' class='center'>This will remove all remaining database values, including roles and role assignments, ";
		$modules_content .= "for \"$module\". This process cannot be reverted. Once complete, you should manually remove the module from the custom folder.</div>";
		$modules_content .= "<div style='text-align:center' class='center'><form class='cmxform' id='frmPurgeAll' method='post' ";
		$modules_content .= "action='/modules/purge/continue/$module'><input type='submit' value='Purge All'></form></div>";
		$rend_array['page_name'] = "Purge Modules";
		$modules_breadcrumbs[] = array("Site Configuration", "site_config"); 	
		$modules_breadcrumbs[] = array("Modules Administration", "modules"); 	
		
	}
	if($subsubfeature === 'continue'){	
		$bolSuccess = true;		
		// get associated role ids
		$arrRoleIDs = getMRoles($module);
		// delete from userroles
		$o_database = new db();
		$o_database->query('DELETE FROM userroles WHERE rid = :rid');
		foreach($arrRoleIDs as $rid) {
			$o_database->bind(':rid', $rid);
			if(!$o_database->execute()){$bolSuccess = false;};
		}
		unset($o_database);
		// delete from roles
		$o_database = new db();
		$o_database->query('DELETE FROM roles WHERE rid = :rid');
		foreach($arrRoleIDs as $rid) {
			$o_database->bind(':rid', $rid);
			if(!$o_database->execute()){$bolSuccess = false;};
		}
		unset($o_database);			
		// delete from modules		
		$o_database = new db();
		$o_database->query('DELETE FROM features WHERE fid = :fid');
		$o_database->bind(':fid', $feature);
		if(!$o_database->execute()){$bolSuccess = false;};
		unset($o_database);	
		if($bolSuccess){
			setMessage('Module "'.$module.'" purged');
			$new_page = "http://"._DOMAIN."/modules/";
			//Redirect browser
			header("Location: $new_page"); 
			exit();		
		}else{
			setMessage("An error has occurred.");		
			$new_page = "http://"._DOMAIN."/modules/";
			//Redirect browser
			header("Location: $new_page"); 
			exit();			
		}			
	}
}

// render content
if(!$modules_ajax){
// set replacable block content
$rend_array['content'] = $modules_content;
$rend_array['breadcrumbs'] = $modules_breadcrumbs;
$rend_array['menu'] = getMenu();

// merge block with theme
$out = renderPhpToString(_THEME_MAIN, $rend_array);
}
// output page content
echo $out;

?>