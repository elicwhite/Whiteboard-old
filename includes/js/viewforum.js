$(document).ready(
	function()
	{
		$(".topic_rows .forum_main a").each
		(
			function()
			{
				var self = $(this)
				self.height(self.height());
				self.hoverIntent
				(
					function()
					{
						self.find("span.topicname").stop(true,true).fadeOut
						(
							"fast",
							function()
							{
								self.find("span.preview").fadeIn("fast");
							}
						);
						
					}, 
					function()
					{
						self.find("span.preview").stop(true,true).fadeOut
						(
							"fast",
							function()
							{
								self.find("span.topicname").fadeIn("fast");
							}
						);
					}
				);
			}
		)

	}
);
