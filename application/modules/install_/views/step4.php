<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Etape 4 : Création des tables</h2>
	<form method="post" action="">
	<ul class="manage">
		<li><input type="submit" id="submit" name="submit" value="Finaliser" class="input_submit"/></li>
		<li><a href="<?php echo site_url($module.'/step3');?>">Etape 3</a></li>
	</ul>
	<?php if(isset($alerte) && $alerte):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<fieldset>
		Les tables suivantes vont être crées :
		<ul>
			<li>captcha</li>
			<li>contact</li>
			<li>contact_message</li>
			<li>groups</li>
			<li>languages</li>
			<li>medias</li>
			<li>modules</li>
			<li>navigation</li>
			<li>pages</li>
			<li>paragraphs</li>
			<li>paragraphs_types</li>
			<li>rights</li>
			<li>settings</li>
			<li>users</li>
		</ul>
	</fieldset>
</form>
