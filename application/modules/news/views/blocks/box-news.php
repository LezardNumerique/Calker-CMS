<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if (isset($news) && $news) : ?>
<div class="box_news">
	<script type="text/javascript">
		$(window).load(function() {
			$('.flexslider').flexslider({
				animation: "slide",
				directionNav : false
			});
		});
	</script>
	<div class="flexslider">
		<?php if(!$this->user->liveView):?><h2><?php echo $this->lang->line('title_news');?></h2><?php endif;?>
		<ul class="slides">
			<?php foreach ($news as $new): ?>
			<?php
			$params['where'] = array('src_id' => $new['id'], 'module' => 'news');
			$uri = ((substr($new['uri'], 0, 7) == "http://") || (substr($new['uri'], 0, 8) == "https://") | (substr($new['uri'], 0, 6) == "ftp://") || (substr($new['uri'], 0, 7) == "mailto:"))? $new['uri']: site_url('news/view/'.$new['id'].'/'.$new['uri']);
			$uri_target = ((substr($new['uri'], 0, 7) == "http://") || (substr($new['uri'], 0, 8) == "https://") || (substr($new['uri'], 0, 6) == "ftp://") || (substr($new['uri'], 0, 7) == "mailto:"))? ' onclick="window.open(this.href); return false;"': '';
			?>
			<li <?php if(!$this->user->liveView):?>onclick="window.location='<?php echo $uri?>';"<?php endif;?>>
				<?php if($this->user->liveView):?>
				<div class="box_live_view">
					<span><?php echo $this->lang->line('title_news');?></span>
					<ul>
						<li>
							<?php if ($new['active'] == 1) :?>
							<a href="<?php echo site_url($this->config->item('admin_folder').'/news/flagLiveView/'.$new['id'].'/'.$new['active']);?>" title="<?php echo $this->lang->line('btn_desactivate_news');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate');?>"/></a>
							<?php else :?>
							<a href="<?php echo site_url($this->config->item('admin_folder').'/news/flagLiveView/'.$new['id'].'/'.$new['active']);?>" title="<?php echo $this->lang->line('btn_activate_news');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/status_red.png');?>" alt="<?php echo $this->lang->line('btn_activate');?>"/></a>
							<?php endif;?>
						</li>
						<li>
							<a href="<?php echo site_url($this->config->item('admin_folder').'/news/createLiveView');?>" title="<?php echo $this->lang->line('btn_create_news');?>" data-title="<?php echo $this->lang->line('btn_create_news');?>" class="dialog tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/create.png');?>" alt="<?php echo $this->lang->line('btn_create');?>" width="16px" height="16px"/></a>
						</li>
						<li>
							<a href="<?php echo site_url($this->config->item('admin_folder').'/news/editLiveView/'.$new['id']);?>" title="<?php echo $this->lang->line('btn_edit_news');?>" data-title="<?php echo $this->lang->line('btn_edit_news');?>" class="dialog tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/edit.png');?>" alt="<?php echo $this->lang->line('btn_edit');?>" width="16px" height="16px"/></a></li>
						<li>
							<a href="<?php echo site_url($this->config->item('admin_folder').'/news/deleteLiveView/'.$new['id']);?>" title="<?php echo $this->lang->line('btn_delete_news');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/delete.png');?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
						</li>
					</ul>
				</div>
				<?php endif;?>
				<div class="box">
					<a href="<?php echo $uri?>" title="<?php echo html_entity_decode($new['title'])?>" class="img" <?php echo $uri_target;?>>
					<?php if(isset($images[$new['id']]) && $images[$new['id']] && is_readable('./'.$this->config->item('medias_folder').'/images/'.$images[$new['id']]['file']) && is_file('./'.$this->config->item('medias_folder').'/images/'.$images[$new['id']]['file'])):?>
					<img src="<?php echo site_url($this->config->item('medias_folder').'/images/155/'.$images[$new['id']]['file']);?>" alt="<?php echo $new['title'];?>"/>
					<?php else :?>
					<img src="<?php echo site_url($this->config->item('medias_folder').'/images/155/default.jpg');?>" alt="<?php echo $new['title'];?>"/>
					<?php endif;?>
					</a>
					<div class="body">
						<h3>
							<a href="<?php echo $uri?>" <?php echo $uri_target;?>><?php echo html_entity_decode($new['title'])?></a>
						</h3>
						<p class="date"><?php echo date ('l d F Y', $new['date_added']);?></p>
						<?php echo character_limiter($new['body'], $news_settings['substr_home_news']);?>
					</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif;?>
</div>
