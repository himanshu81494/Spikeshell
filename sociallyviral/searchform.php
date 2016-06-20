<form method="get" id="searchform" class="search-form" action="<?php echo home_url(); ?>" _lpchecked="1">
	<fieldset>
		<input type="text" name="s" id="s" value="<?php _e('What are you looking for?','sociallyviral'); ?>" onfocus="(this.value == '<?php _e('What are you looking for?','sociallyviral'); ?>') && (this.value = '')" onblur="(this.value == '') && (this.value = '<?php _e('What are you looking for?','sociallyviral'); ?>')" />
		<!-- <input id="search-image" class="sbutton" type="submit" value=""><i class="demo-icon icon-search"></i> -->
		<button id="search-image" class="sbutton" type="submit"><i class="demo-icon icon-search"></i></button>
		
	</fieldset>
</form>