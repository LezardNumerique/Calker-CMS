<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Etape 1 : Base de données</h2>
	<form method="post" action="">
	<ul class="manage">
		<li><input type="submit" id="submit" name="submit" value="Etape 2" class="input_submit"/></li>
	</ul>
	<?php if(isset($alerte) && $alerte):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if($alerte = validation_errors()):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<fieldset>
		<label for="database_hostname">Serveur Sql :</label>
		<input type="text" id="database_hostname" name="database_hostname" value="<?php if($this->input->post('database_hostname')) echo $this->input->post('database_hostname');elseif($this->session->userdata('database_hostname')) echo $this->session->userdata('database_hostname');?>" class="input_text"/>
		<span class="required">* Champs obligatoire</span>
		<label for="database_name">Base de données :</label>
		<input type="text" id="database_name" name="database_name" value="<?php if($this->input->post('database_name')) echo $this->input->post('database_name');elseif($this->session->userdata('database_name')) echo $this->session->userdata('database_name');?>" class="input_text"/>
		<span class="required">* Champs obligatoire</span>
		<label for="database_username">Nom d'utilisateur :</label>
		<input type="text" id="database_username" name="database_username" value="<?php if($this->input->post('database_username')) echo $this->input->post('database_username');elseif($this->session->userdata('database_username')) echo $this->session->userdata('database_username');?>" class="input_text"/>
		<span class="required">* Champs obligatoire</span>
		<label for="database_password">Mot de passe :</label>
		<input type="password" id="database_password" name="database_password" value="<?php if($this->input->post('database_password')) echo $this->input->post('database_password');elseif($this->session->userdata('database_password')) echo $this->session->userdata('database_password');?>" class="input_text"/>
		<span class="required">* Champs obligatoire</span>
		<label for="database_password_confirm">Mot de passe confirmation :</label>
		<input type="password" id="database_password_confirm" name="database_password_confirm" value="<?php if($this->input->post('database_password_confirm')) echo $this->input->post('database_password_confirm');elseif($this->session->userdata('database_password')) echo $this->session->userdata('database_password');?>" class="input_text"/>
		<span class="required">* Champs obligatoire</span>
	</fieldset>
</form>
