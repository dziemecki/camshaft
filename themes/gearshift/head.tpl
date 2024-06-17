<HEAD>
<link rel="shortcut icon" href="<?PHP echo $gear_shift_icon ?>" type="image/vnd.microsoft.icon" />
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
<script type='text/javascript' src='/url/themes-gearshift/theme.js'></script>
<STYLE>
<?PHP require_once("main.css.php"); ?>
</STYLE>
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