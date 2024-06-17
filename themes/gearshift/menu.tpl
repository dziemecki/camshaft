<table class="menu">
<?PHP
if(!empty($menu)){
  foreach ($menu as $item){
    $target = "";
    if(isset($item[2])){
		$target = $item[2];
		$link = $item[1];
		$href = "<tr><td class='menu'><a onClick='window.open(\"/".$link."\",\"".$target."\",";
		$href .= "\"menubar=no,toolbar=no,scrollbars=1,location=no,directories=no,status=no,dependent,width=700,height=600,left=25,top=35\");";
		$href .= "return w?false:true;' class='jlink'>".$item[0]."</a></td></tr>\r\n";	
		echo $href;
	}else{
		echo "<tr><td class='menu'><a href='/".convertDirs($item[1])."'>".$item[0]."</a></td></tr>\r\n";
	}		
  }	
}
?>
</table>
