<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="categories">
	<div id="categories_right">
		<?php if(isset($categorie) && $categorie):?>
		<h1><?php echo html_entity_decode($categorie['title']);?></h1>
		<?php if(isset($products) && $products):?>
		<?php //pre_affiche($products);?>
		<?php //pre_affiche($images);?>
		<div class="pagersorter">
			<?php echo $this->load->view('partials/pager');?>
			<?php echo $this->load->view('partials/sort');?>
		</div>
		<?php echo $this->load->view('partials/products');?>
		<div class="pagersorter">
			<?php echo $this->load->view('partials/pager');?>
			<?php echo $this->load->view('partials/sort');?>
		</div>
		<?php else :?>
		<p class="no_data"><?php echo $this->lang->line('text_products_not_found');?></p>
		<?php endif;?>
		<?php else :?>
		<p class="no_data"><?php echo $this->lang->line('text_categorie_not_found');?></p>
		<?php endif;?>
	</div>
	<br class="clear"/>
</div>
