$(".trumbowyg").trumbowyg({
    btns: [
        ['formatting'],
        ['strong', 'em', 'del'],
        ['superscript', 'subscript'],
        ['link'],
        ['insertImage'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['unorderedList', 'orderedList'],
        ['horizontalRule'],
        ['removeformat'],
        ['fullscreen']
    ],
	tagsToRemove: ['script'],
	autogrow: true,
	svgPath: '/files/icons.svg'
});

function resetMoveClass(){
	var intEntryCount = 0;
	$('#entries').children('div').each(function () {
		if(isSet($(this).attr('id'))){
			intEntryCount++;
		}
	});
   	var intPosition = 1;
	$('#entries').children('div').each(function () {
		var intWID = $(this).attr('wid');
		if(intPosition == 1){
			$('#moveup__'+intWID).addClass('disabled');
		}else{
			$('#moveup__'+intWID).removeClass('disabled');
		}
		if(intPosition == intEntryCount){
			$('#movedown__'+intWID).addClass('disabled');
		}else{
			$('#movedown__'+intWID).removeClass('disabled');
		}
		intPosition++;
	});		
}

// actions on load
$(function() {
	if($('#wiki_form_new').length){
		$('#wiki_title').focus();
	}
});

// actions on anchor click
$(document).on('click', 'a', function(){
	var idCurrEle = $(this).attr('id');
    if (idCurrEle == "addnew"){
		var strModule = $(this).attr('module');
		var newPage = '/wiki/'+strModule+'/add';	
		window.open(newPage,'_self');					
	}
    if (idCurrEle == "delentry"){
		var strModule = $(this).attr('module');	
		var strWID = $(this).attr('wid');	
		strEntryDelete= "/wiki/"+strModule+"/delete/"+strWID;
		$.get( strEntryDelete, function( data ) {
		  if(data == 'true'){
			// no error
			window.open(
			'/wiki/'+strModule+'/view/',
			'_self'
			)				
		  }else{
			// error
			alert(data);				
		  }
		});	 					
	}	
    if (idCurrEle == "insertnew"){
		var strModule = $('#wiki_module').val();
		var formAction = '/wiki/'+strModule+'/insert';
		var formData = $("#wiki_form_new").serialize();		
		$.ajax({
			type: "POST",
			url: formAction,
			data: formData,
			dataType: "text",
			success: function(jdata) {
				if(jdata.substr(0, 5) == "Error" ){
					// error
					alert(jdata);	
				}else{
					// no error
					window.open(
					'/wiki/'+strModule+'/view/',
					'_self'
					)
				}
			},
			error: function (jqXHR, exception) {ajaxErrors(jqXHR, exception);}
		});					
	}
	if (idCurrEle == "editentry"){
		var strModule = $(this).attr('module');	
		var strWID = $(this).attr('wid');
		var newPage = '/wiki/'+strModule+'/edit/'+strWID;	
		window.open(newPage,'_self');						
	}
    if (idCurrEle == "saveentry"){
		var strModule = $('#wiki_module').val();
		var formAction = '/wiki/'+strModule+'/update';
		var formData = $("#wiki_form_edit").serialize();		
		$.ajax({
			type: "POST",
			url: formAction,
			data: formData,
			dataType: "text",
			success: function(data) {
			  if(data == 'true'){
				// no error
				window.open(
				'/wiki/'+strModule+'/view/',
				'_self'
				)				
			  }else{
				// error
				alert(data);				
			  }
			},
			error: function (jqXHR, exception) {ajaxErrors(jqXHR, exception);}
		});					
	}
	if (idCurrEle.indexOf("moveup") > -1 || idCurrEle.indexOf("movedown") > -1){
		var intWID = $(this).attr('wid');
		var strModule = $(this).attr('module');
		var intOrd = $(this).attr('ordinal');
		var strThisEntry = '#div__'+intWID;	
		var strThisEntryHR = $(strThisEntry).next();		
		var strSibling;		
		if(idCurrEle.indexOf("moveup") > -1){			
			strSibling = '#'+$(strThisEntry).prev().prev().attr('id');			
		}else{
			strSibling = '#'+$(strThisEntry).next().next().attr('id');
		}	
		var intSibWID = $().attr('wid');
		strOpts = '!'+$(strThisEntry).attr('wid')+'!'+$(strSibling).attr('ordinal')+'!'+$(strSibling).attr('wid')+'!'+$(strThisEntry).attr('ordinal')+'!';
		strMoveEntry = "/wiki/"+strModule+"/move/"+strOpts;
		$.get( strMoveEntry, function( data ) {
		  if(data == 'true'){
			// no error
			if(idCurrEle.indexOf("moveup") > -1){			
				$(strSibling).before($(strThisEntry));
				$(strThisEntry).after($(strThisEntryHR));
			}else{
				$(strSibling).after($(strThisEntry));
				$(strThisEntry).before($(strThisEntryHR));				
			}
			resetMoveClass();
		  }else{
			// error
			alert(data);				
		  }
		});	 
	}
});
