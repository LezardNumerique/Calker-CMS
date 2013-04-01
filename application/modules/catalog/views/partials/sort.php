<div class="sort">
	<form action="" method="post">
		<div>
			<select onchange="javascript:this.form.submit();" id="sort" name="sort" class="input_select" >
				<option value="pID|desc"><?php echo $this->lang->line('option_filter_by');?></option>
				<option value="price|asc"<?php if($this->session->userdata('sort') == 'price|asc'):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_filter_price_asc');?></option>
				<option value="price|desc"<?php if($this->session->userdata('sort') == 'price|desc'):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_filter_price_desc');?></option>
				<option value="title|asc"<?php if($this->session->userdata('sort') == 'title|asc'):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_filter_title_asc');?></option>
				<option value="title|desc"<?php if($this->session->userdata('sort') == 'title|desc'):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_filter_title_desc');?></option>
				<option value="products.date_added|asc"<?php if($this->session->userdata('sort') == 'date_added|asc'):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_filter_date_added_asc');?></option>
				<option value="products.date_added|desc"<?php if($this->session->userdata('sort') == 'date_added|desc'):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_filter_date_added_desc');?></option>
			</select>
		</div>
	</form>
</div>
