<table class="info">
	<tr>
		<td class="info">
			<strong>Show Online</strong>
		</td>
		<td class="input">
			<input type="hidden" name="post" value="form" />
			Yes: <input type="radio" name="show_online" value="1" <?php echo ($this->USERINFO->show_online == 1 ? 'checked="checked" ' : '')?> />
			No: <input type="radio" name="show_online" value="0" <?php echo ($this->USERINFO->show_online == 0 ? 'checked="checked" ' : '')?>/>

		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Send Digests</strong>
		</td>
		<td class="input">
			Yes: <input type="radio" name="send_digests" value="1" <?php echo ($this->USERINFO->send_digests == 1 ? 'checked="checked" ' : '')?>/>
			No: <input type="radio" name="send_digests" value="0" <?php echo ($this->USERINFO->send_digests == 0 ? 'checked="checked" ' : '')?>/>

		</td>
	</tr>
	<tr>
		<td colspan="2" class="blue"><input type="submit" class="submit" name="submit" value="Submit" /></td>
	</tr>
</table>