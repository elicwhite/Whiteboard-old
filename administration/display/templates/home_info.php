<div class="mainheaderbox2">
	<strong><?php echo $this->HOME_INFO_TEXT?></strong>
	<br />
	<?php echo $this->HOME_MESSAGE?>
</div>

<div class="centerbox">
	<div class="left_section">
		<div class="section_header">Version</div>
		<ul class="infoboxes">
			<?php
			foreach($this->VERSION as $version)
			{
			?>
				<li><?php echo $version?></li>
			<?php
			}
			?>
		</ul>
	</div>
	<div class="middle_section">
		<div class="section_header">Server</div>
		<ul class="infoboxes">
			<?php
			foreach($this->SERVER as $server)
			{
			?>
				<li><?php echo $server?></li>
			<?php
			}
			?>
		</ul>
	</div>
	<div class="right_section">
		<div class="section_header">Database</div>
		<ul class="infoboxes">
			<?php
			foreach($this->DATABASE as $db)
			{
			?>
				<li><?php echo $db?></li>
			<?php
			}
			?>
		</ul>
	</div>
	<div class="clearer"></div>
</div>