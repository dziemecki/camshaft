<HEAD>
<link rel="shortcut icon" href="/url/themes-sample/sample.ico" type="image/vnd.microsoft.icon" />
<?PHP 
  echo "\r\n";
  foreach($core_include as $include){
    echo $include . "\r\n"; 
  }

  if(count($feature_include) > 0){
    foreach($feature_include as $include){
      echo $include . "\r\n"; 
    }  
  } 
?>
<link rel="stylesheet" type="text/css" href="/url/themes-sample/main.css">
<TITLE>
<?PHP 
  echo _SITE_NAME;
  if(isset($page_name)){
    if(strlen($page_name) > 0){
	  echo " : " . $page_name;	
	}
  }  
?>
</TITLE>
</HEAD>