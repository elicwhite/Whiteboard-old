$(document).ready
(
	function()
	{
		$("input[name='is_cat']").change(
			function()
			{
				// For some reason you can't do an if / else, you have to do two ifs. Don't know why.
				if ($("input[name='is_cat']:checked").val() == "1")
				{
					$("select[name=parent_id]").attr("disabled", "disabled");
				}
				else
				{
					$("select[name=parent_id]").removeAttr("disabled");
				}
			}
		).change();
	}
);

