 var _current_class = "";
 var _current_form = "";

  $(function() {
    $( "#dialog-confirm" ).dialog({
      resizable: false,
      height:140,
      modal: false,
	  draggable: true,
      buttons: {
        Continue: function() {
          $( this ).dialog( "close" );
		  document.getElementById(_current_form).submit();
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  });
  
$(function() {
  $("#dialog-warning").dialog({
    /* title: t, */
    resizable: false,
    height: 160,
    modal: true,
    buttons: {
        "Ok" : function () {
            $(this).dialog("close");
        }
    }
}).parent().addClass("ui-state-error");
  });
    
  $(function() {
    $( "#tabs" ).tabs();
  }); 
  
  function displayElement(eID,fID) {
	$( "#dialog-confirm" ).dialog( "open");  
	$( "#dialog-confirm" ).dialog( "option", "modal", true );
    $( "#dialog-confirm" ).dialog( "close"); /* This block initiates modal status */ 	
	if (document.getElementsByClassName(eID)) {
		$(document.getElementsByClassName(eID)).css("visibility", "visible");
		_current_class = eID;
		_current_form = fID;
		$( "#dialog-confirm" ).dialog( "open");
	}
 }
 
   function hideElement(eID) {
	if (document.getElementsByClassName(eID)) {
		$(document.getElementsByClassName(eID)).css("visibility", "hidden");
		$(document.getElementsByClassName(eID)).css("modal", false);
		_current_class = eID;
	}
 }
  