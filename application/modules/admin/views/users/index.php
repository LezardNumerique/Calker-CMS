<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_users');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/users/create')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
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
					<input type="text" class="input_text" name="filter" value="<?php echo $this->session->userdata('filter_users');?>"/>				
					<input type="submit" class="input_submit" name="submit" value="<?php echo $this->lang->line('btn_search');?>"/>
				</div>				
			</form>
		</div>
	</div>
	<?php if(isset($users) && $users) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers: {4:{sorter:false}},
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
				<th width="25%"><?php echo $this->lang->line('td_username');?></th>
				<th width="25%"><?php echo $this->lang->line('td_group');?></th>
				<th width="25%"><?php echo $this->lang->line('td_email');?></th>
				<th width="20%" colspan="3"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;foreach ($users as $user): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td class="center"><?php echo ($i*$start)+$i?></td>
				<td><?php echo ucfirst($user['username']);?></td>
				<td><?php echo ucfirst($user['title']);?></td>
				<td><?php echo $user['email'];?></td>
				<td class="center">
					<?php if($user['uID'] != 1 && $this->user->id != $user['uID']):?>
					<?php if ($user['active']== 1): echo '<a href="'.site_url($this->config->item('admin_folder').'/users/flag/'.$user['uID'].'/0').'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url().$this->config->item('admin_folder').'/users/flag/'.$user['uID'].'/1" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'" /></a>';
					endif;?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if($this->user->root == 1):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/users/edit/'.$user['uID'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
					<?php else :?>
						<?php if($user['uID'] != 1):?>
						<a href="<?php echo site_url($this->config->item('admin_folder').'/users/edit/'.$user['uID'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
						<?php endif;?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if($user['uID'] != 1 && $this->user->id != $user['uID']):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/users/delete/'.$user['uID'])?>" title="<?php echo $this->lang->line('btn_delete')?>" onclick="javascript:return confirmDelete();" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
					<?php endif;?>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total;?> <?php echo $this->lang->line('text_total_users');?>
		</div>
		<div class="pager_right">
			<?php if(isset($pager) && $pager):?>
			<?php echo $pager?>
			<?php endif;?>
		</div>
	</div>

	<?php else:?>
	<p class="no_data"><?php echo $this->lang->line('text_no_user');?></p>
	<?php endif ; ?>
</div>
<!-- [Main] end -->
