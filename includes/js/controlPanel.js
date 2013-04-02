$(document).ready(
	function()
	{
		$('#oldpass').hide();
		$('#newpass').hide();
		
		$('#change_pass')
		.click(
			function()
			{	
				$('#pass').hide();			
				$('#oldpass').show();
				$('#newpass').show();
			}
		);
		
		$('#newavatar').hide();
		
		$('#change_avatar')
		.click(
			function()
			{	
				$('#newavatar').show();
			}
		);
	}
);