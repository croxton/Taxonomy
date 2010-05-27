$(document).ready(function() {

	function findValueCallback(event, data, formatted) {
		$.fancybox.close();
		id = formatted.split("|") ;
		// $("<li>").html( !data ? "No match!" : "Selected: " + id[0]).appendTo("#node_search_results");
		$("#select_entry select option[value='" + id[0] + "']").attr("selected","selected");
	}

	
	jQuery.fn.fadeToggle = function(speed, easing, callback) {
   return this.animate({opacity: 'toggle'}, speed, easing, callback);
	};

	
	
	$("#edit_nodes a.fancypants").livequery('click', function() { 
	
		$.fancybox.showActivity({
    		'overlayOpacity'    :  0.2,
    		'overlayColor'      : '#000' 
		});

			// alert('foo');
   			var url = $(this).attr("href")+" #edit_nodes";
				$("#edit_nodes").load(url, function () {
				    // this is the callback function, called after the load is finished.
				    $.fancybox.hideActivity();
				});
				return false;
	});	
	
	$("th.create_node").livequery('click', function() { 
		$(this).parent().parent().next().toggle();
    });

    $("#search_for_nodes").livequery(function() { 
   		 $(this).fancybox({
	   		 onComplete	:	function() {
	             $("input#autocomplete_entries").val('').focus();  
			},
			'titleShow'			: false,
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'elastic',
			'easingIn'			: 'swing',
			'easingOut'			: 'swing',
			'overlayShow'       :  true,
    		'overlayOpacity'    :  0.7,
    		'overlayColor'      : '#000' 
		});	
		
		
		
		$("input#autocomplete_entries").autocomplete(entries, {
			minChars: 0,
			width: 310,
			matchContains: true,
			autoFill: false,
			formatItem: function(row, i, max) {
				return row.entry;
			},
			formatMatch: function(row, i, max) {
				return '' + row.id + '|' + row.entry;
			},
			formatResult: function(row) {
				return row.entry;
			}
		});
		
		$("#node_search input").result(findValueCallback).next().click(function() {
			$(this).prev().search();
		});
		
		
	});
	
	$(".add_tab_link").click(function(){
	
		return false;
	});


});

	   	

