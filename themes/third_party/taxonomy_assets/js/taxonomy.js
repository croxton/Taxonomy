$(document).ready(function() {


	$("a.delete_tree_confirm, a.delete_node, a.delete_branch").click(function() { 
   			var answer = confirm("Are you sure you want to delete?")
		    if (!answer){
		       return false;
		    }
	});	
	
	$('.taxonomy-select-entry select').live('change', function() {
		
		$(this).detectPageURI();
	});
	
		$('input#use_page_uri').change(function () {
	    if ($(this).attr("checked")) {
	        //do the stuff that you would do when 'checked'
			// alert('checked');
			$('#custom_url').hide().val('[page_uri]');
			$('#taxonomy_select_template').hide();
	        return;
	    }
	    //Here do the stuff you want to do when 'unchecked'
	    //alert('unchecked');
	    $('#taxonomy_select_template').show();
	    $('#custom_url').show().val('');
	});
	
	$("input#use_page_uri:checked").each( 
	    function() { 
	    	$('#taxonomy_us_page_uri div').show();
	       	$('#taxonomy_select_template').hide();
	       	$('#custom_url').hide();
		} 
	);
	
	
	
});