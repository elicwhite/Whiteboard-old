$(document).ready(

	function()
	{
		$(".topic_rows .forum_main a").each
		(
			function()
			{
				var self = $(this)
				//$(this).tooltip();

				self.tooltip
				(
					{
						position: "center left",
						opacity: 0.85,
						effect: 'slide',
						direction: 'left',
						bounce: true
						//tipClass: "preview"
					}
				).dynamic
				(
					{
						right:
						{
							direction: 'right',
							bounce: true
						}
					}
				);

				/*
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
				*/
			}
		)

	}
);
