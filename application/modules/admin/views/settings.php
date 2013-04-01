<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_general_configuration');?></h2>
	<?php echo form_open($this->config->item('admin_folder').'/settings');?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
		</ul>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
		<?php endif;?>
		<?php if ($alerte = $this->session->flashdata('alert')):?>
		<p class="alerte alerte_closable" style="display:none;"><?php echo $alerte;?></p>
		<?php endif;?>
		<div id="tabs">
			<ul>
				<li><a href="#one"><?php echo $this->lang->line('menu_informations');?></a></li>
				<li><a href="#two"><?php echo $this->lang->line('menu_google_analytics');?></a></li>
				<li><a href="#three"><?php echo $this->lang->line('menu_smtp');?></a></li>
				<li><a href="#four"><?php echo $this->lang->line('menu_themes');?></a></li>
				<li><a href="#five"><?php echo $this->lang->line('menu_values');?></a></li>
			</ul>
			<fieldset>
				<div id="one">
					<div class="left">
						<label for="site_name"><?php echo $this->lang->line('label_site_name');?></label>
						<input type="text" name="site_name" value="<?php echo $this->system->site_name?>" id="site_name" class="input_text"/>
						<label for="site_adress"><?php echo $this->lang->line('label_adress');?></label>
						<input type="text" name="site_adress" value="<?php echo $this->system->site_adress?>" id="site_adress" class="input_text"/>
						<label for="site_adress_next"><?php echo $this->lang->line('label_adress_next');?></label>
						<input type="text" name="site_adress_next" value="<?php echo $this->system->site_adress_next?>" id="site_adress_next" class="input_text"/>
						<label for="site_post_code"><?php echo $this->lang->line('label_code_post');?></label>
						<input type="text" name="site_post_code" value="<?php echo $this->system->site_post_code?>" id="site_post_code" class="input_text"/>
						<label for="site_city"><?php echo $this->lang->line('label_city');?></label>
						<input type="text" name="site_city" value="<?php echo $this->system->site_city?>" id="site_city" class="input_text"/>
						<label for="site_country"><?php echo $this->lang->line('label_country');?></label>
						<input type="text" name="site_country" value="<?php echo $this->system->site_country?>" id="site_country" class="input_text"/>
						<label for="site_phone"><?php echo $this->lang->line('label_phone');?></label>
						<input type="text" name="site_phone" value="<?php echo $this->system->site_phone?>" id="site_phone" class="input_text"/>
						<label for="site_email"><?php echo $this->lang->line('label_site_email');?></label>
						<input type="text" name="site_email" value="<?php echo $this->system->site_email?>" id="site_email" class="input_text"/>
						<label for="site_schedule"><?php echo $this->lang->line('label_site_schedule');?></label>
						<textarea id="site_schedule" name="site_schedule" class="input_textarea" style="height:140px"><?php echo $this->system->site_schedule?></textarea>
						<label for="maintenance"><?php echo $this->lang->line('label_maintenance');?></label>
						<select id="maintenance" name="maintenance" class="input_select">
							<option <?php if ($this->system->maintenance == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->maintenance == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<?php if($this->user->root):?>
						<label for="debug"><?php echo $this->lang->line('label_debug');?></label>
						<select id="debug" name="debug" class="input_select">
							<option <?php if ($this->system->debug == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->debug == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<?php else :?>
						<input type="hidden" name="debug" value="<?php echo $this->system->debug?>"/>
						<?php endif;?>
						<?php if($this->user->root):?>
						<label for="cache"><?php echo $this->lang->line('label_cache');?></label>
						<select id="cache" name="cache" class="input_select">
							<option <?php if ($this->system->cache == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->cache == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<?php else :?>
						<input type="hidden" name="cache" value="<?php echo $this->system->cache?>"/>
						<?php endif;?>
						<?php if($this->user->root):?>
						<label for="cache_css"><?php echo $this->lang->line('label_cache_css');?></label>
						<select id="cache_css" name="cache_css" class="input_select">
							<option <?php if ($this->system->cache_css == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->cache_css == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<?php else :?>
						<input type="hidden" name="cache_css" value="<?php echo $this->system->cache_css?>"/>
						<?php endif;?>
						<?php if($this->user->root):?>
						<label for="ip_allow"><?php echo $this->lang->line('label_ip_allow');?></label>
						<input type="text" name="ip_allow" value="<?php echo $this->system->ip_allow?>" id="ip_allow" class="input_text"/>
						<?php else :?>
						<input type="hidden" name="ip_allow" value="<?php echo $this->system->ip_allow?>"/>
						<?php endif;?>
						<label for="meta_keywords"><?php echo $this->lang->line('label_meta_keywords');?></label>
						<input type="text" name="meta_keywords" value="<?php echo $this->system->meta_keywords?>" id="meta_keywords" class="input_text"/>
						<label for="meta_description"><?php echo $this->lang->line('label_meta_description');?></label>
						<input type="text" name="meta_description" value="<?php echo $this->system->meta_description?>" id="meta_description" class="input_text"/>
						<label for="meta_more"><?php echo $this->lang->line('label_meta_extra');?></label>
						<input type="text" name="meta_more" value="<?php echo $this->system->meta_more?>" id="meta_more" class="input_text"/>
					</div>
				</div>
				<div id="two">
					<div class="left">
						<label for="google_analytic_visits"><?php echo $this->lang->line('label_google_analytic_visits');?></label>
						<select id="google_analytic_visits" name="google_analytic_visits" class="input_select">
							<option <?php if ($this->system->google_analytic_visits == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->google_analytic_visits == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<label for="google_analytic_stats"><?php echo $this->lang->line('label_google_analytic_stats');?></label>
						<select id="google_analytic_stats" name="google_analytic_stats" class="input_select">
							<option <?php if ($this->system->google_analytic_stats == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->google_analytic_stats == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<?php if($this->user->root):?>
						<label for="google_analytic_ga_id"><?php echo $this->lang->line('label_google_ga_analytic');?></label>
						<input type="text" name="google_analytic_ga_id" value="<?php echo $this->system->google_analytic_ga_id?>" id="google_analytic_ga_id" class="input_text"/>
						<label for="google_analytic_ua_id"><?php echo $this->lang->line('label_google_ua_analytic');?></label>
						<input type="text" name="google_analytic_ua_id" value="<?php echo $this->system->google_analytic_ua_id?>" id="google_analytic_ua_id" class="input_text"/>
						<label for="google_analytics_email"><?php echo $this->lang->line('label_google_analytics_email');?></label>
						<input type="text" name="google_analytics_email" value="<?php echo $this->system->google_analytics_email?>" id="google_analytics_email" class="input_text"/>
						<label for="google_analytics_password"><?php echo $this->lang->line('label_google_analytics_password');?></label>
						<input type="password" name="google_analytics_password" value="<?php echo $this->system->google_analytics_password?>" id="google_analytics_password" class="input_text"/>
						<label for="google_analytic_domain"><?php echo $this->lang->line('label_google_analytic_domain');?></label>
						<input type="text" name="google_analytic_domain" value="<?php echo $this->system->google_analytic_domain?>" id="google_analytic_domain" class="input_text"/>
						<label for="google_analytic_code"><?php echo $this->lang->line('label_google_analytic_code');?></label>
						<textarea name="google_analytic_code" id="google_analytic_code" class="input_textarea" style="height:250px;"><?php echo $this->system->google_analytic_code?></textarea>
						<?php else :?>
						<input type="hidden" name="google_analytic_ga_id" value="<?php echo $this->system->google_analytic_ga_id?>"/>
						<input type="hidden" name="google_analytic_ua_id" value="<?php echo $this->system->google_analytic_ua_id?>"/>
						<input type="hidden" name="google_analytics_email" value="<?php echo $this->system->google_analytics_email?>"/>
						<input type="hidden" name="google_analytics_password" value="<?php echo $this->system->google_analytics_password?>"/>
						<input type="hidden" name="google_analytic_domain" value="<?php echo $this->system->google_analytic_domain?>"/>
						<input type="hidden" name="google_analytic_code" value="<?php echo $this->system->google_analytic_code?>"/>
						<?php endif;?>
					</div>
				</div>
				<div id="three">
					<div class="left">
						<label for="smtp_is"><?php echo $this->lang->line('label_smtp_is');?></label>
						<select id="smtp_is" name="smtp_is" class="input_select">
							<option <?php if ($this->system->smtp_is == 1):?>selected='selected' <?php endif;?>value="1"><?php echo $this->lang->line('option_yes');?></option>
							<option <?php if ($this->system->smtp_is == 0):?>selected='selected' <?php endif;?>value="0"><?php echo $this->lang->line('option_no');?></option>
						</select>
						<?php if($this->user->root):?>
						<label for="smtp_host"><?php echo $this->lang->line('label_smtp_host');?></label>
						<input type="text" name="smtp_host" value="<?php echo $this->system->smtp_host?>" id="smtp_host" class="input_text"/>
						<label for="smtp_username"><?php echo $this->lang->line('label_smtp_username');?></label>
						<input type="text" name="smtp_username" value="<?php echo $this->system->smtp_username?>" id="smtp_username" class="input_text"/>
						<label for="smtp_password"><?php echo $this->lang->line('label_smtp_password');?></label>
						<input type="password" name="smtp_password" value="<?php echo $this->system->smtp_password?>" id="smtp_password" class="input_text"/>
						<label for="smtp_port"><?php echo $this->lang->line('label_smtp_port');?></label>
						<input type="text" name="smtp_port" value="<?php echo $this->system->smtp_port?>" id="smtp_port" class="input_text"/>
						<?php else :?>
						<input type="hidden" name="smtp_host" value="<?php echo $this->system->smtp_host?>"/>
						<input type="hidden" name="smtp_username" value="<?php echo $this->system->smtp_username?>"/>
						<input type="hidden" name="smtp_password" value="<?php echo $this->system->smtp_password?>"/>
						<input type="hidden" name="smtp_port" value="<?php echo $this->system->smtp_port?>"/>
						<?php endif;?>
					</div>
				</div>
				<div id="four">
					<script type="text/javascript">
					$(document).ready(function() {
						$(".change_theme_color").click(function() {
							$("#ResultStylesheet").empty();
							$("#ResultStylesheet").append('<div class="loading"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/loading.gif');?>" alt="LOADING"></div>');
							var theme = $(this).attr("data-id");
							var dataString = 'theme='+theme;
							$.ajax({
								data: dataString,
								type: "POST",
								url: '<?php echo site_url($this->config->item('admin_folder').'/AjaxChangeTheme');?>',
								cache: false,
								success: function(html){
									$("#ResultStylesheet").html(html);
								}
							});
						});
					});
					</script>
					<h2><?php echo $this->lang->line('title_design');?></h2>
					<?php foreach ($themes as $theme):?>
					<?php if (is_file(APPPATH.'views/'.$theme.'/thumb.jpg')):?>
					<div class="themes">
						<label for="theme_<?php echo $theme;?>" style="float:none;"><img src="<?php echo site_url(APPPATH.'views/'.$theme.'/thumb.jpg');?>" title="<?php echo ucwords(str_replace('_', ' ', $theme))?>" alt="<?php echo ucwords(str_replace('_', ' ', $theme))?>" width="200" height="150" class="change_theme_color" data-id="<?php echo $theme;?>"/></label>
						<input type="radio" name="theme" id="theme_<?php echo $theme;?>" data-id="<?php echo $theme;?>" value="<?php echo $theme;?>"<?php if ($theme == $this->layout->theme):?> checked="checked"<?php endif;?>/>
					</div>
					<?php endif;?>
					<?php endforeach;?>
					<br class="clear"/>
					<h2><?php echo $this->lang->line('title_declinaison');?></h2>
					<div id="ResultStylesheet">
						<?php $i=1;foreach ($stylesheets as $stylesheet):?>
						<div class="theme_colors">
							<label for="stylesheet_<?php echo $i;?>" style="background:<?php echo $stylesheet['hexa'];?>"><?php echo ucwords(str_replace('_', ' ', $stylesheet['color']))?></label>
							<input type="radio" name="stylesheet" id="stylesheet_<?php echo $i;?>" value="<?php echo $stylesheet['file']?>"<?php if ($stylesheet['file'] == $this->system->stylesheet):?> checked="checked"<?php endif;?>/>
						</div>
						<?php $i++;endforeach;?>
						<br class="clear"/>
					</div>
					<h2><?php echo $this->lang->line('title_logo');?></h2>
					<label for="image"><?php echo $this->lang->line('label_logo');?></label>
					<input type="file" name="image" id="image" class="input_file"/><br class="clear"/>
					<?php if($logo = $this->system->logo) : ?>
					<br />
					<img src="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/img/'.$logo);?>" alt="<?php echo $this->lang->line('text_logo');?>"/>
					<?php endif;?>
				</div>
				<div id="five">
					<div class="left">
						<label for="per_page"><?php echo $this->lang->line('label_max_line');?></label>
						<input type="text" name="per_page" value="<?php echo $this->system->per_page?>" id="per_page" class="input_text"/>
						<label for="num_links"><?php echo $this->lang->line('label_num_links');?></label>
						<input type="text" name="num_links" value="<?php echo $this->system->num_links?>" id="num_links" class="input_text"/>
						<label for="per_captcha"><?php echo $this->lang->line('label_per_captcha');?></label>
						<input type="text" name="per_captcha" value="<?php echo $this->system->per_captcha?>" id="per_captcha" class="input_text"/>
					</div>
				</div>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
		$("input.input_file").filestyle({
			image: BASE_URI+APPPATH+"views/"+ADMIN_THEME+"/img/upload_file.gif",
			imageheight : 40,
			imagewidth : 78,
			width : 240
		});
	});
	</script>
</div>
<!-- [Main] end -->
