<h3><?php echo $this->lang->line('title_google_map');?></h3>
<div id="google_map_dialog" class="ui-helper-hidden">
	<?php if($this->system->site_name) echo $this->system->site_name.'<br />';?>
	<?php if($this->system->site_adress) echo $this->system->site_adress.'<br />';?>
	<?php if($this->system->site_adress_next) echo $this->system->site_adress_next.'<br />';?>
	<?php if($this->system->site_post_code) echo $this->system->site_post_code;?> <?php if($this->system->site_city) echo $this->system->site_city;?><br />
	<?php if($this->system->site_country) echo $this->system->site_country.'<br />';?>
	<?php if($this->system->site_phone) echo $this->lang->line('label_phone');?> <?php if($this->system->site_phone) echo format_phone($this->system->site_phone).'<br />';?>
	<?php if($this->system->site_email) echo $this->lang->line('label_email');?> <?php if($this->system->site_email) echo format_phone($this->system->site_email);?>
	<div id="qrcode"><?php if(isset($qrcode)) echo $qrcode;?></div>
</div>
<div id="google_map" style="width:100%;height:243px"></div>