<ul class="forum_top_links">
	<li><span id="userCount"><?php echo $this->usercount?></span> Users Online</li>
	<li><span id="guestCount"><?php echo $this->guestcount?></span> Guests Online</li>
</ul>
<div class="foruminfo_box">Online List</div>
<table cellspacing="0" class="onlinetable">
	<thead>
		<tr id="header">
			<th class="lefttext">
				User
			</th>
			<th>
				Current Page
			</th>
			<th>
				Time
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
			
		foreach($this->online as $index=>$user)
		{
			$stripe = false;
			if ($index%2 == 1)
				$stripe = true;
			?>	
		<tr <?php echo ($stripe) ? 'class="even"' : ""?>>	
			<td class="lefttext">
					<?php echo $user["name"] ?>
			</td>
			<td>
					<?php echo $user["page"] ?>
			</td>
			<td>
					<?php echo $user["time"] ?>
			</td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>