<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Etape 3 : Droits des fichiers</h2>
	<form method="post" action="">
	<ul class="manage">
		<li><input type="submit" id="submit" name="submit" value="Etape 4" class="input_submit"/></li>
		<li><a href="<?php echo site_url($module.'/step2');?>">Etape 2</a></li>
	</ul>
	<fieldset>
	<?php
	$folders = array(
		'./'.$this->config->item('backup_folder'),
		'./'.$this->config->item('cache_folder'),
		'./'.$this->config->item('medias_folder'),
		'./'.$this->config->item('medias_folder').'/captcha',
		'./'.$this->config->item('medias_folder').'/images',
		'./'.$this->config->item('medias_folder').'/images/.cache',
		'./'.$this->config->item('medias_folder').'/swf',
		'./'.$this->config->item('medias_folder').'/tmp',
		'./'.$this->config->item('medias_folder').'/videos',
		'./'.APPPATH.'logs',
		'./'.APPPATH.'config/database.php',
		'./'.APPPATH.'views/theme1/img',
		'./'.APPPATH.'modules/pages/css/.cache',
		'./'.APPPATH.'modules/pages/language/fr/pages_lang.php',
		'./'.APPPATH.'modules/pages/language/en/pages_lang.php',
		'./'.APPPATH.'modules/contact/css/.cache',
		'./'.APPPATH.'modules/contact/language/fr/contact_lang.php',
		'./'.APPPATH.'modules/contact/language/en/contact_lang.php'
	);
	if($folders)
	{
		$error = false;
		echo "<ul>";
		foreach ($folders as $folder)
		{
			if(@chmod($folder, 0777))
			{
				echo "<li>Droit du dossier <strong>".str_replace('./', '', $folder)."</strong> modifié</li>";
			}
			else
			{
				$error = true;
				echo "<li><span style=\"color:#900900\"><strong>ERREUR:</strong> Impossible de modifier les droits du dossier ".str_replace('./', '', $folder)."</span> <strong>Merci de le faire manuellement.</strong></li>";
			}
		}
		echo "</ul>";
		if(!$error) echo '<input type="hidden" name="confirm" value="1"/>';
	}
	?>
	</fieldset>
</form>
