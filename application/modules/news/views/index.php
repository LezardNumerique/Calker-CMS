<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if($this->user->liveView):?>
<script src="<?php echo site_url(APPPATH.'views/assets/js/live_view.js?v='.mktime());?>" type="text/javascript"></script>
<?php endif;?>
<div id="dialog"></div>
<div id="listing_new">
	<?php //pre_affiche($news);?>
	<h1><?php echo $title;?></h1>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if($alerte = $this->session->flashdata('alerte')):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if (isset($news) && $news) :?>
	<ul class="listing_news">
		<?php $i = 1;$count_news = count($news);foreach($news as $new): ?>
		<?php if ($i % 4 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
		<?php $data['new'] = $new;?>
		<li class="<?php echo $rowClass;?>">
			<?php if(!$this->user->liveView):?>
			<h2><a href="<?php echo site_url($this->language->get_uri_language().$module.'/view/'.$new['uri'].'/'.$new['id']);?>"><?php echo $new['title'];?></a></h2>
			<?php endif;?>
			<?php $this->load->view('partials/live-view', $data);?>
			<div class="parag">
				<div class="img">
					<a href="<?php echo site_url($this->language->get_uri_language().$module.'/view/'.$new['uri'].'/'.$new['id']);?>">
						<?php if(isset($images[$new['id']]) && $images[$new['id']] && is_readable('./'.$this->config->item('medias_folder').'/images/'.$images[$new['id']]['file']) && is_file('./'.$this->config->item('medias_folder').'/images/'.$images[$new['id']]['file'])):?>
						<img src="<?php echo site_url($this->config->item('medias_folder').'/images/'.$images_sizes['width'].'x'.$images_sizes['height'].'/'.$images[$new['id']]['file']);?>" alt="<?php echo $new['title'];?>" width="<?php echo $images_sizes['width'];?>" height="<?php echo $images_sizes['height'];?>"/>
						<?php else :?>
						<img src="<?php echo site_url($this->config->item('medias_folder').'/images/'.$images_sizes['width'].'x'.$images_sizes['height'].'/default.jpg');?>" alt="<?php echo $new['title'];?>" width="<?php echo $images_sizes['width'];?>" height="<?php echo $images_sizes['height'];?>"/>
						<?php endif;?>
					</a>
				</div>
				<div class="body">
					<h3><?php echo date ('l d F Y', $new['date_added']);?></h3>
					<p><?php echo character_limiter($new['body'], $this->news->settings['substr_listing_news']);?></p>
					<p class="more"><a href="<?php echo site_url($this->language->get_uri_language().$module.'/view/'.$new['uri'].'/'.$new['id']);?>"><?php echo $this->lang->line('btn_more');?></a></p>
				</div>
			</div>
		</li>
		<?php $i++; endforeach;?>
	</ul>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total_news;?> <?php echo $this->lang->line('text_total_news');?>
		</div>
		<?php if(isset($pager) && $pager):?>
		<div class="pager_right">
			<?php echo $pager?>
		</div>
		<?php endif;?>
	</div>
	<?php else : ?>
	<p class="no_data"><?php echo $this->lang->line('news_not_found');?></p>
	<?php endif; ?>
</div>