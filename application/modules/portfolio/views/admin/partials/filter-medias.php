<?php if(isset($filter) && $filter):?>
<form name="" id="" action="<?php echo site_url($this->uri->uri_string()) ?>" method="post" class="search">
	<fieldset>
		<table class="table_search">
			<tr>
				<td><label for="filter_search"><?php echo $this->lang->line('label_filter_keywords');?></label></td>
				<td><input type="text" name="filter_search" id="filter_search" value="<?php echo $this->session->userdata('filter_search');?>" class="input_text"/></td>
				<td><input type="submit" value="<?php echo $this->lang->line('input_search');?>" class="input_submit"/></td>
			</tr>
		</table>
	</fieldset>
</form>
<?php endif;?>
