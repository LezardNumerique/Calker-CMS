<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if (isset($medias) && $medias) : ?>
<?php //pre_affiche($medias);?>
<ul class="box_portfolio">
	<?php foreach ($medias as $media): ?>
	<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file'])):?>
	<li>
		<a href="<?php echo site_url('portfolio/'.$media['categories_id_default'].'/'.$media['cURI'].'/index'.$this->config->item('url_suffix_ext'));?>" title="<?php echo $media['title'];?>"><img src="<?php echo site_url($this->config->item('medias_folder').'/images/100x/'.$media['file']);?>" alt="<?php echo $media['title'];?>"/></a>
	</li>
	<?php endif;?>
	<?php endforeach; ?>
</ul>
<?php endif;?>
