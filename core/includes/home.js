$(document).on('click', 'input', function(){
	var idCurrEle = $(this).attr('id');	
	if(isSet($(this).attr("link"))){
		var linkCurrEle = $(this).attr("link");
		newURL = "/" + linkCurrEle.replace(/-/g,"/") + "/";
		window.location.href = newURL;
	}
})