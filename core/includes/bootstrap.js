// enable send to clipboard for class "allowCopy"
$(function() {
   $('.allowCopy').click(function() {
     $(this).focus();
     $(this).select();
     document.execCommand('copy');
   });
});

// Custom pause function
function wait(ms)
{
   var start = new Date().getTime();
   var end = start;
   while(end < start + ms) {
     end = new Date().getTime();
  }
}

// test for date in mm/dd/yyyy format
function isDate(txtDate)
{
    var currVal = txtDate;
    if(currVal == '')
        return false;
    
    var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; //Declare Regex
    var dtArray = currVal.match(rxDatePattern); // is format OK?
    
    if (dtArray == null) 
        return false;
    
    //Checks for mm/dd/yyyy format.
    dtMonth = dtArray[1];
    dtDay= dtArray[3];
    dtYear = dtArray[5];        
    
    if (dtMonth < 1 || dtMonth > 12) 
        return false;
    else if (dtDay < 1 || dtDay> 31) 
        return false;
    else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) 
        return false;
    else if (dtMonth == 2) 
    {
        var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
        if (dtDay> 29 || (dtDay ==29 && !isleap)) 
                return false;
    }
    return true;
}

// convert standard date format (mm/dd/yyyy) to ISO standard (yyyy-mm-dd)
function dateToISO(txtDate)
{
    var currVal = txtDate;
    if(currVal == '')
        return false;
    
    var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; //Declare Regex
    var dtArray = currVal.match(rxDatePattern); // is format OK?
    
    if (dtArray == null) 
        return false;
    
    //parse from mm/dd/yyyy format.
    dtMonth = dtArray[1];
    dtDay= dtArray[3];
    dtYear = dtArray[5];        
    
	isoDate = dtYear +"-"+dtMonth+"-"+dtDay 
    return isoDate;
}


// basic test for numberic values
function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

// returns today's date in MM/DD/YYYY format
function todayUS(){
	var date = new Date();
	var year = date.getUTCFullYear();
	var month = date.getUTCMonth();
	var day = date.getUTCDate();
	//month 2 digits
	month = ("0" + (month + 1)).slice(-2);
	var formattedDate = month + '/' + day + "/" + year;	
	return formattedDate;
}

// given a number of days future, returns that date in MM/DD/YYYY format
function futureUS(DaysToAdd){
	var date = new Date();
	date.setDate(date.getDate() + DaysToAdd); 	
	var year = date.getUTCFullYear();
	var month = date.getUTCMonth();
	var day = date.getUTCDate();
	//month 2 digits
	month = ("0" + (month + 1)).slice(-2);
	var formattedDate = month + '/' + day + "/" + year;	
	return formattedDate;
}

// returns ajax errors in a more readable format
function ajaxErrors(jqXHR, exception){
	var msg = '';
	if (jqXHR.status === 0) {
		msg = 'Could not connect.\n Verify Network.';
	} else if (jqXHR.status == 404) {
		msg = 'Requested page not found. [404]';
	} else if (jqXHR.status == 500) {
		msg = 'Internal Server Error [500].';
	} else if (exception === 'parsererror') {
		msg = 'Requested JSON parse failed.';
	} else if (exception === 'timeout') {
		msg = 'Time out error.';
	} else if (exception === 'abort') {
		msg = 'Ajax request aborted.';
	} else {
		msg = 'Uncaught Error.\n' + jqXHR.responseText;
	}
	console.log(msg);
}

// returns true if variable is set
function isSet(varTest){
	if (typeof varTest == "undefined" || varTest == null){
		return false;
	}else{
		return true;
	}
}

// parses the URL string
function GetURLParameter(iParam){
	var sPageURL = window.location.href;
	var intStart = sPageURL.indexOf('//') + 2;
	var strVars = sPageURL.substring(intStart); 
	var arrURL = strVars.split('/'); 
	return arrURL[iParam]; 
}

// insert item into array at supplied index
function insertArray({theArray, theItem, theIndex}){
	var arrA = thaArray.slice(0, theIndex-1);
	var arrB = thaArray.slice(theIndex);
	var strNew = implode(arrA,"!") + theItem + implode(arrC,"!");
	return explode(strNew,"!")
}

// decode HTML entities
function htmlDecode(input)
{
  var doc = new DOMParser().parseFromString(input, "text/html");
  return doc.documentElement.textContent;
}

function addWiki(target){
	var divMenu = $("#"+target);
	var tblMenu = $(divMenu).children('table').first(); 
	var objTbody = $(tblMenu).children('tbody').first();
	var strModule = '';
	if(isSet(GetURLParameter(1))){ strModule = GetURLParameter(1);}
	if(strModule.length == 0){strModule = 'general';}
    var strMnuWiki = "<tr><td class='menu'><a onclick=\"window.open('/wiki/"+strModule+"/view','_new',";
	strMnuWiki += "'menubar=no,toolbar=no,scrollbars=1,location=no,directories=no,status=no,dependent,width=725,height=600,left=25,top=35');";
	strMnuWiki += "return false;\" class=\"jlink\">Help</a><td></tr>";
	$(objTbody).children('tr').last().before($(strMnuWiki));
}	

function cmsfEncode(string){
	rtnstring = string;
	var arrayEncode = [
		['\\\\' , '_e1'],
		['&' , '_e2'],
		['/' , '_e3'],
		['"' , '_e4'],
		["'" , '_e5'],
		['#' , '_e6'],
		[',' , '_e7'],
		['(' , '_e8'],
		[')' , '_e9']
	];			
	for (var i = 0; i < arrayEncode.length; i++) { 
		var sRegExInput = arrayEncode[i][0];   
		var sRegExOutput = arrayEncode[i][1];  		
		string = string.replaceAll(sRegExInput, sRegExOutput)
	}		
	return string; 
}

function cmsfDecode(string){
	rtnstring = string;
	var arrayEncode = [
		['_e1' , '\\'],
		['_e2' , '&' ],
		['_e3' , '/' ],
		['_e4' , '"' ],
		['_e5' , "'" ],
		['_e6' , '#' ],
		['_e7' , ',' ],
		['_e8' , '(' ],
		['_e9' , ')' ],
		['_e10' , ' ']
	];			
	for (var i = 0; i < arrayEncode.length; i++) {
		var sRegExInput = new RegExp(arrayEncode[i][0], "g");    
		var sRegExOutput = arrayEncode[i][1];  		
		string = string.replace(sRegExInput, sRegExOutput)
	}		
	return string; 
}

function roundPHP(num, dec){
  var num_sign = num >= 0 ? 1 : -1;
  return parseFloat((Math.round((num * Math.pow(10, dec)) + (num_sign * 0.0001)) / Math.pow(10, dec)).toFixed(dec));
}

// Actions on load
$(document).ready(function(){ 
    // none at this time...	
});	
