<?php if(isset($categories) && $categories):?>
<div id="box_featured_categories">
	<ul>
		<?php $i=1;$count_categories = count($categories);foreach($categories as $categorie):?>
		<li <?php if($count_categories == $i):?>class="last"<?php endif;?>>
			<h2><img src="<?php echo site_url('medias/images/258x112/'.$categorie['file']);?>" alt="<?php echo html_entity_decode($categorie['title']);?>" width="258" height="112"/></h2>
			<h3><a href="<?php echo site_url('catalog/categories/'.$categorie['id'].'/'.$categorie['uri'].'/index'.$this->config->item('url_suffix_ext'));?>"><?php echo html_entity_decode($categorie['title']);?></a></h3>
			<span class="body"><?php echo $categorie['body'];?></span>
		</li>
		<?php $i++;endforeach;?>
	</ul>
	<br class="clear"/>
</div>
<?php endif;?>