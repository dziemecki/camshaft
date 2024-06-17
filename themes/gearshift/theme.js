// Actions on load
var bolLeft = false;
var bolTop = false;
var bolMobile = false;
var bolChromeOS = false;
var lastWinWidth;
var menuWidth;

function checkWidth(){
	var winWidth = $(window).width();
	$.getJSON( '/url/themes-gearshift/config.json', function( data ) {
		arrLeft = data.menuleftonly;
		arrTop = data.menutoponly;
		strModule = GetURLParameter(1);
		bolLeft = arrLeft.includes(strModule);
		bolTop = arrTop.includes(strModule);
	if(lastWinWidth != winWidth){
		var themeWidth = parseInt($('#page').css("width"));
		if(!bolTop & !bolLeft){
			if(themeWidth > winWidth){bolTop = true}else{bolLeft = true}
		}
		if(bolTop){	
			menuWidth = 0;
			$.ajax({url: '/url/themes-gearshift/menu.php', success: function(theMenu){ 
				$('#menutop').hide();
				$('#menutop').html(theMenu);
				$('#menutop tbody').each(function(){
					$('tr', this).each(function(){
						$('td', this).each(function(){								
							$('a', this).each(function(){
								emSize = parseInt($('a').css('font-size'))*0.6;
								strString = this.text;
								intString = strString.length * emSize;																
								if(intString > menuWidth){menuWidth = intString;}							
							})
						});	
					});		
				});
				addWiki('menutop');
				menuWidth = menuWidth + "px";
				$('#menutop').css("width",menuWidth);
				if(bolMobile){			
					$(".menu").addClass('mobile');
					$(".hamburger").addClass('mobile');
					$(".cross").addClass('mobile');					
				}else{		
					$(".menu").removeClass('mobile');
					$(".hamburger").removeClass('mobile');
					$(".cross").removeClass('mobile');					
				}
				if(bolChromeOS){			
					$(".menu").addClass('chromeOS');
					$(".hamburger").addClass('chromeOS');
					$(".cross").addClass('chromeOS');					
				}else{		
					$(".menu").removeClass('chromeOS');
					$(".hamburger").removeClass('chromeOS');
					$(".cross").removeClass('chromeOS');					
				}				
			}});			
			$('#menu').html('');
			$('#menu').css("width","0px");
			$('#hamburger').html('<button class="hamburger">&#9776;</button>');			
			$('#hamburger').show();			
			$('#cross').hide();	
			$('#cross').html('<button class="cross">&#735;</button>');				
		    $('#menutop').hide();
			$('#page').css('width',winWidth);
		}
		if(bolLeft){
			$.ajax({url: '/url/themes-gearshift/menu.php', success: function(theMenu){ 
				$('#menu').html(theMenu);
				addWiki('menu');
				$('#menu').css("width","25%");
				$('#hamburger').hide();	
				$('#cross').hide();
				$('#menutop').hide();
				$('#page').css('width','1020');
				if(bolMobile){			
					$(".menu").addClass('mobile');
				}else{		
					$(".menu").removeClass('mobile');
				}	
				if(bolChromeOS){			
					$(".menu").addClass('chromeOS');
				}else{		
					$(".menu").removeClass('chromeOS');
				}				
			}});			
		}
	lastWinWidth = winWidth;
	}
	});	
}

$(document).ready(function(){ 
    bolMobile = DeviceMeta.isMobileOrTablet();
	bolChromeOS = DeviceMeta.isChromeOS();
	$(window).resize(checkWidth)
	         .trigger('resize');
	if(bolMobile){
		$("body").addClass('mobile');
		$("form").addClass('mobile');
		$("input").addClass('mobile');
		$("select").addClass('mobile');
		$("textarea").addClass('mobile');
		$("label").addClass('mobile');
		$(".breadcrumbs").addClass('mobile');
		$("td.content").addClass('mobile');
		$("div.title").addClass('mobile');
		$("#logo").addClass('mobile');
		$("#marquee").addClass('mobile');
		$("td").addClass('mobile');
	}else{
		$("body").removeClass('mobile');
		$("form").removeClass('mobile');
		$("input").removeClass('mobile');
		$("select").removeClass('mobile');
		$("textarea").removeClass('mobile');
		$("label").removeClass('mobile');
		$(".breadcrumbs").removeClass('mobile');
		$("td.content").removeClass('mobile');
		$("div.title").removeClass('mobile');
		$("#logo").removeClass('mobile');
		$("#marquee").removeClass('mobile');
		$("td").removeClass('mobile');
		$(".hamburger").removeClass('mobile');
		$(".cross").removeClass('mobile');		
	}	
	if(bolChromeOS){
		$("body").addClass('chromeOS');
		$("form").addClass('chromeOS');
		$("input").addClass('chromeOS');
		$("select").addClass('chromeOS');
		$("textarea").addClass('chromeOS');
		$("label").addClass('chromeOS');
		$(".breadcrumbs").addClass('chromeOS');
		$("td.content").addClass('mobile');
		$("div.title").addClass('chromeOS');
		$("#logo").addClass('chromeOS');
		$("#marquee").addClass('chromeOS');
		$("td").addClass('chromeOS');
	}else{
		$("body").removeClass('chromeOS');
		$("form").removeClass('chromeOS');
		$("input").removeClass('chromeOS');
		$("select").removeClass('chromeOS');
		$("textarea").removeClass('chromeOS');
		$("label").removeClass('chromeOS');
		$(".breadcrumbs").removeClass('chromeOS');
		$("td.content").removeClass('chromeOS');
		$("div.title").removeClass('chromeOS');
		$("#logo").removeClass('chromeOS');
		$("#marquee").removeClass('chromeOS');
		$("td").removeClass('chromeOS');
		$(".hamburger").removeClass('chromeOS');
		$(".cross").removeClass('chromeOS');		
	}
	// Temp code to undo any class additions	
	$( "*" ).removeClass('mobile');
	$( "*" ).removeClass('chromeOS');
    bolMobile = false;
	bolChromeOS = false;	
	// menu toggling
	$('#hamburger').click(function() {
			$('#hamburger').hide();
			$('#cross').show();		
			$('#menutop').slideToggle( 'fast', function() {
			});
	});

	$('#cross').click(function() {
			$('#cross').hide();
			$('#hamburger').show();		
			$('#menutop').slideToggle( 'fast', function() {
			});
	});  
})




