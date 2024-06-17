<table class="menu">
<?PHP
if(!empty($menu)){
  foreach ($menu as $item){
	echo "<tr><td class='menu'><a href='/".convertDirs($item[1])."'>".$item[0]."</a></td></tr>";
  }	
}
?>
</table>
