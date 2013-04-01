<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">
function getTaxRate()
{
	var parameterVal = document.getElementById('tva').options[document.getElementById('tva').selectedIndex].value;

	if ((parameterVal > 0) ) {
	  return parameterVal;
	} else {
	  return 0;
	}
}

function doRound(x, places)
{
	return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function updateTtc()
{
	var taxRate = getTaxRate();
	var Value = $("input#price").val();

	//Retourne montant TTC
	if (Value > 0)
	{
		grossValue = Value;
	}
	else
	{
		grossValue = 0;
		//Retourne montant HT
		document.forms["form_tva"].price.value = 0;
	}

	if (taxRate > 0) {
		grossValue = grossValue * ((taxRate / 100) + 1);
	}

	//Retourne montant marge TTC
	document.forms["form_tva"].price_ttc.value = doRound(grossValue, 4);

}

function updateHt()
{
	var taxRate = getTaxRate();
	var Value = $("input#price_ttc").val();

	//Retourne montant HT
	Value = Value/(1+(taxRate/100));
	document.forms["form_tva"].price.value = doRound(Value, 4);

}
$(document).ready(function() {
	$("input.input_file").filestyle({
			image: BASE_URI+APPPATH+"views/"+ADMIN_THEME+"/img/upload_file.gif",
			imageheight : 40,
			imagewidth : 78,
			width : 240
	});
	$('.button_submit').click(function() {		
		$('#products_redirect').val(1);
		$('#submit_products').click();
	});
	$("#attributes_id").change(function() {
		var attributes_id = $(this).attr("value");
		var data = 'attributes_id='+attributes_id;
		$.ajax({
			data: data,
			dataType: 'json',
			type: "POST",
			url: '<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/ajaxProductsChangeAttributes');?>',
			cache: false,
			success: function(html){
				$("#attributes_values_id").html(html.options);
				$("#attributes_color").val(html.color);
			}
		});

	});
	$("#attributes_values_id").change(function() {
		var attributes_values_id = $(this).attr("value");
		var data = 'attributes_values_id='+attributes_values_id;
		$.ajax({
			data: data,
			dataType: 'json',
			type: "POST",
			url: '<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/ajaxProductsChangeAttributesValues');?>',
			cache: false,
			success: function(html){
				$("#attributes_color").val(html.color);
			}
		});

	});
	$("#products_combo_search").autocomplete({
		source: function( request, response ) {
		console.log = request;
		$.ajax({
				type: "POST",
				url: "<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsComboSearch/'.$product['id']);?>",
				dataType: "json",
				data: request,
				success: function(data) {
							response($.map(data, function(item) {
							return {
								label: item.label,
								id: item.id
								};
						}));
					}
				});
		},
		minLength: 2,
		select: function(event, ui) {
			$.post('<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/selectProductsCombo/'.$product['id']);?>', { tokencsrf: CSRF, products_id_y: ui.item.id }, function(data) {
				document.location.reload(true)
			}, "json");
		}
	});
});
</script>
<div id="main">
	<h2><?php echo ($product['id'])? $this->lang->line('title_edit_products').' : '.html_entity_decode($product['title'])  : $this->lang->line('title_create_products');?></h2>	
	<?php echo form_open(($product['id']) ? site_url($this->config->item('admin_folder').'/'.$module.'/productsEdit/'.$categories_id.'/'.$product['id']) : site_url($this->config->item('admin_folder').'/'.$module.'/productsCreate/'.$categories_id), array('enctype' => 'multipart/form-data', 'class' => (!$product['id'] ? 'uri_autocomplete' : ''), 'id' => 'form_products', 'name' => 'form_tva'));?>	
		<input type="hidden" name="id" value="<?php echo $product['id'];?>" />
		<input type="hidden" name="products_redirect" id="products_redirect" value="0" />
		<input type="hidden" name="products_tabs" id="products_tabs" value="#one" />
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit_products" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><input type="button" name="" value="<?php echo $this->lang->line('btn_save_quit');?>" class="input_submit button_submit"/></li>
			<?php if($product['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsDelete/'.$product['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
		<?php endif;?>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<div id="tabs">
			<ul>
				<li><a href="#one"><?php echo $this->lang->line('menu_content');?></a></li>
				<li><a href="#two"><?php echo $this->lang->line('menu_body');?></a></li>
				<li><a href="#three"><?php echo $this->lang->line('menu_seo');?></a></li>
				<li><a href="#four"><?php echo $this->lang->line('menu_categories');?></a></li>
				<?php if($product['id']):?>
				<li><a href="#five"><?php echo $this->lang->line('menu_images');?></a></li>
				<li><a href="#six"><?php echo $this->lang->line('menu_products_combo');?></a></li>
				<!--<li><a href="#seven"><?php echo $this->lang->line('menu_attributes');?></a></li>-->
				<?php endif;?>
			</ul>
			<fieldset>
				<div id="one">
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input name="title" id="title" type="text" value="<?php if($this->input->post('title')) echo $this->input->post('title');else echo html_entity_decode($product['title']);?>" class="input_text" maxlength="64"/>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input name="uri" id="uri" type="text" value="<?php if($this->input->post('uri')) echo $this->input->post('uri');else echo $product['uri'];?>" class="input_text" maxlength="64"/>
					<label for="reference"><?php echo $this->lang->line('label_reference');?></label>
					<input name="reference" id="reference" type="text" value="<?php if($this->input->post('reference')) echo $this->input->post('reference');else echo $product['reference'];?>" class="input_text" maxlength="32"/>
					<?php if(isset($manufacturers) && $manufacturers):?>
					<label for="manufacturers_id"><?php echo $this->lang->line('label_manufacturers');?></label>
					<select id="manufacturers_id" name="manufacturers_id" class="input_select">
					<option value="0" <?php if($product['manufacturers_id'] == 0):?>selected="selected"<?php endif;?>></option>
					<?php foreach($manufacturers as $manufacturer):?>
						<option value="<?php echo $manufacturer['id'];?>" <?php if($product['manufacturers_id'] == $manufacturer['id']):?>selected="selected"<?php endif;?>><?php echo html_entity_decode($manufacturer['title']);?></option>
					<?php endforeach;?>
					</select>
					<?php endif;?>
					<label for="price_shopping"><?php echo $this->lang->line('label_price_shopping');?></label>
					<input name="price_shopping" id="price_shopping" type="text" value="<?php if($this->input->post('price_shopping')) echo $this->input->post('price_shopping');else echo $product['price_shopping'];?>" class="input_text" maxlength="17"/>
					<label for="price"><?php echo $this->lang->line('label_price_no_tax');?></label>
					<input name="price" id="price" type="text" value="<?php if($this->input->post('price')) echo $this->input->post('price');else echo $product['price'];?>" class="input_text" onkeyup="updateTtc()" maxlength="17"/>
					<label for="tva"><?php echo $this->lang->line('label_tax');?></label>
					<select name="tva" id="tva" class="input_select" onchange="updateTtc()">
						<option value="0"><?php echo $this->lang->line('option_no_tax');?></option>
						<?php if (isset($tva) && $tva) : ?>
							<?php foreach ($tva as $vat): ?>
							<option value="<?php echo $vat['rate']?>" <?php if ($this->input->post('tva') == $vat['rate'] || $product['tva'] == $vat['rate']): echo 'selected="selected"'; endif;?>><?php echo $vat['title']?></option>
							<?php endforeach;?>
						<?php endif; ?>
					</select>
					<label for="price_ttc"><?php echo $this->lang->line('label_price_width_tax');?></label>
					<input name="price_ttc" id="price_ttc" type="text" value="<?php if($this->input->post('price')) echo $this->input->post('price');else echo $this->tva->get_price_ttc($product['price'], '1', $product['tva']);?>"  class="input_text" onkeyup="updateHt()" maxlength="17"/>
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" class="input_select" id="active">
						<option value="0" <?php echo ($product['active'] == '0') ? "selected" : "";?>><?php echo $this->lang->line('option_desactivate')?></option>
						<option value="1" <?php echo ($product['active'] == '1') ? "selected" : "";?>><?php echo $this->lang->line('option_activate')?></option>
					</select>
				</div>
				<div id="two">
					<textarea name="body" id="body" class="input_textarea"><?php if($this->input->post('body')) echo $this->input->post('body');else echo $product['body'];?></textarea>
				</div>
				<div id="three">
					<label for="meta_title"><?php echo $this->lang->line('label_meta_title');?></label>
					<input type="text" id="meta_title" name="meta_title" value="<?php if($this->input->post('meta_title')) echo $this->input->post('meta_title');else echo $product['meta_title'];?>" class="input_text" maxlength="64"/>
					<label for="meta_keywords"><?php echo $this->lang->line('label_meta_keywords');?></label>
					<input type="text" id="meta_keywords" name="meta_keywords" value="<?php if($this->input->post('meta_keywords')) echo $this->input->post('meta_keywords');else echo $product['meta_keywords'];?>" class="input_text" maxlength="255"/>
					<label for="meta_description"><?php echo $this->lang->line('label_meta_description');?></label>
					<input type="text" id="meta_description" name="meta_description" value="<?php if($this->input->post('meta_description')) echo $this->input->post('meta_description');else echo $product['meta_description'];?>" class="input_text"/>
				</div>
				<div id="four">
					<?php if(isset($categories) && $categories):?>
					<table class="table_list">
						<thead>
							<tr>
								<th width="70%"><?php echo $this->lang->line('td_categories');?></th>
								<th width="10%" class="center"><?php echo $this->lang->line('td_action');?></th>
								<th width="10%" class="center"><?php echo $this->lang->line('td_default');?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;$count_categories = count($categories);foreach($categories as $categorie):?>
							<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
							<tr class="<?php echo $rowClass?>">
								<td>
									<label for="categories_<?php echo $categorie['id'];?>" style="width:100%;float:left;">
									<?php if ($categorie['level'] == 0) :?>
									<span class="lv0_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv0.gif')?>" alt=""/></span>
									<span class="lv0"><?php echo html_entity_decode($categorie['title'])?></span>
									<?php elseif ($categorie['level'] == 1) :?>
									<span class="lv1_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv1.gif')?>" alt=""/></span>
									<span class="lv1"><?php echo html_entity_decode($categorie['title'])?></span>
									<?php elseif ($categorie['level'] == 2) :?>
									<span class="lv2_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv2.gif')?>" alt=""/></span>
									<span class="lv2"><?php echo html_entity_decode($categorie['title'])?></span>
									<?php endif;?>
									</label>
								</td>
								<td class="center">
									<input type="checkbox" id="categories_<?php echo $categorie['id'];?>" name="categories[<?php echo $categorie['id'];?>]" value="<?php echo $categorie['id'];?>" <?php if(isset($products_to_categories) && in_array($categorie['id'], $products_to_categories) || $categories_id == $categorie['id']):?>checked="checked"<?php endif;?>/>
								</td>
								<td class="center">
									<input type="radio" name="categories_id_default" value="<?php echo $categorie['id'];?>" <?php if($categorie['id'] == $product['categories_id_default'] || $categories_id == $categorie['id']):?>checked="checked"<?php endif;?>/>
								</td>
							</tr>
							<?php $i++;endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</div>
				<?php if($product['id']):?>
				<div id="five">
					<label for="image"><?php echo $this->lang->line('label_image');?></label>
					<input type="file" name="image" id="image" value="" class="file input_file"/>
					<label for="legend"><?php echo $this->lang->line('label_legend');?></label>
					<input type="text" name="legend" id="legend" value="" class="input_text" maxlength="64"/>
					<br class="clear"/>
					<br />
					<?php if(isset($images) && $images):?>
					<table class="table_list">
						<thead>
							<tr>
								<th width="70%"><?php echo $this->lang->line('td_image');?></th>
								<th width="10%" class="center"><?php echo $this->lang->line('td_cover');?></th>
								<th width="20%" class="center" colspan="2"><?php echo $this->lang->line('td_action');?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;$count_images = count($images);foreach($images as $image):?>
							<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
							<tr class="<?php echo $rowClass?>">
								<td>
									<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$image['file'])):?>
									<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x100/'.$image['file']);?>" alt="<?php echo $image['options']['legend'];?>"/>
									<?php else :?>
									<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x100/default.jpg');?>" alt="<?php echo html_entity_decode($product['title']);?>" width="160" height="100"/>
									<?php endif;?>
								</td>
								<td class="center">
									<?php if($image['options']['cover'] == 1):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsCoverImages/'.$categories_id.'/'.$product['id'].'/'.$image['id']);?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/enabled.png')?>" alt="" width="16" height="16"/></a><?php endif;?>
									<?php if($image['options']['cover'] == 0):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsCoverImages/'.$categories_id.'/'.$product['id'].'/'.$image['id']);?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/forbbiden.png')?>" alt="" width="16" height="16"/></a><?php endif;?>
								</td>
								<td class="center">
									<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsMoveImages/'.$categories_id.'/'.$product['id'].'/'.$image['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
									<?php endif;?>
									<?php if(($count_images) != $i):?>
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsMoveImages/'.$categories_id.'/'.$product['id'].'/'.$image['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
									<?php endif;?>
								</td>
								<td class="center">
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsDeleteImages/'.$categories_id.'/'.$product['id'].'/'.$image['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
								</td>
							</tr>
							<?php $i++;endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</div>
				<div id="six">
					<label for="products_combo_search"><?php echo $this->lang->line('label_search_product_combo');?></label>
					<input type="text" name="products_combo_search" id="products_combo_search" value="" class="input_text"/><br class="clear"/><br />
					<?php if (isset($products_combos) && $products_combos) : ?>
					<?php //pre_affiche($products_combos);?>
					<table class="table_list">
						<thead>
							<tr>
								<th width="80%"><?php echo $this->lang->line('td_products');?></th>
								<th width="20%" class="center"><?php echo $this->lang->line('td_action');?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 1; foreach ($products_combos as $product): ?>
							<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
							<tr class="<?php echo $rowClass?>">
								<td>
									<?php echo html_entity_decode($product['title'])?>
								</td>
								<td class="center">
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsCombosDelete/'.$categories_id.'/'.$product['products_id_x'].'/'.$product['products_id_y'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
								</td>
							</tr>
							<?php $i++; endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</div>
				<!--
				<div id="seven">
					<label for="attributes_id"><?php echo $this->lang->line('label_attributes');?></label>
					<select id="attributes_id" name="attributes_id" class="input_select">
						<option value="0"></option>
						<?php if(isset($attributes) && $attributes):?>
						<?php foreach($attributes as $attribute):?>
						<option value="<?php echo $attribute['id'];?>"><?php echo html_entity_decode($attribute['name']);?></option>
						<?php endforeach;?>
						<?php endif;?>
					</select>
					<label for="attributes_values_id"><?php echo $this->lang->line('label_values');?></label>
					<select id="attributes_values_id" name="attributes_values_id" class="input_select">
						<option value="0"></option>
						<?php if(isset($attributes_values) && $attributes_values):?>
						<?php foreach($attributes_values as $attribute_value):?>
						<option value="<?php echo $attribute_value['id'];?>"><?php echo html_entity_decode($attribute_value['name']);?></option>
						<?php endforeach;?>
						<?php endif;?>
					</select>
					<label for="suffix"><?php echo $this->lang->line('label_suffix');?></label>
					<select name="suffix" id="suffix" class="input_select">
						<option value=""></option>
						<option value="+"><?php echo $this->lang->line('option_more')?></option>
						<option value="-"><?php echo $this->lang->line('option_less')?></option>
					</select>
					<label for="attributes_price"><?php echo $this->lang->line('label_price_no_tax');?></label>
					<input name="attributes_price" id="attributes_price" type="text" value="<?php if($this->input->post('attributes_price')) echo $this->input->post('attributes_price');?>" class="input_text" maxlength="64"/>
					<input name="attributes_color" id="attributes_color" type="hidden" value="<?php if($this->input->post('attributes_color')) echo $this->input->post('attributes_color');?>" maxlength="32"/>
					<?php if(isset($products_attributes_values) && $products_attributes_values):?>
					<br class="clear"/><br />
					<table class="table_list">
						<thead>
							<tr>
								<th width="60%"><?php echo $this->lang->line('td_name')?></th>
								<th width="20%"><?php echo $this->lang->line('td_price')?></th>
								<th width="20%" colspan="1" class="center"><?php echo $this->lang->line('td_action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;foreach($products_attributes_values as $product_attribute_value):?>
							<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
							<tr class="<?php echo $rowClass?>">
								<td>
									<?php echo html_entity_decode($product_attribute_value['aNAME']);?> - <?php echo html_entity_decode($product_attribute_value['avNAME']);?>
								</td>
								<td>
									<?php echo $product_attribute_value['suffix'];?> <?php echo format_price($this->tva->get_price_ttc($product_attribute_value['price'], '1', ''))?>
								</td>
								<!--<td class="center">
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsAttributesEdit/'.$categories_id.'/'.$product['id'].'/'.$product_attribute_value['pavID'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
								</td>
								<td class="center">
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsAttributesDelete/'.$categories_id.'/'.$product['id'].'/'.$product_attribute_value['pavID'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
								</td>
							</tr>
							<?php $i++;endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</div>
				-->
				<?php endif;?>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs({
			show: function(e, ui) {
				$('#products_tabs').val('#'+ui.panel.id);
			}
		 });
	});
	</script>
</div>
<!-- [Main] end -->