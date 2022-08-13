<?php
/*   
  Module usage wiki
*/

// get sub-feature
$subfeature = get("p2");
$subsubfeature = get("p3");
if(!$subfeature){$subfeature = 'home';}
$wiki_content = "";
$wiki_ajax = false;

// test permissions
$req_access = array(1,2,3);
if(!confirmAccess($req_access)){
	$_SESSION['_message'] = "Invalid access attempt";
	$new_page = "http://"._DOMAIN;
	//Redirect browser
	header("Location: $new_page"); 
	exit();
}
$bolEditAccess = false;
if(confirmAccess(_WIKI_CHANGE)){
	$bolEditAccess = true;
}

//GLOBAL VARIABLES
$wiki_content = "";
$wiki_ajax = false;

// local functions

function wiki_getNextID($strModule){
	$o_database = new db(); 
	//Set the query
	$o_database->query('SELECT ordinal FROM wiki WHERE module = :module ORDER BY ordinal DESC');
	//Bind the data
	$o_database->bind(':module', $strModule);
	//Run the query
	$row = $o_database->single();
	$intOrd = $row['ordinal'];
	//Release the object
	unset($o_database);	
	$intNextOrd = $intOrd + 1;
	return $intNextOrd; 		
}

if($subsubfeature == 'view'){
	$o_database = new db(); 
	//Set the query
	$o_database->query('SELECT * FROM wiki WHERE module = :module ORDER BY ordinal');
	//Bind the data
	$o_database->bind(':module', $subfeature);
	//Run the query
	$rows = $o_database->resultset();
	unset($o_database);	
	$intRowCount = sizeof($rows);
	if($intRowCount > 0){
		// build TOC
		$wiki_content .= "<div><a name='top'></a>";
		$wiki_content .= "<p><b>Table of Contents</b></p>";
		$wiki_content .= "<ul>";
		foreach($rows as $row){
			$wiki_content .= "<li><a href=\"#" . $row['title'] . "\">".$row['title']."</a></li>";
		}
		$wiki_content .= "</ul></div>";
		// Display wiki data
		$wiki_content .= "<div id='entries'>";
		$intIdx = 0;
		foreach($rows as $row){
			$intIdx++;			
			$strID = $row['wid'];
			$strUpDisbled = ($intIdx==1)?"disabled":"";
			$strDnDisbled = ($intIdx==$intRowCount)?"disabled":"";
			$strUp = "[<a class='jlink up $strUpDisbled' wid='$strID' module='$subfeature' id='moveup__".$strID."'>Up</a>]"; 
			$strDn = "[<a class='jlink down $strDnDisbled' wid='$strID' module='$subfeature' id='movedown__".$strID."'>Down</a>]"; 
			$wiki_content .= "<div id='div__$strID' class='wiki_item' ordinal='".$row['ordinal']."' wid='$strID'>";
			$wiki_content .= "<div><a name=\"" . $row['title'] . "\" id='title__$strID'  class='wiki_title'>".$row['title']."</a></div>";
			$wiki_content .= "<div id='content__$strID' class='wiki_content'>" .  html_entity_decode($row['content']) . "</div>";
			$strDel = "[<a class='jlink' id='delentry' wid='$strID' module='$subfeature'>Delete</a>]";
			$wiki_content .= "<div class='meta'>[<a href='#top'>Top</a></li>]";
			if($bolEditAccess){$wiki_content .= "[<a class='jlink' id='editentry' wid='$strID' module='$subfeature'>Edit</a>]".$strDel.$strUp.$strDn;}			
			$wiki_content .= "</div></div><hr />";
		}		
		$wiki_content .= "</div>";
	}
	if($bolEditAccess){
		$wiki_content .= "<div class='meta center'>[<a class='jlink' id='addnew' module='$subfeature'>Add New</a>]</div>";
	}
}

if($subsubfeature == 'edit'){
	$intWID = get("p4");
	$o_database = new db(); 
	//Set the query
	$o_database->query('SELECT * FROM wiki WHERE wid = :wid');
	//Bind the data
	$o_database->bind(':wid', $intWID);
	//Run the query
	$row = $o_database->single();	
	$wiki_content .= "<form class='center' id='wiki_form_edit'>";	
	$wiki_content .= "<div class='center'>Title: <input type='text' size='85' maxlength='255' value='".$row['title']."' id='wiki_title' name='wiki_title'></div>";
	$wiki_content .= "<div class='center'><textarea class='trumbowyg' id='wiki_content' name='wiki_content'>".$row['content']."</textarea></div>";	
	$wiki_content .= "<div class='center'>[<a href='/wiki/$subfeature/view'>Cancel</a>][<a class='jlink' id='saveentry'>Save</a>]</div><p />";
	$wiki_content .= "<div><input type='hidden' id='wiki_module' name='wiki_module' value='$subfeature'><input type='hidden' id='wiki_wid' name='wiki_wid' value='$intWID'></div><p />";
	$wiki_content .= "</form>";	
}

