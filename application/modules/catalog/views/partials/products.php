<ul class="listing_products">
	<?php $i = 1;foreach($products as $product):?>
	<?php if ($i % 5 != 0): $rowClass = '';else: $rowClass = 'last';endif;?>
	<li class="<?php echo $rowClass;?>">
		<span class="img">
			<a href="<?php echo site_url($module.'/products/'.$product['pID'].'/'.$product['pURI'].$this->config->item('url_suffix_ext'));?>">
				<?php if(isset($images[$product['pID']]) && is_file('./'.$this->config->item('medias_folder').'/images/'.$images[$product['pID']]['file'])):?>
				<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x96/'.$images[$product['pID']]['file']);?>" alt="<?php echo html_entity_decode($product['pTITLE']);?>" <?php echo $this->system->get_images_size('./'.$this->config->item('medias_folder').'/images/.cache/x96/'.$images[$product['pID']]['file']);?>/>
				<?php else:?>
				<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x96/default.jpg');?>" alt="<?php echo html_entity_decode($product['title']);?>" width="150" height="96"/>
				<?php endif;?>
			</a>
		</span>
		<h2 class="title">
			<a href="<?php echo site_url($module.'/products/'.$product['pID'].'/'.$product['pURI'].$this->config->item('url_suffix_ext'));?>"><?php echo html_entity_decode($product['pTITLE']);?></a>
		</h2>
		<span class="price">
			<?php if(isset($product['new_price']) && $product['new_price'] && $product['date_begin'] == '' && $product['date_end'] == '' && $product['sACTIVE'] == 1):?>
			<strike><?php echo $this->tva->display_price($product['price'], '1', $product['pTVA']);?></strike> <?php echo $this->tva->display_price($product['new_price'], '1', $product['sTVA']);?>
			<?php elseif(isset($product['new_price']) && $product['new_price'] && ($product['date_begin'] <= date('Y-m-d') && $product['date_end'] >= date('Y-m-d')) && $product['sACTIVE'] == 1):?>
			<strike><?php echo $this->tva->display_price($product['price'], '1', $product['pTVA']);?></strike> <?php echo $this->tva->display_price($product['new_price'], '1', $product['sTVA']);?>
			<?php else :?>
			<?php echo $this->tva->display_price($product['price'], '1', $product['pTVA']);?>
			<?php endif;?>
		</span>
	</li>
	<?php $i++;endforeach;?>
</ul>