$(document).ready(function() {

	function findValueCallback(event, data, formatted) {
		$.fancybox.close();
		id = formatted.split("|") ;
		// $("<li>").html( !data ? "No match!" : "Selected: " + id[0]).appendTo("#node_search_results");
		$("#select_entry select option[value='" + id[0] + "']").attr("selected","selected");
		// alert(id[0]);
	}

	
	jQuery.fn.fadeToggle = function(speed, easing, callback) {
   return this.animate({opacity: 'toggle'}, speed, easing, callback);
	};

	
	
	$("#edit_nodes a.fancypants").livequery('click', function() { 
	
		$.fancybox.showActivity();
		var url = $(this).attr("href");
		
		$.getJSON(url,
        function(data){
          	// alert(data.some_message);
            $("#edit_nodes").html(data.data);
            $.fancybox.hideActivity();
         
        });
		
		return false;
			// alert('foo');

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
	
	$("#edit_nodes a.delete_node").livequery('click', function() { 
   			var url = $(this).attr("href");
   			var answer = confirm("Are you sure you want to delete this node?")
		    if (answer){
		        $.fancybox.showActivity();
				var url = $(this).attr("href");
				
				$.getJSON(url,
		        function(data){
		          	// alert(data.some_message);
		            $("#edit_nodes").html(data.data);
		            $.fancybox.hideActivity();
		         
		        });
		    }	
			return false;
	});	
	
	$("#edit_nodes a.delete_nodes").livequery('click', function() { 
   			var url = $(this).attr("href");
   			var answer = confirm("Are you sure you want to delete this node and all it's children?")
		    if (answer){
		        $.fancybox.showActivity();
				var url = $(this).attr("href");
				
				$.getJSON(url,
		        function(data){
		          	// alert(data.some_message);
		            $("#edit_nodes").html(data.data);
		            $.fancybox.hideActivity();
		         
		        });
		    }	
			return false;
	});	
	


});

	   	