if($subsubfeature == 'update'){
	if($bolEditAccess){
		$wiki_ajax = true;
		$intWID = post("wiki_wid");
		$o_database = new db(); 
		$o_database->query('UPDATE wiki SET title = :title, content = :content WHERE wid = :wid');
		//Bind the data
		$o_database->bind(':title', post("wiki_title"));
		$o_database->bind(':content', post("wiki_content"));
		$o_database->bind(':wid', $intWID );
		if($o_database->execute()){
			$out = "true";
		}else{
			$out = "ERROR:  The entry Was NOT Deleted";
		}
	}else{
		$out = "ERROR: Insufficient Permissions";
	}
	
}

if($subsubfeature == 'delete'){
	if($bolEditAccess){	
		$wiki_ajax = true;
		$intWID = get("p4");
		$o_database = new db(); 
		$o_database->query('DELETE FROM wiki WHERE wid = :wid');
		//Bind the data
		$o_database->bind(':wid', $intWID);
		if($o_database->execute()){
			$out = "true";
		}else{
			$out = "ERROR:  The entry Was NOT deleted";
		}	
	}else{
		$out = "ERROR: Insufficient Permissions";
	}	
}

if($subsubfeature == 'add'){
	if($bolEditAccess){	
		$wiki_content .= "<form class='center' id='wiki_form_new'>";	
		$wiki_content .= "<div class='center'>Title: <input type='text' size='85' maxlength='255' value='' id='wiki_title' name='wiki_title'></div>";
		$wiki_content .= "<div class='center'><textarea class='trumbowyg' id='wiki_content' name='wiki_content'>New entry...</textarea></div>";	
		$wiki_content .= "<div class='center'>[<a href='/wiki/$subfeature/view'>Cancel</a>][<a class='jlink' id='insertnew'>Add</a>]</div><p />";
		$wiki_content .= "<div><input type='hidden' id='wiki_module' name='wiki_module' value='$subfeature'></div><p />";
		$wiki_content .= "</form>";
	}else{
		$out = "ERROR: Insufficient Permissions";
	}	
}

if($subsubfeature == 'insert'){
	if($bolEditAccess){	
		$strModule = post("wiki_module");
		$intNextID = wiki_getNextID($strModule);
		$o_database = new db(); 
		$o_database->query('INSERT INTO wiki (module, ordinal, title, content) VALUES (:module, :ordinal, :title, :content)');
		$o_database->bind(':module', $strModule);
		$o_database->bind(':ordinal', $intNextID );
		$o_database->bind(':title', post("wiki_title"));
		$o_database->bind(':content', post("wiki_content"));
		if(!$o_database->execute()){
			return "ERROR:  The entry Was NOT inserted";
		}
	}else{
		$out = "ERROR: Insufficient Permissions";
	}	
}

if($subsubfeature == 'move'){
	if($bolEditAccess){		
		$wiki_ajax = true;
		$arrOpts = explode('!',get("p4"));
		$o_database = new db(); 
		$o_database->query('UPDATE wiki SET ordinal = :ordinal WHERE wid = :wid');
		//Bind the primary
		$o_database->bind(':ordinal', $arrOpts[2]);
		$o_database->bind(':wid', $arrOpts[1]);
		if($o_database->execute()){
			$o_database->query('UPDATE wiki SET ordinal = :ordinal WHERE wid = :wid');
			//Bind the secondary
			$o_database->bind(':ordinal', $arrOpts[4]);
			$o_database->bind(':wid', $arrOpts[3]);	
			if($o_database->execute()){
				$out = "true";
			}else{
				$out = "ERROR: The move was NOT successful";
			}
		}else{
			$$out = "ERROR: The move was NOT successful";
		}
	}else{
		$out = "ERROR: Insufficient Permissions";
	}	
}

// render content
if(!$wiki_ajax){
// set replacable block content
$wiki_includes[] = "<link rel='stylesheet' href='/url/core-includes/wiki.css'>";
$wiki_includes[] = "<link rel='stylesheet' href='/url/3rdparty-trumbowyg-dist-ui/trumbowyg.min.css'>";
$wiki_includes_low[] = "<script src='/url/3rdparty-trumbowyg-dist/trumbowyg.min.js'></script>";
$wiki_includes_low[] = "<script type='text/javascript' src='/url/core-includes/wiki.js'></script>";
$rend_array['feature_include'] = $wiki_includes;	
$rend_array['feature_include_low'] = $wiki_includes_low;
$rend_array['content'] = $wiki_content;

// merge block with theme
$out = renderPhpToString(_THEME_NOWRAP, $rend_array);
}
// output page content
echo $out;

?>