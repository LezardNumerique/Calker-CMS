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
	var Value = $("input#new_price").val();

	//Retourne montant TTC
	if (Value > 0)
	{
		grossValue = Value;
	}
	else
	{
		grossValue = 0;
		//Retourne montant HT
		document.forms["form_tva"].new_price.value = 0;
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
	document.forms["form_tva"].new_price.value = doRound(Value, 4);

}

$(function() {
	$("#date_begin").datepicker({ dateFormat: 'dd/mm/yy' });
	$("#date_end").datepicker({ dateFormat: 'dd/mm/yy' });

	$("#products_search").autocomplete({
		source: function( request, response ) {
		//console.log(request.term);
		//request = [];
		//$('tbody').children('tr').each(function(idx, elm) {
			//order.push(elm.id.split('_')[1])
		//});
		//request.push('');
		$.ajax({
			type: "POST",
			url: "<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsSearch');?>",
			dataType: "json",
			data: {tokencsrf: CSRF, term: request.term},
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
			$('#products_id').val(ui.item.id);
		}
	});

});
</script>
<div id="main">
	<h2><?php echo ($special['id'])? $this->lang->line('title_edit_specials').' : '.html_entity_decode($product['title'])  : $this->lang->line('title_create_specials');?></h2>	
	<?php echo form_open(($special['id']) ? site_url($this->config->item('admin_folder').'/'.$module.'/specialsEdit/'.$special['id']) : site_url($this->config->item('admin_folder').'/'.$module.'/specialsCreate'), array('enctype' => 'multipart/form-data', 'id' => 'form_specials', 'name' => 'form_tva'));?>	
		<input type="hidden" name="id" value="<?php echo $special['id'];?>" />
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<?php if($special['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/specialsDelete/'.$special['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
		<?php endif;?>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<fieldset>
			<div>
				<label for="products_search"><?php echo $this->lang->line('label_products');?></label>
				<input name="products_search" id="products_search" type="text" value="<?php if($this->input->post('products_search')) echo $this->input->post('products_search');if(isset($product['title'])) echo html_entity_decode($product['title']);?>" class="input_text"/>
				<input name="products_id" id="products_id" type="hidden" value="<?php if($this->input->post('products_id')) echo $this->input->post('products_id');else echo $special['products_id'];?>" class="input_text"/>
				<label for="date_begin"><?php echo $this->lang->line('label_date_begin');?></label>
				<input name="date_begin" id="date_begin" type="text" value="<?php if($this->input->post('date_begin')) echo $this->input->post('date_begin');if($special['date_begin']) echo substr($special['date_begin'], 8,2).'/'.substr($special['date_begin'], 5,2).'/'.substr($special['date_begin'], 0,4);?>" class="input_text"/>
				<label for="date_end"><?php echo $this->lang->line('label_date_end');?></label>
				<input name="date_end" id="date_end" type="text" value="<?php if($this->input->post('date_end')) echo $this->input->post('date_end');if($special['date_end']) echo substr($special['date_end'], 8,2).'/'.substr($special['date_end'], 5,2).'/'.substr($special['date_end'], 0,4);?>" class="input_text"/>
				<label for="new_price"><?php echo $this->lang->line('label_price_no_tax');?></label>
				<input name="new_price" id="new_price" type="text" value="<?php if($this->input->post('new_price')) echo $this->input->post('new_price');else echo $special['new_price'];?>" class="input_text" onkeyup="updateTtc()" maxlength="17"/>
				<label for="tva"><?php echo $this->lang->line('label_tax');?></label>
				<select name="tva" id="tva" class="input_select" onchange="updateTtc()">
					<option value="0"><?php echo $this->lang->line('option_no_tax');?></option>
					<?php if (isset($tva) && $tva) : ?>
						<?php foreach ($tva as $vat): ?>
						<option value="<?php echo $vat['rate']?>" <?php if ($this->input->post('tva') == $vat['rate'] || $special['tva'] == $vat['rate']): echo 'selected="selected"'; endif;?>><?php echo $vat['title']?></option>
						<?php endforeach;?>
					<?php endif; ?>
				</select>
				<label for="price_ttc"><?php echo $this->lang->line('label_price_width_tax');?></label>
				<input name="price_ttc" id="price_ttc" type="text" value="<?php if($this->input->post('new_price')) echo $this->input->post('new_price');else echo $this->tva->get_price_ttc($special['new_price'], '1', $special['tva']);?>"  class="input_text" onkeyup="updateHt()" maxlength="17"/>
				<label for="active"><?php echo $this->lang->line('label_status');?></label>
				<select name="active" class="input_select" id="active">
					<option value="0" <?php echo ($special['active'] == '0') ? "selected" : "";?>><?php echo $this->lang->line('option_desactivate')?></option>
					<option value="1" <?php echo ($special['active'] == '1') ? "selected" : "";?>><?php echo $this->lang->line('option_activate')?></option>
				</select>
			</div>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->