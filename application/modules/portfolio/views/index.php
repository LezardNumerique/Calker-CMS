<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<?php //pre_affiche($medias);?>
<h1><?php echo $title;?></h1>
<?php if(isset($categories_children) && $categories_children):?>
<?php foreach($categories_children as $categorie_children):?>
<a href="<?php echo site_url($module.'/categories/'.$categorie_children['id'].'/'.$categorie_children['uri'].'/index'.$this->config->item('url_suffix_ext'));?>"><?php echo $categorie_children['title'];?></a><br />
<?php endforeach;?>
<?php endif;?>
<?php if (isset($medias) && $medias) :?>
<ul class="listing_portfolio">
	<?php $i = 1;$count_medias = count($medias);foreach($medias as $media): ?>
	<?php if ($i % 4 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
	<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file'])):?>
	<li class="<?php echo $rowClass;?>">
		<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file'])):?>
		<div class="img" style="background:url('<?php echo site_url($this->config->item('medias_folder').'/images/x'.$images_sizes['height'].'/'.$media['file']);?>') no-repeat center center;">
			<a href="<?php echo site_url($this->config->item('medias_folder').'/images/x'.$images_sizes['height'].'/'.$media['file']);?>" class="colorbox_1"><?php echo $this->lang->line('btn_zoom');?></a>
		</div>
		<?php endif;?>
		<?php if($media['mTITLE'] && is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file'])):?>
		<h2><a href="<?php echo site_url($this->config->item('medias_folder').'/images/x'.$images_sizes['height'].'/'.$media['file']);?>" class="colorbox_2"><?php echo $media['mTITLE'];?></a></h2>
		<?php endif;?>
		<?php if($media['legend']):?>
		<div class="legend"><?php echo $media['legend'];?></div>
		<?php endif;?>
		<h3><?php echo $this->lang->line('title_reference');?> : #000<?php echo $media['medias_id'];?></h3>
		<?php if(!isset($categories_id)):?>
		<div class="categories">
		<?php echo $this->lang->line('title_categories');?> : <a href="<?php echo site_url($module.'/categories/'.$media['categories_id_default'].'/'.$media['uri'].'/index'.$this->config->item('url_suffix_ext'));?>"><?php echo $media['cTITLE'];?></a>
		</div>
		<?php endif;?>
		<?php if($media['mBODY']):?>
		<div class="body">
		<?php echo character_limiter($media['mBODY'], $this->portfolio->settings['substr_body_portfolio']);?>
		</div>
		<?php endif;?>
	</li>
	<?php endif;?>
	<?php $i++; endforeach;?>
</ul>
<script>
$(document).ready(function(){
	$(".colorbox_1").colorbox({rel:'colorbox_1', slideshow:true, speed:800, slideshowSpeed:8000, title: function(){
		$(this).attr('rel');
	}});
	$(".colorbox_2").colorbox({rel:'colorbox_2', slideshow:true, speed:800, slideshowSpeed:8000, title: function(){
		$(this).attr('rel');
	}});
});
</script>
<div class="pager">
	<div class="pager_left">
		<?php echo $total_medias;?> <?php echo $this->lang->line('text_total_medias');?>
	</div>
	<?php if(isset($pager) && $pager):?>
	<div class="pager_right">
		<?php echo $pager?>
	</div>
	<?php endif;?>
</div>
<?php else : ?>
<p class="no_data"><?php echo $this->lang->line('medias_not_found');?></p>
<?php endif; ?>