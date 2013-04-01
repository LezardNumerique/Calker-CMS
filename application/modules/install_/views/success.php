<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Succès : CALKER CMS a bien été installé</h2>
<form method="get" action="<?php echo site_url('');?>">
	<p class="alerte">ATTENTION : Vous devez impérativement supprimer le dossier "install" dans <?php echo APPPATH.'modules';?></p>
	<fieldset>
		<p class="actions">
			<input type="submit" class="input_submit blue" value="Voir le Backoffice" name="" onclick="window.open('<?php echo site_url($this->config->item('admin_folder'));?>'); return false;">
			<input type="submit" class="input_submit green" value="Voir le Front" name="" onclick="window.open('<?php echo site_url('');?>'); return false;">
		</p>
	</fieldset>
</form>
