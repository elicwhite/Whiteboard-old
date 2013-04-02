<table class="info">
	<tr>
		<td class="info">
			<strong>Username</strong>
		</td>
		<td class="input">
			<?php echo $this->USERINFO->username?>
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Display Name</strong>
		</td>
		<td class="input">
			<?php
				$display = "";
				if ($this->USERINFO->displayname != "")
					$display = $this->USERINFO->displayname;
			?>
			<input type="text" name="displayname" value="<?php echo $display?>" />
		</td>
	</tr>

	<tr id="pass">
		<td class="info">
			<strong>Password</strong>
		</td>
		<td class="input">
			<?php
				$pass = str_repeat("*", 7);
			echo $pass;?> - <a id="change_pass">Change Password</a>
		</td>
	</tr>
	<tr id="oldpass">
		<td class="info">
			<strong>Old Password</strong>
		</td>
		<td class="input">
			<input type="password" name="old_password" value="" />
		</td>
	</tr>
	<tr id="newpass">
		<td class="info">
			<strong>New Password</strong>
		</td>
		<td class="input">
			<input type="password" name="new_password" value="" />
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>E-mail</strong>
		</td>
		<td class="input">
			<?php
				$email = "";
				if ($this->USERINFO->email != "")
					$email = $this->USERINFO->email;
			?>
			<input type="text" name="email" value="<?php echo $email?>" />
		</td>
	</tr>
	<tr id="avatar">
		<td class="info">
			<strong>Avatar</strong>
		</td>
		<td class="input">
			<?php
				/*
				We need to check if we have an avatar, 
				if we do have an avatar, lets display it, otherwise tell the user
				that he doesn't have one, so that he may pick one
				*/
				
				$hasimage = false;
				if ($this->USERINFO->avatar != "")
				{
					$id = intval($this->USERINFO->avatar);
					$query = $GLOBALS['super']->db->query("SELECT * FROM ".TBL_PREFIX."images WHERE `id`=".$id);
					$result = $GLOBALS['super']->db->fetch_assoc($query);
					$hasimage = true;
				}
				if ($hasimage)
				{
					?>
					<img src="<?php echo $result['url']?>" alt="<?php echo $result['name']?>" />
					<br />
					<?php
				}
				else 
				{
					echo "No Avatar - ";
				}
				
			?>
			<a id="change_avatar">Change</a>
		</td>
	</tr>
	<tr id="newavatar">
		<td class="info">
			<strong>New Avatar</strong>
		</td>
		<td class="input">
			<input type="file" name="new_avatar" value="" />
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Signature</strong><br />
		</td>
		<td class="input">
			<textarea name="signature" cols="30" rows="4"><?php echo $this->USERINFO->signature?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="blue"><input type="submit" class="submit" name="submit" value="Submit" /></td>
	</tr>
</table>