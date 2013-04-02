<div class="mainheaderbox2">
	Choose a forum to edit.
</div>

<div class="catrow">Choose a Forum:</div>
<div class="contentbox">
	<form action="" method="get" class="forumedit">
		<div style="padding-top: 10px;">
			<input type="hidden" name="act" value="forums"/>
		    <input type="hidden" name="sub" value="edit"/>
			<select name="fid" class="select">
				<?php
				foreach($this->FORUM_LIST as $forum)
				{
				?>
				<option value="<?php echo $forum['id']?>"><?php echo $forum['name']?></option>
				<?php
				}
				?>
			</select>
			<input type="submit" class="submit" value="Edit" />
		</div>
	</form>
</div>