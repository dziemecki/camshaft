<?php
/*   
  Displays images stored in offline folders.
  Usage:  <img src="http://sample.org/url/dir-dir2-dir3.../file.name" /> 
  Should only be used for displayed or downloadable files.
*/
  //reject unlogged requests
  if(getPersistent("token")){$valid_user = true;}else{$valid_user = false;}
  // filter out ".." */
  $dirs = str_replace("..", "--", get("p2"));   
  // theme\template elements can always be accessed
  if($dirs == "themes-"._THEME){$theme_file = true;}else{$theme_file = false;} 
  // set key vars
  $location = convertDirs($dirs);
  $filename = get("p3");
  $file = $location . $filename; 
  $ext = pathinfo($file, PATHINFO_EXTENSION);
  // compile list of allowed directories
  $arrAllowedDirectories = $_DIR_ACCESS_ALLOWED;
  if(isset(_PREGEN_CORE['feature_dirs'])){
	  $arrAllowedDirectories = array_merge($_DIR_ACCESS_ALLOWED,_PREGEN_CORE['feature_dirs']);
  }
  if(isset(_PREGEN_CUSTOM['feature_dirs'])){
	  $arrAllowedDirectories = array_merge($_DIR_ACCESS_ALLOWED,_PREGEN_CUSTOM['feature_dirs']);
  }   
  // check for open extentions
  if(in_array($ext, $_OPEN_EXTS_ALLOWED)){$open_file = true;}else{$open_file = false;}  
  if((in_array($dirs, $arrAllowedDirectories) && $valid_user) || $theme_file || $open_file){	
    // render php
	if($ext == 'php'){
	    echo renderPhpToString($file);	
	}
	// output images for display
	if(in_array($ext,$_IMG_EXTS_ALLOWED)){
		$mime_type = mime_content_type($file);
		header('Content-Type: '.$mime_type);
		header('Content-Length: ' . filesize($file));
		readfile($file);
	} 
	// serve documents for display
	if(in_array($ext,$_DOC_EXTS_ALLOWED)){
		$mime_type = mime_content_type($file);
		header('Content-Type: '.$mime_type);
		header('Content-Length: ' . filesize($file));
		readfile($file);
	} 
	// output other files for download
 	if(in_array($ext,$_FIL_EXTS_ALLOWED)){	
		$quoted = sprintf('"%s"', addcslashes(basename($file), '"\\'));
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $quoted); 
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
	} 
  }
?>