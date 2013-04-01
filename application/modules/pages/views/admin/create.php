<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">
$(function() {
	<?php if($page['id']):?>
	$('#submit_button').click(function() {
		$.post('<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/editAjax/'.$page['id']);?>', $("#form_pages").serialize(), function(data) {
			var data = JSON.parse(data);
			$("."+data.type).hide();
			$('.ajax_'+data.type).fadeTo(0, 200);
			$('.ajax_'+data.type).html(data.text);
			$("."+data.type+"_closable").append('<a href="#" class="'+data.type+'_close">Fermer</a>');
		});
	});
	<?php else:?>
	$('#submit_button').click(function() {
		$.post('<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/createAjax');?>', $("#form_pages").serialize(), function(data) {
			var data = JSON.parse(data);
			if(data.type == 'notice')
			{
				window.location = '<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/edit');?>/'+data.text;
			}
			else
			{
				$("."+data.type).hide();
				$('.ajax_'+data.type).fadeTo(0, 200);
				$('.ajax_'+data.type).html(data.text);
				$("."+data.type+"_closable").append('<a href="#" class="'+data.type+'_close"><?php echo $this->lang->line('btn_close');?></a>');
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
						$.post('<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/createNavigation');?>', $("#form_navigations").serialize(), function(data_page) {
							if(data_page)
							{						
								$.post('<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/reloadListNavigations');?>', {tokencsrf: CSRF, uri:data_page.uri, page_parents_uri:data_page.page_parents_uri, page_uri:data_page.page_uri}, function(data_reload) {
									$("#t_uri").html(data_reload.options);
									$('#t_uri').val(data_reload.uri);
									$("#uri").val(data_reload.uri);								
								}, "json");								
							}
							$("#dialog").dialog('close');														
						}, "json");
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
	<h2><?php echo ($page['id'])? $this->lang->line('title_edit_page').' : '.html_entity_decode($page['title']) : $this->lang->line('title_create_page');?></h2>
	<?php echo form_open(($page['id']) ? $this->config->item('admin_folder').'/'.$module.'/edit/'.$page['id'] : $this->config->item('admin_folder').'/'.$module.'/create', array('class' => (!$page['id'] ? 'uri_autocomplete' : ''), 'id' => 'form_pages'));?>
		<ul class="manage">
			<li><input type="button" name="submit_button" id="submit_button" value="<?php echo $this->lang->line('btn_save')?>" class="input_submit"/></li>
			<li><input type="submit" name="submit" id="submit" value="<?php echo $this->lang->line('btn_save_quit')?>" class="input_submit"/></li>
			<?php if($page['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/selectParag/'.$page['id']);?>"><?php echo $this->lang->line('btn_create_parag');?></a></li><?php endif;?>
			<?php if($page['id']):?><li><a href="<?php echo site_url($page['uri']);?>" onclick="window.open(this.href);return false;"><?php echo $this->lang->line('btn_preview');?></a></li><?php endif;?>
			<?php if($page['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/delete/'.$page['id']);?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'));?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<p class="ajax_notice notice_closable"></p>
		<p class="ajax_alerte alerte_closable"></p>
		<div id="tabs">
			<ul>
				<li><a href="#one"><?php echo $this->lang->line('menu_content');?></a></li>
				<li><a href="#two"><?php echo $this->lang->line('menu_seo');?></a></li>
				<li><a href="#three"><?php echo $this->lang->line('menu_options');?></a></li>
			</ul>
			<fieldset>
				<div id="one">
					<?php if($page['id']):?><input type="hidden" name="pages_id" id="pages_id" value="<?php echo $page['id'];?>"/><?php endif;?>
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input type="text" id="title" name="title" value="<?php echo ($this->input->post('title')) ? $this->input->post('title') : html_entity_decode($page['title']);?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input type="text" id="uri" name="uri" value="<?php echo ($this->input->post('uri')) ? $this->input->post('uri') : $page['uri'];?>" class="input_text" maxlength="128"/>										
					<select id="t_uri" name="t_uri" class="input_select target">
						<option value="0"><?php echo $this->lang->line('option_or_selected');?></option>
						<?php
						if(isset($navigations) && $navigations):
						foreach ($navigations as $navigation):						
						?>
						<option value="<?php echo str_replace($parent_uri, '', $navigation['uri']);?>"<?php if($navigation['parent_id'] == 0 || in_array($navigation['uri'], $parents_uri) && $navigation['uri'] != $parent_uri.$page['uri']):?> disabled="disabled"<?php endif;?>><?php echo ($navigation['level'] > 0 ? "|".str_repeat("__", $navigation['level']) : '').character_limiter(html_entity_decode($navigation['title']), 40);?></option>
						<?php endforeach;endif;?>
					</select>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/createNavigation/'.$page['id'].'/'.$parent_id);?>" class="dialog" title="<?php echo $this->lang->line('btn_create_navigation');?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/more.png');?>" alt="<?php echo $this->lang->line('btn_create_navigation');?>" width="16" height="16"/></a>
					<span class="required" style="margin-left:0;"><?php echo $this->lang->line('text_required');?></span>
					<label for="class"><?php echo $this->lang->line('label_class');?></label>
					<input type="text" id="class" name="class" value="<?php echo ($this->input->post('class')) ? $this->input->post('class') : $page['class'];?>" class="input_text" maxlength="32"/>
					<label for="parent_id"><?php echo $this->lang->line('label_parent');?></label>
					<select name="parent_id" id="parent_id" class="input_select">
					<option value="0"></option>
					<?php
					$follow = null;
					if($parents):
					foreach ($parents as $parent):
					?>
					<?php
					if ($page['id'] == $parent['id'] || $follow == $parent['parent_id'])
					{
						$follow = $page['id'];
						continue;
					}
					else
					{
						$follow = null;
					}
					?>
					<option value="<?php echo $parent['id']?>" <?php echo ($page['parent_id'] == $parent['id'] || (isset($parent_id) && $parent_id == $parent['id'])) ? 'selected="selected"' : '';?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']): '';?> <?php echo (strlen(html_entity_decode($parent['title'])) > 50 ) ? substr(html_entity_decode($parent['title']), 0, 50) . '...' : html_entity_decode($parent['title'])?></option>
					<?php
					endforeach;
					endif;
					?>
					</select>
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" id="active" class="input_select">
						<option value="0"<?php if ($this->input->post('active') == 0 || $page['active'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_desactivate');?></option>
						<option value="1"<?php if ($this->input->post('active') == 1 || $page['active'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_activate');?></option>
					</select>
				</div>
				<div id="two">
					<label for="meta_title"><?php echo $this->lang->line('label_meta_title');?></label>
					<input type="text" id="meta_title" name="meta_title" value="<?php echo ($this->input->post('meta_title')) ? $this->input->post('meta_title') : html_entity_decode($page['meta_title']);?>" class="input_text" maxlength="128"/>
					<label for="meta_keywords"><?php echo $this->lang->line('label_meta_keywords');?></label>
					<input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo ($this->input->post('meta_keywords')) ? $this->input->post('meta_keywords') : $page['meta_keywords'];?>" class="input_text" maxlength="255"/>
					<label for="meta_description"><?php echo $this->lang->line('label_meta_description');?></label>
					<input type="text" id="meta_description" name="meta_description" value="<?php echo ($this->input->post('meta_description')) ? $this->input->post('meta_description') : $page['meta_description'];?>" class="input_text" maxlength="255"/>
				</div>
				<div id="three">
					<label for="show_sub_pages"><?php echo $this->lang->line('label_show_sub_pages');?></label>
					<select name="show_sub_pages" id="show_sub_pages" class="input_select">
					<option value='0'<?php if ($this->input->post('show_sub_pages') == 0 || $page['show_sub_pages'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_no');?></option>
					<option value='1'<?php if ($this->input->post('show_sub_pages') == 1 || $page['show_sub_pages'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_yes');?></option>
					</select>
					<label for="show_navigation"><?php echo $this->lang->line('label_show_navigation');?></label>
					<select name="show_navigation" id="show_navigation" class="input_select">
					<option value="0"<?php if ($this->input->post('show_navigation') == 0 || $page['show_navigation'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_no');?></option>
					<option value="1"<?php if ($this->input->post('show_navigation') == 1 || $page['show_navigation'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_yes');?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
		$("#sortable").sortable({
			update : function () {
				order = [];
				$('tbody').children('tr').each(function(idx, elm) {
				  order.push(elm.id.split('_')[1])
				});
				$.post("<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/sortOrderParag/'.$page['id']);?>", {tokencsrf: CSRF, items: order}, function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close"><?php echo $this->lang->line('btn_close')?></a>');
				});
			}
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
	<?php if($page['id']):?>
	<h2 id="paragraphe"><?php echo $this->lang->line('title_paragraphs');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/selectParag/'.$page['id']);?>"><?php echo $this->lang->line('btn_create_parag');?></a></li>
		<li><a href="<?php echo site_url($page['uri']);?>"><?php echo $this->lang->line('btn_preview');?></a></li>
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module);?>"><?php echo $this->lang->line('btn_return')?></a></li>
	</ul>
	<?php if(isset($paragraphs) && $paragraphs):?>
	<?php if($alerte = validation_errors()):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<p class="ajax_notice notice_closable"></p>
	<table class="table_list" id="table_sort">
		<thead>
			<tr>
				<th width="3%" class="center">#</th>
				<th width="30%"><?php echo $this->lang->line('td_paragraphs')?></th>
				<th width="30%"><?php echo $this->lang->line('td_paragraphs_types')?></th>
				<th width="25%" colspan="4"><?php echo $this->lang->line('td_action')?></th>
			</tr>
		</thead>
		<tbody id="sortable">
			<?php $i = 1;$count_paragraphs = count($paragraphs);foreach($paragraphs as $paragraph):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $paragraph['pID'];?>">
				<td class="center"><?php echo $i?></td>
				<td>
					<?php if($paragraph['title'] != '') : echo $paragraph['title'];else : echo $this->lang->line('text_paragraph_number').' '.$paragraph['pID'];endif;?>
				</td>
				<td><?php echo $this->lang->line('text_paragraph_type_'.$paragraph['code']);?></td>
				<td class="center">
					<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/moveParag/'.$page['id'].'/'.$paragraph['pID'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
					<?php if(($count_paragraphs) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/moveParag/'.$page['id'].'/'.$paragraph['pID'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if ($paragraph['pACTIVE'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/flagParag/'.$page['id'].'/'.$paragraph['pID'].'/'.$paragraph['pACTIVE']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/flagParag/'.$page['id'].'/'.$paragraph['pID'].'/'.$paragraph['pACTIVE']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';
					endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/editParag/'.$page['id'].'/'.$paragraph['pID'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/deleteParag/'.$page['id'].'/'.$paragraph['pID'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_paragraphs');?></p>
	<?php endif;?>
	<?php endif;?>
</div>
<!-- [Main] end -->