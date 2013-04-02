$(document).ready(
	function()
	{
		$('.profileright .catrow').not('.noSlide')
		.prepend('<span class="arrow"></span>')
		.click(
			function()
			{				
				$(this).next().slideToggle('slow');
			}
		);
		// Allow items marked with the class .noSlide to not be affected
		$('.profileright .catrow').not(':first-child, .noSlide').next().hide();
	}
);