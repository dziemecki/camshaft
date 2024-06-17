<DIV class="breadcrumbs">
<?PHP
	if(!empty($breadcrumbs)){
	  $str_breadcrumbs = "";
	  foreach ($breadcrumbs as $item){
		$str_breadcrumbs .= " >> <a href='/".convertDirs($item[1])."' class='breadcrumbs'>".$item[0]."</a>";	
	  }	
	  echo $str_breadcrumbs; 
	}
?>
</DIV>
<?PHP
  $body_title = "";	
  if(isset($page_name)){
    if(strlen($page_name) > 0){
	  $body_title = $page_name;	
	}
  } 
    echo "<DIV class='title'>".$body_title."</DIV>";
	echo "<DIV class='content'>".$content."</DIV>";
	
	if(count($feature_include_low) > 0){
	  echo "<DIV class='include.low'>". "\r\n";
      foreach($feature_include_low as $include){
        echo $include . "\r\n"; 
      }
	  echo "</DIV>". "\r\n"; 
    } 
	
?>