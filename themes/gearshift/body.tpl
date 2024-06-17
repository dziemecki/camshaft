<DIV class="breadcrumbs" >
<?PHP
	$str_breadcrumbs = "";	  
	if(confirmAccess(array('terminal'))){
	  $str_breadcrumbs .= " >> <a href='/' class='breadcrumbs'>Home</a>";
	}	
	if(!empty($breadcrumbs)){	  
	  foreach ($breadcrumbs as $item){
		$str_breadcrumbs .= " >> <a href='/".convertDirs($item[1])."' class='breadcrumbs'>".$item[0]."</a>";	
	  }	
	}
	//if($nowrap){$str_breadcrumbs = "";};
	echo $str_breadcrumbs; 
?>
</DIV>
<?PHP
  echo displayMessage();
  $body_title = "";	
  if(isset($page_name)){
    if(strlen($page_name) > 0){
	  $body_title = $page_name;	
	}
  } 
    echo "<DIV class='title'>".$body_title."</DIV>". "\r\n"; 
	echo "<DIV class='content'>".$content."</DIV>". "\r\n"; 
	
	if(count($feature_include_low) > 0){
	  echo "<DIV class='include.low'>". "\r\n";
      foreach($feature_include_low as $include){
        echo $include . "\r\n"; 
      }
	  echo "</DIV>". "\r\n"; 
    } 
	
?>