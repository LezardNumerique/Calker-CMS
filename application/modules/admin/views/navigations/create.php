<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">
$(function() {
	<?php if($navigation['id']):?>
	$('#submit_button').click(function() {
		$.post('<?php echo site_url($this->config->item('admin_folder').'/navigations/saveAjax');?>', $("#form_navigations").serialize(), function(data) 		{
			var data = JSON.parse(data);
			$("."+data.type).hide();
			$('.ajax_'+data.type).fadeTo(0, 200);
			$('.ajax_'+data.type).html(data.text);
			$("."+data.type+"_closable").append('<a href="#" class="'+data.type+'_close"><?php echo $this->lang->line('btn_close');?></a>');
		});
	});
	<?php else:?>
	$('#submit_button').click(function() {
		$.post('<?php echo site_url($this->config->item('admin_folder').'/navigations/saveAjax');?>', $("#form_navigations").serialize(), function(data) 		{
			var data = JSON.parse(data);
			if(data.type == 'notice')
			{
				window.location = '<?php echo site_url($this->config->item('admin_folder').'/navigations/edit');?>/'+data.text;
			}
			else
			{
				$("."+data.type).hide();
				$('.ajax_'+data.type).fadeTo(0, 200);
				$('.ajax_'+data.type).html(data.text);
				$("."+data.type+"_closable").append('<a href="#" class="'+data.type+'_close">Fermer</a>');
			}
		});
	});
	<?php endif;?>	
	$('.dialog').click(function() {
		var uri = $(this).attr('href');
		var title = $(this).attr('title');
		$("#dialog").load(uri, function() {
			calker.init();
			$(this).dialog({
				title:title,
				width:'710px',
				modal: true,
				resizable: false,
				draggable: false,
				position: 'center',
				overlay: {
					backgroundColor: '#000',
					opacity: 0.5
				},
				buttons: {	
					Enregistrer: function() {
						$.post('<?php echo site_url($this->config->item('admin_folder').'/navigations/createPage/'.$navigation['id']);?>', $("#form_pages").serialize(), function(data_page) {	
							if(data_page)
							{						
								$.post('<?php echo site_url($this->config->item('admin_folder').'/navigations/reloadListPages');?>', {tokencsrf: CSRF, uri:data_page}, function(data_reload) {
									$("#t_uri").html(data_reload.options);
									$('#t_uri').val(data_reload.uri);
									$("#uri").val(data_reload.uri);								
								}, "json");
							}								
							$("#dialog").dialog('close');																		
						});
					},				
					Fermer: function() {
						$("#dialog").dialog('close');
					}					
				}
			});
		});
		return false;
	});	
});
</script>
<div id="dialog"></div>
<div id="main">
	<h2><?php echo ($navigation['id'])? $this->lang->line('title_edit_navigation').' : '.html_entity_decode($navigation['title']) : $this->lang->line('title_create_navigation')?></h2>
	<?php echo form_open($this->config->item('admin_folder').'/navigations/save', array('id' => 'form_navigations'));?>
		<input type="hidden" name="id" value="<?php echo $navigation['id']?>"/>
		<ul class="manage">
			<li><input type="button" name="submit_button" id="submit_button" value="<?php echo $this->lang->line('btn_save')?>" class="input_submit"/></li>
			<li><input type="submit" name="submit" id="submit" value="<?php echo $this->lang->line('btn_save_quit')?>" class="input_submit"/></li>
			<?php if($navigation['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/delete/'.$navigation['id']);?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'));?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if($alerte = $this->session->flashdata('alerte')):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<p class="ajax_notice notice_closable"></p>
		<p class="ajax_alerte alerte_closable"></p>
		<?php $post = $this->session->flashdata('post');?>
		<fieldset>
			<input type="hidden" name="redirect_uri" value="<?php echo $this->uri->uri_string();?>"/>
			<label for="title"><?php echo $this->lang->line('label_title');?></label>
			<input name="title" id="title" type="text"  value="<?php if(isset($post['title'])) echo $post['title'];else echo html_entity_decode($navigation['title']);?>" class="input_text" autocomplete="off" maxlength="64"/>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<?php if($parent_id != 0):?>
			<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
			<input name="uri" id="uri" type="text"  value="<?php if(isset($post['uri'])) echo $post['uri'];else echo $navigation['uri'];?>" class="input_text" maxlength="128"/>
			<select id="t_uri" name="t_uri" class="input_select target">
				<option value="0"><?php echo $this->lang->line('option_or_selected');?></option>
				<?php
				if(isset($pages) && $pages):
				foreach ($pages as $page):?>
				<option value="<?php echo $page['uri']?>"><?php echo ($page['level'] > 0 ? "|".str_repeat("__", $page['level']) : '').character_limiter(html_entity_decode($page['title']), 40);?></option>
				<?php endforeach;endif;?>
			</select>
			<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/createPage/'.$navigation['id']);?>" class="dialog" title="<?php echo $this->lang->line('btn_create_page');?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/more.png');?>" alt="<?php echo $this->lang->line('btn_create_page');?>" width="16" height="16"/></a>
			<?php endif;?>
			<label for="parent_id"><?php echo $this->lang->line('label_parent');?></label>
			<select name="parent_id" class="input_select">
				<option value="0"></option>
				<?php
				$follow = null;
				foreach ($navigations as $parent):?>
				<?php
				if ($navigation['id'] == $parent['id'] || $follow == $parent['parent_id'])
				{
					$follow = $parent['id'];
					continue;
				}
				else
				{
					$follow = null;
				}
				?>
				<option value="<?php echo $parent['id']?>" <?php echo ($navigation['parent_id'] == $parent['id'] || (isset($parent_id) && ($parent_id == $parent['id']))) ? 'selected="selected"' : '';?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']) : '';?> <?php echo html_entity_decode($parent['title']).$follow;?></option>
			<?php endforeach;?>
			</select>			
			<?php if(isset($modules) && $modules):?>
			<label for="module"><?php echo $this->lang->line('label_module');?></label>
			<select id="module" name="module" class="input_select">
				<option value=""></option>
				<?php foreach($modules as $module):?>
				<option value="<?php echo $module['name'];?>"<?php if($module['name'] == $navigation['module']):?> selected="selected"<?php endif;?>><?php echo ucwords($module['name']);?></option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
			<label for="active"><?php echo $this->lang->line('label_status');?></label>
			<select name="active" class="input_select" id="active">
				<option value='1' <?php if ($navigation['active'] == '1'):?>selected="selected" <?php endif;?>><?php echo $this->lang->line('label_activate');?></option>
				<option value='0' <?php if ($navigation['active'] == '0'):?>selected="selected" <?php endif;?>><?php echo $this->lang->line('label_desactivate');?></option>
			</select>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->