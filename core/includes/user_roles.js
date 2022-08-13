// actions on form element click
$(document).on('click', 'input', function(){
	var idCurrEle = $(this).attr('id');
    if (idCurrEle == "frmAssignbtnClear"){
		$("#frmAssignOptions option").each(function()
			{$(this).prop('selected', false);})
	}	
    if (idCurrEle == "frmAssignbtnAll"){
		$("#frmAssignOptions option").each(function()
			{$(this).prop('selected', true);})
	}	
    if (idCurrEle == "frmAssignbtnSave"){		
		// set selected roles
		var strRoles = '';
		var $userSet = false;
		$("#frmAssignOptions option").each(function()
			{
				if($(this).prop('selected') == true){
					strRoles += $(this).val() + ',';
					if($(this).val()=='3') {$userSet = true;}
				}
			}
		);
		if(!$userSet){alert("'User' role not assigned. This user will have degraded access to site.")}
		if(strRoles.length > 0){strRoles = strRoles.slice(0,-1);}		
		var formAction = '/user_roles/setroles/';
		$('#frmAssignURoles').val(strRoles);	
		$('#frmAssign').attr("method", 'post');	
		$('#frmAssign').attr("action", formAction);	
		$('#frmAssign').submit();	
	}	
})