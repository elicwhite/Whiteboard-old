<div class="mainheaderbox2">
	This form will help you create, and edit forums.
</div>
<form action="" method="post" class="forumedit">
	<div class="catrow"><?php echo $this->title?></div>
	<div class="contentbox">
		
		<table class="info">
			<tr>
				<td class="info">
					<strong>Name</strong>
				</td>
				<td class="input">
					<?php secureForm("addEditForum"); ?>
					<input type="hidden" name="post" value="form" />
					<input class="text" type="text" value="<?php echo $this->name?>" name="forum_name" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Description</strong>
				</td>
				<td class="input">
					<textarea class="textarea" name="forum_description" cols="30" rows="4"><?php echo $this->description?></textarea>
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Active</strong>
				</td>
				<td class="input">
					Yes: <input type="radio" name="active" value="1" <?php if ($this->active == 1) echo 'checked="checked" '?>/>
					No: <input type="radio" name="active" value="0" <?php if ($this->active != 1) echo 'checked="checked" '?>/>

				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Category</strong>
				</td>
				<td class="input">
					Yes: <input id="catbutton" type="radio" name="is_cat" value="1" <?php if ($this->is_cat == 1) echo 'checked="checked" '?>/>
					No: <input type="radio" name="is_cat" value="0" <?php if ($this->is_cat == 0) echo 'checked="checked" '?>/>
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Redirect</strong>
				</td>
				<td class="input">
					Yes: <input type="radio" name="redirect" value="1" <?php if ($this->redirect == 1) echo 'checked="checked" '?>/>
					No: <input type="radio" name="redirect" value="0" <?php if ($this->redirect == 0) echo 'checked="checked" '?>/>

				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Redirect URL</strong><br />
					ex: http://www.google.com
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->url?>" name="redirecturl" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Parent Forum</strong><br />
					If the forum is not a category, you can choose what forum you want to be a child of.
				</td>
				<td class="input">
					<select name="parent_id" class="select">
						<?php 
						foreach($this->PARENT_LIST as $parent)
						{
						?>
						<option value="<?php echo $parent['id']?>" <?php if ($parent['selected']) echo 'selected="selected"'?>><?php echo $parent['name']?></option>
						<?php
						}
						?>
					</select>

				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Theme</strong><br />
					The theme you want this forum and it's sub forums to use.
				</td>
				<td class="input">
					<select name="theme" class="select">
						<?php
						foreach($this->THEME_LIST as $theme)
						{
							print_r($theme);
						?>
						<option value="<?php echo $theme['id']?>"><?php echo $theme['displayname']?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
		</table>
			
		<div class="clearer"></div>
	</div>
	
	<div class="catrow">Set Permissions</div>
	<div class="contentbox">
		<table class="permissions" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th scope="col">Group Name</th>
					<th scope="col">View</th>
					<th scope="col">New Topic</th>
					<th scope="col">Post Reply</th>
					<th scope="col">Edit Self</th>
					<th scope="col">Edit Others</th>
					<th scope="col">Delete Self</th>
					<th scope="col">Delete Others</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				foreach($this->groups as $group)
				{
					//print_r($group);
				
					//echo (isset($group['View']) && $group['View']) ? 'yay' : "nay";
					
					?>
				<tr>
					<td><?php echo $group['name']?></td>
					<td><input <?php echo (isset($group['View']) && $group['View']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][View]" /></td>
					<td><input <?php echo (isset($group['NewTopic']) && $group['NewTopic']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][NewTopic]" /></td>
					<td><input <?php echo (isset($group['Reply']) && $group['Reply']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][Reply]" /></td>
					<td><input <?php echo (isset($group['EditSelf']) && $group['EditSelf']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][EditSelf]" /></td>
					<td><input <?php echo (isset($group['EditOthers']) && $group['EditOthers']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][EditOthers]" /></td>
					<td><input <?php echo (isset($group['DeleteSelf']) && $group['DeleteSelf']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][DeleteSelf]" /></td>
					<td><input <?php echo (isset($group['DeleteOthers']) && $group['DeleteOthers']) ? 'checked="checked" ' : ""; ?>type="checkbox" name="perm[<?php echo $group['id']?>][DeleteOthers]" /></td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>

	<p class="submit">
		<input type="submit" class="submit" name="submit" value="Submit" />
	</p>
</form>