<?PHP
/* Main page for dmfatlanta theme 
*/
$nowrap = true; 
?>
<HTML>
<?PHP 
require_once("config.php");
require_once("head.tpl");
?>
<BODY>
<?PHP
echo displayMessage();
?>
<DIV id="page">
    <DIV id="main" class="row">
        <DIV id="body" class="box">
        <?PHP 
        require_once("body.tpl");
        ?>
        </DIV>
    </DIV>
    <DIV id="footer" class="row">
    <?PHP 
    require_once("footer.tpl");
    ?>
    </DIV>
</DIV>
</BODY>
</HTML>