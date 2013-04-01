<?php if(isset($filter) && $filter):?>
<?php echo form_open($this->uri->uri_string(), array('class' => 'search'));?>
	<fieldset>
		<table class="table_search">
			<tr>
				<td><label for="filter_categories"><?php echo $this->lang->line('label_filter_categories');?></label></td>
				<td>
					<select id="filter_categories" name="filter_categories" class="input_select">
						<option value="-1"></option>
						<?php if(isset($categories) && $categories):?>
						<?php foreach($categories as $categorie):?>
						<option value="<?php echo $categorie['id'];?>"<?php if($this->session->userdata('filter_categories') == $categorie['id']):?>selected="selected"<?php endif;?>><?php echo ($categorie['level'] > 0) ? "|".str_repeat("__", $categorie['level']): "";?> <?php echo html_entity_decode($categorie['title']);?></option>
						<?php endforeach;?>
						<?php endif;?>
					</select>
				</td>
				<td><label for="filter_search"><?php echo $this->lang->line('label_filter_keywords');?></label></td>
				<td><input type="text" name="filter_search" id="filter_search" value="<?php echo $this->session->userdata('filter_search');?>" class="input_text"/></td>
				<td><label for="filter_or"><?php echo $this->lang->line('label_filter_or');?></label></td>
				<td><input type="checkbox" name="filter_or" id="filter_or" value="1" class="input_text"<?php if($this->session->userdata('filter_or') == 'or_like') :?>checked="checked"<?php endif;?>/></td>
				<td><input type="submit" value="Rechercher" class="input_submit"/></td>
			</tr>
		</table>
	</fieldset>
</form>
<?php endif;?>