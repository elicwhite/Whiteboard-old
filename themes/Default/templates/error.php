<div class="error">
	<?php 
	if (is_array($this->error_message))
	{
		?>
		<ul>
			<?php
			foreach($this->error_message as $error)
			{
				?>
				<li><?php echo $error?></li>
				<?php
			}
			?>
		</ul>
		<?php
	}
	else
		echo $this->error_message
	?>
</div>