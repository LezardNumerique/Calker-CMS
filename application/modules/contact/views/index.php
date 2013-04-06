<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="contact">
	<h1><?php echo $this->lang->line('title_contact');?></h1>
	<script type="text/javascript">
	$(document).ready(function() {
		$("#submit_contact").click(function() {
			var bReturn = true;
			<?php if($this->settings['active_field_firstname'] == 1):?>
			$("input#contact_firstname").css({border: ""});
			$("label[for=\"contact_firstname\"]").css({color: ""});
			if ( jQuery.trim($("input#contact_firstname").val()).length==0 ) {
				$("input#contact_firstname").css({border: "1px dotted #ffffff"});
				$("label[for=\"contact_firstname\"]").css({color: "#ffffff"});
				bReturn = false;
			}
			<?php endif;?>
			<?php if($this->settings['active_field_lastname'] == 1):?>
			$("input#contact_lastname").css({border: ""});
			$("label[for=\"contact_lastname\"]").css({color: ""});
			if ( jQuery.trim($("input#contact_lastname").val()).length==0 ) {
				$("input#contact_lastname").css({border: "1px dotted #ffffff"});
				$("label[for=\"contact_lastname\"]").css({color: "#ffffff"});
				bReturn = false;
			}
			<?php endif;?>
			<?php if($this->settings['active_field_phone'] == 1):?>
			$("input#contact_phone").css({border: ""});
			$("label[for=\"contact_phone\"]").css({color: ""});
			if ( jQuery.trim($("input#contact_phone").val()).length==0 ) {
				$("input#contact_phone").css({border: "1px dotted #ffffff"});
				$("label[for=\"contact_phone\"]").css({color: "#ffffff"});
				bReturn = false;
			}
			<?php endif;?>
			$("input#contact_email").css({border: ""});
			$("label[for=\"contact_email\"]").css({color: ""});
			if ( jQuery.trim($("input#contact_email").val()).length==0 ) {
				$("input#contact_email").css({border: "1px dotted #ffffff"});
				$("label[for=\"contact_email\"]").css({color: "#ffffff"});
				bReturn = false;
			}
			<?php if($this->settings['active_field_message'] == 1):?>
			$("textarea#contact_message").css({border: ""});
			$("label[for=\"contact_message\"]").css({color: ""});
			if ( jQuery.trim($("textarea#contact_message").val()).length==0 ) {
				$("textarea#contact_message").css({border: "1px dotted #ffffff"});
				$("label[for=\"contact_message\"]").css({color: "#ffffff"});
				bReturn = false;
			}
			<?php endif;?>
			$("input#contact_captcha").css({border: ""});
			$("label[for=\"contact_captcha\"]").css({color: ""});
			if ( jQuery.trim($("input#contact_captcha").val()).length==0 ) {
				$("input#contact_captcha").css({border: "1px dotted #ffffff"});
				$("label[for=\"contact_captcha\"]").css({color: "#ffffff"});
				bReturn = false;
			}
			return bReturn;
		});
	});
	</script>
	<?php if($this->settings['active_coord'] == 1):?>
	<p class="adress">
		<?php if($this->system->site_name) echo $this->system->site_name.'<br />';?>
		<?php if($this->system->site_adress) echo $this->system->site_adress.'<br />';?>
		<?php if($this->system->site_adress_next) echo $this->system->site_adress_next.'<br />';?>
		<?php if($this->system->site_post_code) echo $this->system->site_post_code;?> <?php if(isset($this->system->site_city)) echo $this->system->site_city;?><br /><br />
		<?php if($this->system->site_phone) echo '<strong>'.$this->lang->line('title_phone').'</strong>'?> <?php if($this->system->site_phone) echo format_phone($this->system->site_phone);?><br />
		<?php if($this->system->site_email) echo '<strong>'.$this->lang->line('title_mailto').'</strong>'?> <?php if($this->system->site_email) :?><a href="mailto:<?php echo $this->system->site_email;?>"><?php echo $this->system->site_email;?></a><br /><br /><?php endif;?>
		<?php if($this->system->site_schedule) echo '<strong>'.$this->lang->line('title_schedule').'</strong>'?><br /> <?php if($this->system->site_schedule) :?> <?php echo nl2br($this->system->site_schedule);?><?php endif;?>
	</p>
	<?php endif;?>
	<?php if($this->settings['active_qrcode'] == 1):?>
	<div id="qrcode"><?php echo $qrcode;?></div>
	<br class="clear"/>
	<?php endif;?>
	<?php echo validation_errors();?>
	<?php if($alerte = validation_errors()):?>
		<p class="alerte closable"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if($this->settings['active_form'] == 1):?>
	<h2><?php echo $this->lang->line('title_form_to_mail')?></h2>
	<?php echo form_open($this->template['module'], array('class' => 'form_contact', 'id' => 'form_contact'));?>
		<fieldset>
			<?php if($this->settings['active_field_firstname'] == 1):?>
			<div>
				<label for="contact_firstname"><?php echo $this->lang->line('label_firstname');?></label>
				<input type="text" name="contact_firstname" id="contact_firstname" value="<?php echo set_value('contact_firstname');?>" class="input_text" maxlength="64"/>
				<span class="required"><?php echo $this->lang->line('text_required');?></span>
			</div>
			<?php endif;?>
			<?php if($this->settings['active_field_lastname'] == 1):?>
			<div>
				<label for="contact_lastname"><?php echo $this->lang->line('label_lastname');?></label>
				<input type="text" name="contact_lastname" id="contact_lastname" value="<?php echo set_value('contact_lastname');?>" class="input_text" maxlength="64"/>
				<span class="required"><?php echo $this->lang->line('text_required');?></span>
			</div>
			<?php endif;?>
			<div>
				<label for="contact_email"><?php echo $this->lang->line('label_email');?></label>
				<input type="text" name="contact_email" id="contact_email" value="<?php echo set_value('contact_email');?>" class="input_text" maxlength="128"/>
				<span class="required"><?php echo $this->lang->line('text_required');?></span>
			</div>
			<?php if($this->settings['active_field_phone'] == 1):?>
			<div>
				<label for="contact_phone"><?php echo $this->lang->line('label_phone');?></label>
				<input type="text" name="contact_phone" id="contact_phone" value="<?php echo set_value('contact_phone');?>" class="input_text" maxlength="16"/>
				<span class="required"><?php echo $this->lang->line('text_required');?></span>
			</div>
			<?php endif;?>
			<?php if($this->settings['active_field_message'] == 1):?>
			<div>
				<label for="contact_message"><?php echo $this->lang->line('label_message');?></label>
				<textarea name="contact_message" id="contact_message" rows="5" cols="40" class="input_textarea" maxlength="255"><?php echo set_value('contact_message');?></textarea>
				<span class="required"><?php echo $this->lang->line('text_required');?></span>
			</div>
			<?php endif;?>
			<div>
				<label for="contact_captcha"><?php echo $this->lang->line('label_captcha');?></label>
				<input type="text" name="contact_captcha" id="contact_captcha" value="" class="input_text" maxlength="<?php echo $this->system->per_captcha;?>"/>
				<span class="required"><?php echo $this->lang->line('text_required');?></span>
				<span class="captcha"><?php echo $captcha;?></span>
			</div>
		</fieldset>
		<p><input type="submit" name="submit_contact" id="submit_contact"  value="<?php echo $this->lang->line('btn_send_message')?>" class="input_submit"/></p>
	</form>
	<?php endif;?>
	<?php if($this->settings['active_map'] == 1):?>
	<script type="text/javascript">
		var geocoder;
		var map;
		function initialize() {
			geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(-34.397, 150.644);
			var myOptions = {
				scrollwheel: false,
				zoom: 15,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		}
		function codeAddress() {
			var address = '<?php echo $this->system->site_adress;?> <?php echo $this->system->site_post_code;?> <?php echo $this->system->site_city;?>';
			geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
				} else {
					alert("<?php echo $this->lang->line('alert_geocode');?>" + status);
				}
			});
		}
		$(document).ready(function(){
			initialize();
			codeAddress();
		});
	</script>
	<br class="clear"/>
	<h2><?php echo $this->lang->line('title_google_map')?></h2>
	<div id="map_canvas" style="width: 100%; height: 500px"></div>
	<?php endif;?>
</div>