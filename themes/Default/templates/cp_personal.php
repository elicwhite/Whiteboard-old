<table class="info">
	<tr>
		<td class="info">
			<strong>First Name</strong>
		</td>
		<td class="input">
			<input type="hidden" name="post" value="form" />
			<input type="text" value="<?php echo $this->USERINFO->firstname?>" name="fname" />
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Last name</strong>
		</td>
		<td class="input">
			<input type="text" value="<?php echo $this->USERINFO->lastname?>" name="lname" />
		</td>
	</tr>

	<tr>
		<td class="info">
			<strong>Gender</strong>
		</td>
		<td class="input">
			Male: <input type="radio" name="gender" value="m" <?php echo ($this->USERINFO->gender == "m" ? 'checked="checked" ' : '')?> />
			Female: <input type="radio" name="gender" value="f" <?php echo ($this->USERINFO->gender == "f" ? 'checked="checked" ' : '')?> />

		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Location</strong>
		</td>
		<td class="input">
			<input type="text" name="country" value="<?php echo $this->USERINFO->country?>" />
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Birth Date</strong>
		</td>
		<td class="input">
			Month: 
			<select name="birth_month">
				<option value="1" <?php echo ($this->USERINFO->birth_month == "1" ? 'selected="selected" ' : '')?>>January</option>
				<option value="2" <?php echo ($this->USERINFO->birth_month == "2" ? 'selected="selected" ' : '')?>>February</option>
				<option value="3" <?php echo ($this->USERINFO->birth_month == "3" ? 'selected="selected" ' : '')?>>March</option>
				<option value="4" <?php echo ($this->USERINFO->birth_month == "4" ? 'selected="selected" ' : '')?>>April</option>
				<option value="5" <?php echo ($this->USERINFO->birth_month == "5" ? 'selected="selected" ' : '')?>>May</option>
				<option value="6" <?php echo ($this->USERINFO->birth_month == "6" ? 'selected="selected" ' : '')?>>June</option>
				<option value="7" <?php echo ($this->USERINFO->birth_month == "7" ? 'selected="selected" ' : '')?>>July</option>
				<option value="8" <?php echo ($this->USERINFO->birth_month == "8" ? 'selected="selected" ' : '')?>>August</option>
				<option value="9" <?php echo ($this->USERINFO->birth_month == "9" ? 'selected="selected" ' : '')?>>September</option>
				<option value="10" <?php echo ($this->USERINFO->birth_month == "10" ? 'selected="selected" ' : '')?>>October</option>
				<option value="11" <?php echo ($this->USERINFO->birth_month == "11" ? 'selected="selected" ' : '')?>>November</option>
				<option value="12" <?php echo ($this->USERINFO->birth_month == "12" ? 'selected="selected" ' : '')?>>December</option>
			</select>
			Day:
			<select name="birth_day">
				<?php
				for($i = 1; $i <= 31; $i++)
				{
					$selected = ($this->USERINFO->birth_day == $i ? 'selected="selected" ' : '');
					echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				}
				?>
			</select>
			Year:
			<select name="birth_year">
				<?php
				// Loop through all the years from the current year back to 1930.
				for($i = intval(date("Y")); $i >= 1930; $i--)
				{
					$selected = ($this->USERINFO->birth_year == $i ? 'selected="selected" ' : '');
					echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="blue"><input type="submit" class="submit" name="submit" value="Submit" /></td>
	</tr>
</table>