jQuery(document).ready( function($) {
	$('.taxonomy-turbotabs_group tr.term-description-wrap').remove();
	$('.taxonomy-turbotabs_group tr.term-parent-wrap').remove(); 
	$('<span class="fa fa-folder" style="color: #787878; vertical-align: middle;padding-left: 5px;font-size: 1.1em;"></span>').insertAfter('#turbotabs_groupchecklist li .selectit');
	$('<span class="fa fa-trash-o"></span>').insertBefore('.post-type-turbotabs .submitdelete.deletion');
	$('<span class="fa fa-th-large"></span>').insertBefore('#turbotabs_groupdiv h3'); 
	var h2T = $('.post-type-turbotabs .wrap h2').text();
	$('.post-type-turbotabs .wrap h2').html( '<span class="fa fa-plus"></span>' + h2T);
	$('<span class="fa fa-hand-o-right"></span>').insertBefore('.post-type-turbotabs .postbox-container-1 #Icon h3.ui-sortable-handle span');
	$('.hide-dd').click(function(){
		$(this).parent().remove(); 
	});
	$('.post-type-turbotabs #pageparentdiv').remove();

	$('.taxonomy-turbotabs_group .preview-open').click(function(){
		$(this).find('.fa').toggleClass('fa-rotate-180');
	});

	$('.tt-assist .ast-hide').click(function(){
		$(this).closest('.tt-assist').animate({bottom: '-80px', opacity: 0},390);
	});

	var bulb = '';
	$('.qst').hover(
		function(){
			bulb = $(this).closest('.group').find('.question-bulb');
			bulb.show(400);
		},
		function(){
			bulb.hide(280);
		}
	);

	// check for number of selected groups
	var group = $('ul[data-wp-lists="list:turbotabs_group"]');
	var selGroups = 0;
	var added = $('p#turbotabs_group-add');

	group.find('li').each(function(){
		if( $(this).find('input').is(':checked') ){
			$(this).addClass('active');
		}
		selGroups = group.find('li.active').length;
		checked();
	}); 
    // count selected groups (from turbotab post type)
	group.on('change', 'li', function(){
		var check = $(this).find('input');
		if( check.is(':checked') ){
				$(this).addClass('active');
			} else {
				$(this).removeClass('active');
			} 
		selGroups = group.find('li.active').length;	
		checked();
	});
	// check for count of selected groups when new is added (which will be automaticly  selected)
	added.on('click', 'input#turbotabs_group-add-submit', function(){
		var current = group.find('li').length;
		var start = setInterval(function(){
			if( group.find('li').length > current ){
				clearInterval(start);
				group.find('li').first().addClass('active');
				$('<span class="fa fa-folder" style="color: #787878; vertical-align: middle;padding-left: 5px;font-size: 1.1em;"></span>').insertAfter('#turbotabs_groupchecklist li:first-child .selectit');
				selGroups = group.find('li.active').length;
				checked();
			}
		},150);
		start;
	});
	// helper function
	function checked(){
		
		if( selGroups > 1 ){ // if more than one selected - disable submiting
			alert('Sorry, only one group per tab is allowed. Your tab cannot belong to two groups (for duplicate content reasons and plugin functionality).' + ' Uncheck '+ ( selGroups - 1 ) +' of '+ selGroups + ' selected')
			$('.post-type-turbotabs #major-publishing-actions #publish').attr('disabled', 'disabled');
		} else if( selGroups === 0 || selGroups === 1 ){
			$('.post-type-turbotabs #major-publishing-actions #publish').removeAttr('disabled');
		}
	}

	var tabHeights = $('.options .prev-cont .prev').map(function() {
      return $(this).outerHeight(true);
      }).get();
      var maxHeight = Math.max.apply(null, tabHeights); 

	//initializing prevs
	var opCont =  $('.options .prev-cont'); 
	var prev = $('.options .prev-cont .prev');
	$('.prev-navs li:first-child, .tabs li:first-child').addClass('active');
	$('.prev-cont').find('.prev').first().addClass('active');
	$('.prev-cont').find('.prev').addClass('animated');
	$('.container').find('.tab').first().addClass('active');
	$('.prev-navs').on('click', 'li', function(e){
	  e.preventDefault();
	  if (!$(this).hasClass("active")) {
	      index = $(this).index();
	      $(this).siblings().removeClass("active");
	      $(this).addClass("active").parents('.options').find('.prev').eq(index).addClass('active bounceIn').siblings().removeClass("active bounceIn");
	  }
	});
	opCont.css({height: maxHeight});

	//preview setting up
	$('.preview-open').click(function(){
		$(this).parents().toggleClass('active');
	});

	// initialize spectrum
	$('input.color').spectrum({
		showAlpha: true,
		showInitial: true,
		showPalette: true,
		allowEmpty:true,
	    showSelectionPalette: true,
	    maxPaletteSize: 10,
		palette: [
        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
    	]
	});
}); 

// custom alert box
// source Source: http://jsfiddle.net/vivekkt91/bds77/

var ALERT_TITLE = "Oops!";
var ALERT_BUTTON_TEXT = "Ok";

if(document.getElementById) {
	window.alert = function(txt) {
		createCustomAlert(txt);
	}
}

function createCustomAlert(txt) {
	d = document;

	if(d.getElementById("modalContainer")) return;

	mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
	mObj.id = "modalContainer";
	mObj.style.height = d.documentElement.scrollHeight + "px";
	
	alertObj = mObj.appendChild(d.createElement("div"));
	alertObj.id = "alertBox";
	if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
	alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";
	alertObj.style.visiblity="visible";

	h1 = alertObj.appendChild(d.createElement("h1"));
	h1.appendChild(d.createTextNode(ALERT_TITLE));

	msg = alertObj.appendChild(d.createElement("p"));
	//msg.appendChild(d.createTextNode(txt));
	msg.innerHTML = txt;

	btn = alertObj.appendChild(d.createElement("a"));
	btn.id = "closeBtn";
	btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
	btn.href = "#";
	btn.focus();
	btn.onclick = function() { removeCustomAlert();return false; }

	alertObj.style.display = "block";
	
}

function removeCustomAlert() {
	document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
}
function ful(){
	alert('text');
}

jQuery(window).load(function(){
	setTimeout(function(){
		jQuery('.tt-assist').fadeIn(200).animate({bottom: '0'},560).delay(360);
	},6700);
});