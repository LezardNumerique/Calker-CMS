<script type="text/javascript">
$(function() {
	$("#table_sort").tablesorter();
	$('textarea').autosize();
});
</script>
<table class="table_list translation" id="table_sort">
	<thead>
		<tr>
			<th width="30%"><?php echo $this->lang->line('td_label');?></th>
			<th width="70%"><?php echo $this->lang->line('td_translation');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 1;foreach($rows as $key => $value):?>
		<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
		<tr class="<?php echo $rowClass?>">
			<td>
				<?php if($key == '1a') echo '<input type="text" name="attr_1a" id="attr_1a" class="input_text" value=""/>';else echo '<label for="'.$key.'">'.$key.'</label>';?>
			</td>
			<td>				
				<textarea name="<?php echo $key;?>" id="<?php echo $key;?>" class="input_textarea"><?php echo $value;?></textarea>
				<a href="javascript:void(0);" title="<?php echo $this->lang->line('btn_delete')?>" onclick="javascript:delete_translations('<?php echo $key;?>');"><img src="<?php echo site_url(APPPATH.'/views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				<span style="display:none;"><?php echo $value;?></span>
			</td>
		</tr>
		<?php $i++;endforeach;?>
	</tbody>
</table>
<script type="text/javascript">
attr_translations();
</script>