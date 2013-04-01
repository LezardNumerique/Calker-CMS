<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_admin_contact');?></h2>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<div class="pager">
		<div class="pager_left"></div>
		<div class="pager_right">
			<?php echo form_open($this->uri->uri_string(), array('class' => 'search'));?>
				<div>	
					<input type="text" class="input_text" name="filter" value="<?php echo $this->input->post('filter');?>"/>				
					<input type="submit" class="input_submit" name="submit" value="<?php echo $this->lang->line('btn_search');?>"/>
				</div>
			</form>
		</div>
	</div>
	<?php if(isset($contacts) && $contacts) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers: {6:{sorter: false}},
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
				<th width="15%"><?php echo $this->lang->line('td_firstname');?></th>
				<th width="15%"><?php echo $this->lang->line('td_lastname');?></th>
				<th width="15%"><?php echo $this->lang->line('td_email');?></th>
				<th width="15%"><?php echo $this->lang->line('td_phone');?></th>
				<th width="15%"><?php echo $this->lang->line('td_lang');?></th>
				<th width="15%" colspan="2"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;foreach ($contacts as $contact): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td class="center"><?php echo $i;?></td>
				<td><?php echo ucfirst($contact['firstname']);?></td>
				<td><?php echo ucfirst($contact['lastname']);?></td>
				<td><?php echo $contact['email'];?></td>
				<td><?php echo format_phone($contact['phone']);?></td>
				<td><?php echo $contact['lang'];?></td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mail/'.$contact['id'])?>" title="<?php echo $this->lang->line('btn_mails')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/mails.png')?>" alt="<?php echo $this->lang->line('btn_mails')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/delete/'.$contact['id'])?>" title="<?php echo $this->lang->line('btn_delete')?>"  class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<?php if(isset($pager) && $pager):?>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total;?> <?php echo $this->lang->line('text_total_contact');?>
		</div>
		<div class="pager_right">
			<?php echo $pager;?>
		</div>
	</div>
	<?php endif;?>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_contact');?></p>
	<?php endif ; ?>
</div>
<!-- [Main] end -->