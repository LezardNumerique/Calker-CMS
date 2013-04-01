<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<form method="post" action="<?php echo site_url('catalog/search');?>" id="form_search">
	<input type="text" name="search_catalog" id="search_catalog" value="<?php echo $this->lang->line('values_search')?>" class="input_text" onfocus="this.value=''"/>
	<input type="submit" name="submit_search_catalog" id="submit_search_catalog" value="Rechercher" class="input_text"/>
</form>