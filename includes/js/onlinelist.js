$(document).ready
(
	function()
	{
		updateList();
	}
);

function updateList()
{
	$.get
	(
		"includes/xml/online.php",
		function(xml)
		{
			// Update the user counts
			$("#guestCount").html
			(
				$("guestCount",xml).text()
			)
			$("#userCount").html
			(
				$("userCount",xml).text()
			)
			
			// Update the rows
			$(".onlinetable tbody").empty();
			
			$("user",xml).each
			(
				function(id)
				{
					user = $("user",xml).get(id);
					$(".onlinetable tbody").append
					(
						'<tr>'+
							'<td class="lefttext">'+
									$("name",user).text()+
							'</td>'+
							'<td>'+
									$("page",user).text()+
							'</td>'+
							'<td>'+
									$("time",user).text()+
							'</td>'+
						'</tr>'
					);
				}
			);
			
			$(".onlinetable tbody tr:odd").addClass("even");
		}
	);	
	setTimeout('updateList()', 15 * 1000);
}