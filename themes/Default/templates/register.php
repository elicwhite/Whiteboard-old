<div id="registerbox">
	<div class="catrow">Registration Information</div>
	<div class="contentbox">
		<p>
			By registering you have access to many features that would be otherwise unavaliable.
			You will have the ability to take part in discussions, modify a user profile that other members will be able to see,
			and have the ability to watch discussions that are important to you for updates.
		</p>
		<p>
			Fill in the form below and then head over to your new profile and look around.
			There are many more settings than what is asked in the registration form below.
		</p>
	</div>



	<div class="catrow">Register</div>
	<div class="contentbox">
		<form action="?act=register&amp;do=register" method="post">
			
			<table class="info">
				<tr>
					<td class="info">
						<strong>Username</strong><br />
						The name you will log in with
					</td>
					<td class="input">
						<input type="hidden" name="formsent" value="1" />
						<input class="text" type="text" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : "" ?>" name="username" />
					</td>
				</tr>
				<tr>
					<td class="info">
						<strong>Displayname</strong><br />
						Your alias on the board (this can be changed later)
					</td>
					<td class="input">
						<input type="hidden" name="formsent" value="1" />
						<input class="text" type="text" value="<?php echo (isset($_POST['displayname'])) ? $_POST['displayname'] : "" ?>" name="displayname" />
					</td>
				</tr>
				<tr>
					<td class="info">
						<strong>Password</strong>
					</td>
					<td class="input">
						<input class="text" type="password" value="" name="password" /><br />
					</td>
				</tr>
				<tr>
					<td class="info">
						<strong>Password Again</strong>
					</td>
					<td class="input">
						<input class="text" type="password" value="" name="password2" /><br />
					</td>
				</tr>
				<tr>
					<td class="seperator" colspan="2"></td>
				</tr>
				<tr>
					<td class="info">
						<strong>E-Mail</strong>
					</td>
					<td class="input">
						<input class="text" type="text" value="<?php echo (isset($_POST['email'])) ? $_POST['email'] : "" ?>" name="email" size="20" /><br />
					</td>
				</tr>
				<tr>
					<td class="info">
						<strong>Captcha</strong>
					</td>
					<td class="input">
						<p>
						<script type="text/javascript">
						var RecaptchaOptions = {
						   theme : 'white'
						};
						</script>
						<?php echo $this->CAPTCHAHTML?>
						</p>
					</td>
				</tr>
				<tr>
					<td class="info center" colspan="2">
						<input type="submit" name="submit" value="Submit" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>