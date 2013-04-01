<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Etape 2 : Compte Administrateur</h2>
	<?php echo form_open('');?>	
		<ul class="manage">
			<li><input type="submit" id="submit" name="submit" value="Etape 3" class="input_submit"/></li>
			<li><a href="<?php echo site_url($module.'/step1');?>">Etape 1</a></li>
		</ul>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<fieldset>
			<label for="admin_email">Email :</label>
			<input type="text" id="admin_email" name="admin_email" value="<?php if($this->input->post('admin_email')) echo $this->input->post('admin_email');elseif($this->session->userdata('admin_email')) echo $this->session->userdata('admin_email');?>" class="input_text"/>
			<span class="required">* Champs obligatoire</span>
			<label for="admin_password">Mot de passe :</label>
			<input type="password" id="admin_password" name="admin_password" value="<?php if($this->input->post('admin_password')) echo $this->input->post('admin_password');elseif($this->session->userdata('admin_password')) echo $this->session->userdata('admin_password');?>" class="input_text"/>
			<span class="required">* Champs obligatoire</span>
			<label for="admin_password_confirm">Mot de passe confirmation :</label>
			<input type="password" id="admin_password_confirm" name="admin_password_confirm" value="<?php if($this->input->post('admin_password_confirm')) echo $this->input->post('admin_password_confirm');elseif($this->session->userdata('admin_password')) echo $this->session->userdata('admin_password');?>" class="input_text"/>
			<span class="required">* Champs obligatoire</span>
		</fieldset>
	</form>
