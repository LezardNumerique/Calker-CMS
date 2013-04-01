<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_medias');?></h2>
	<?php if($this->user->root):?>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/create')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php endif;?>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable"><?php echo $notice;?></p>
	<?php endif;?>
	<?php if(isset($themes) && count($themes) > 1) : ?>
	<div class="pager">
		<div class="pager_left"></div>
		<div class="pager_right">
			<?php echo form_open($this->uri->uri_string(), array('class' => 'search'));?>				
				<select id="filter_theme" name="filter_theme" class="input_select">
					<option value="-1"><?php echo $this->lang->line('option_filter_theme');?></option>
					<?php if(isset($themes) && $themes):?>					
					<?php foreach($themes as $key => $theme):?>
					<option value="<?php echo $theme;?>"<?php if($this->session->userdata('filter_theme') == $theme):?>selected="selected"<?php endif;?>><?php echo ucfirst($theme);?></option>
					<?php endforeach;?>
					<?php endif;?>
				</select>
				<input type="submit" value="<?php echo $this->lang->line('label_filter');?>" class="input_submit"/></td>				
			</form>			
		</div>
	</div>
	<?php endif;?>
	<?php if(isset($medias_types) && $medias_types) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers: {7:{sorter: false}},
		});		
		$('a.tooltip').tooltip({
			track: true,
			delay: 0,
			fixPNG: true,
			showURL: false,
			showBody: " - ",
			top: -35,
			left: 5
		});
	});
	</script>	
	<table class="table_list" id="table_sort">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="12%"><?php echo $this->lang->line('td_name');?></th>
				<th width="12%"><?php echo $this->lang->line('td_key');?></th>
				<th width="12%"><?php echo $this->lang->line('td_module');?></th>
				<th width="12%"><?php echo $this->lang->line('td_theme');?></th>
				<th width="12%"><?php echo $this->lang->line('td_width');?></th>
				<th width="12%"><?php echo $this->lang->line('td_height');?></th>
				<?php if($this->user->root):?><th width="15%" colspan="2" class="center"><?php echo $this->lang->line('td_action');?></th><?php endif;?>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;$count_medias_types = count($medias_types);?>
			<?php foreach ($medias_types as $media_type): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td class="center"><?php echo $i;?></td>
				<td><?php echo $media_type['name'];?></td>
				<td><?php echo $media_type['key'];?></td>
				<td><?php echo ucfirst($media_type['module']);?></td>
				<td><?php echo ucfirst($media_type['theme']);?></td>
				<td><?php echo $media_type['width'];?> px</td>
				<td><?php echo $media_type['height'];?> px</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/edit/'.$media_type['medias_types_id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/delete/'.$media_type['medias_types_id'])?>" title="<?php echo $this->lang->line('btn_delete');?>"  class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++; endforeach;?>
		</tbody>
	</table>
	<?php else :?>
	<p class="no_data"><?php echo $this->lang->line('text_no_media');?></p>
	<?php endif; ?>
</div>
<!-- [Main] end -->