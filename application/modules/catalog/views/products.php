<div id="products">
	<div class="left">
		<!--BEGIN BIG IMG-->
		<?php if(isset($images_products) && $images_products):?>
		<?php foreach($images_products as $image_product):?>
		<?php if($image_product['options']['cover'] == 1):?>
		<div id="img_big" style="min-height:<?php echo $this->system->get_images_size('./'.$this->config->item('medias_folder').'/images/.cache/350x500/'.$image_product['file'], 1);?>px;">
			<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$image_product['file'])):?>
			<a href="<?php echo site_url('./'.$this->config->item('medias_folder').'/images/800x600/'.$image_product['file']);?>" class="colorbox">
				<img src="<?php echo site_url('./'.$this->config->item('medias_folder').'/images/350x500/'.$image_product['file']);?>" alt="<?php echo html_entity_decode($product['title']);?>" <?php echo $this->system->get_images_size('./'.$this->config->item('medias_folder').'/images/.cache/350x500/'.$image_product['file']);?>/>
			</a>
			<?php else:?>
			<img src="<?php echo site_url($this->config->item('medias_folder').'/images/350x/default.jpg');?>" alt="<?php echo html_entity_decode($product['title']);?>" width="350" height="262"/>
			<?php endif;?>
		</div>
		<script type="text/javascript">
		function popin_products ()
		{
			$(document).ready(function(){
				$(".colorbox").colorbox({transition:"none", slideshow:true});
			});
		}
		popin_products();
		</script>
		<?php endif;?>
		<?php endforeach;?>
		<?php else:?>
		<img src="<?php echo site_url($this->config->item('medias_folder').'/images/350x/default.jpg');?>" alt="<?php echo html_entity_decode($product['title']);?>" width="350" height="262"/>
		<?php endif;?>
		<!--END BIG IMG-->
		<!--BEGIN THUMBS IMG-->
		<?php //pre_affiche($images_products);?>
		<?php if(isset($images_products) && $images_products):?>
		<ul id="img_thumbs">
		<?php $i=1;foreach($images_products as $image_product):?>
		<?php if ($i % 3 != 0): $rowClass = '';else: $rowClass = 'last';endif;?>
			<li class="<?php echo $rowClass;?>">
				<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$image_product['file'])):?>
				<a href="<?php echo site_url('./'.$this->config->item('medias_folder').'/images/350x500/'.$image_product['file']);?>" class="thumbs" rel="<?php echo $image_product['file'];?>">
					<img src="<?php echo site_url('./'.$this->config->item('medias_folder').'/images/100x75/'.$image_product['file']);?>" alt="<?php echo html_entity_decode($product['title']);?>" <?php echo $this->system->get_images_size('./'.$this->config->item('medias_folder').'/images/.cache/100x75/'.$image_product['file']);?>/>
				</a>
				<?php else:?>
				<img src="<?php echo site_url($this->config->item('medias_folder').'/images/100x75/default.jpg');?>" alt="<?php echo html_entity_decode($product['title']);?>" width="100" height="75"/>
				<?php endif;?>
			</li>
		<?php $i++;endforeach;?>
		</ul>
		<?php endif;?>
		<script type="text/javascript">
		$(document).ready(function(){
			$(".thumbs").click(function() {
				var src = $(this).attr('href');
				var file = $(this).attr('rel');
				$("#img_big").html('<a href="<?php echo site_url('./'.$this->config->item('medias_folder').'/images/800x600');?>/'+file+'" class="colorbox"><img src="'+src+'" alt="" width="" height=""/></a>');
				popin_products();
				return false;
			});
		});
		</script>
		<!--END THUMBS IMG-->
	</div>
	<!--BEGIN RIGHT-->
	<div class="right">
		<h1><?php echo html_entity_decode($product['title']);?></h1>
		<h2><a href="<?php echo site_url($module.'/categories/'.$categorie['id'].'/'.$categorie['uri'].'/index'.$this->config->item('url_suffix_ext'));?>"><?php echo html_entity_decode($categorie['title']);?></a></h2>
		<div class="body"><?php echo $product['body'];?></div>
		<?php if(isset($products_attributes_values) && $products_attributes_values):?>
		<h2>Disponibilités</h2>
		<?php //pre_affiche($products_attributes_values);?>
		<?php foreach($products_attributes_values as $key => $value):?>
		<div class="attributes">
			<?php $attribut_name = '';?>
			<?php if($value && is_array($value)):?>
			<?php $i=1;foreach($value as $attribut):?>
			<?php if($attribut['aNAME'] && $attribut_name != $attribut['aNAME']):?>
			<?php $attribut_name = $attribut['aNAME']?>
			<h3><?php echo $attribut['aNAME'];?></h3>
			<?php endif;?>
			<?php if($attribut['color'] && $attribut['color'] != ''):?>
			<span class="attributes_colors" style="background-color:<?php echo $attribut['color'];?>"><?php echo html_entity_decode($attribut['avNAME']);?></span>
			<?php else:?>
			<?php
			if($attribut['attributes_values_id'] == 116) echo '<strong>Disponible en d\'autres couleurs</strong>';
			else echo html_entity_decode($attribut['avNAME']);
			?>
			<?php //pre_affiche($attribut);?>
			<?php endif;?>
			<?php endforeach;?>
			<?php endif;?>
		</div>

		<?php endforeach;?>
		<?php endif;?>
		<div class="price">
			<?php if(isset($special) && $special && $special['date_begin'] == '' && $special['date_end'] == '') :?>
			<strike><?php echo $this->tva->display_price($product['price'], '1', $product['tva']);?></strike> <?php echo $this->tva->display_price($special['new_price'], '1', $special['tva']);?>
			<?php elseif(isset($special) && $special && ($special['date_begin'] <= date('Y-m-d') && $special['date_end'] >= date('Y-m-d'))):?>
			<strike><?php echo $this->tva->display_price($product['price'], '1', $product['tva']);?></strike> <?php echo $this->tva->display_price($special['new_price'], '1', $special['tva']);?>
			<?php else :?>
			<?php echo $this->tva->display_price($product['price'], '1', $product['tva']);?>
			<?php endif;?>
		</div>
		<p class="btn_actions"><a href="<?php echo site_url('contact');?>">Nous contacter</a><?php if($this->session->userdata('redirect_uri_front')):?> - <a href="<?php echo site_url($this->session->userdata('redirect_uri_front'));?>">Retour</a><?php endif;?></p>
	</div>
	<!--END RIGHT-->
	<br class="clear"/>
	<?php //pre_affiche($products);?>
	<?php if(isset($products) && $products):?>
	<div id="products_to_products">
		<h3>Produits associés</h3>
		<?php echo $this->load->view('partials/products');?>
	</div>
	<br class="clear"/>
	<?php endif;?>
</div>
