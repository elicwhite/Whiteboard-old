// Redirects to a specific url after 5 seconds.
function redir(url)
{
	setTimeout(function()
    	{
    		window.location = url;
    	}, 1000);
}

// Make a fadeToggle() action for jquery
jQuery.fn.fadeToggle = function(speed, easing, callback) {
   return this.animate({opacity: 'toggle'}, speed, easing, callback);

}; 

$(document).ready(
	function()
	{
		// Go to the top of the page button
		$('a#gotop').click(
			function(event)
			{
				event.preventDefault();
				$('html').animate({scrollTop:0}, 'slow'); 
			}
		);
		
		
		// Buttons for the users on this page section.
		$('#showlessonline').show();
		$('#usersonpage').hide();
		$('a#numusersonpage').click(
			function(event)
			{
				$('#shortonline').fadeToggle("slow");
			}
		)
		$('a#showmoreonline').click(
			function(event)
			{
				$(this).parent().fadeToggle("slow");
				$('#usersonpage').show("slow");
			}
		)
		$('#showlessonline').click(
			function(event)
			{
				$(this).parent().hide("slow");
				$('#shortonline').fadeToggle("slow");
			}
		)
		
		
		
		/*
		$('#searchbar').hide();
		
		$('#search').mouseenter(
			function()
			{				
				$('#searchbar').slideDown('slow');
				$('input.searchbox').focus();
			}
		);
		
		$('input.searchbox').blur(
			function()
			{
				$('#searchbar').slideUp();
			}
		);
		*/
	}
);