<?PHP
/* Main page for dmfatlanta theme 
*/
?>
<HTML>
<?PHP 
require_once("config.php");
require_once("head.tpl");
?>
<BODY>
<DIV id="page">
    <DIV id="banner" class="row">
    <?PHP 
    require_once("banner.tpl");
    ?>
    </DIV>
    <DIV id="main" class="row">
         <DIV id="menu" class="box">
        <?PHP 
        require_once("menu.tpl");
        ?>
        </DIV> 
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