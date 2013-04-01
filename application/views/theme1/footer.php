<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
		<div class="clear"></div>
		</div>
	</div>
	<div id="footer_content">
		<div id="footer_border"></div>
		<div id="footer">
			<div id="sidebarbottom">
				<?php if ($data['blockCategTree'] = $this->navigation->getTree(8)) :?>
				<?php $this->load->view($this->system->theme.'/sidebar', $data);?>
				<?php endif;?>
			</div>
			<p class="copyright">Copyright <?php echo date('Y');?> <?php echo $this->system->site_name;?></p>
			<p class="adress"><?php echo $this->system->site_name;?> - <?php echo $this->system->site_adress;?> - <?php if($this->system->site_adress_next) echo $this->system->site_adress_next.' - ';?><?php echo $this->system->site_post_code;?> <?php echo $this->system->site_city;?> - <?php echo $this->system->site_country;?> - <?php echo $this->lang->line('footer_phone');?> <?php echo $this->system->site_phone;?></p>
			<p><?php echo $this->lang->line('footer_display');?> {elapsed_time} <?php echo $this->lang->line('footer_seconds');?> | <a href="http://www.lezard-numerique.net"><?php echo $this->lang->line('footer_seo');?></a><span id="top_link"> | <a href="#header_content"><?php echo $this->lang->line('footer_top');?></a></span></p>
		</div>
	</div>
</body>
</html>