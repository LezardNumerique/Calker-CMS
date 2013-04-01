<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<fieldset>
	<label for="title"><?php echo $this->lang->line('label_title')?></label>
	<input name="title" id="title" class="input_text" value="<?php if($post['title']) echo $post['title'];else echo html_entity_decode($paragraph['title'])?>" maxlength="64"/>
	<label for="class"><?php echo $this->lang->line('label_class')?></label>
	<input name="class" id="class" class="input_text" value="<?php if($post['class']) echo $post['class'];else echo $paragraph['class']?>" maxlength="32"/>
	<label for="body"><?php echo $this->lang->line('label_body')?></label>
	<textarea name="body" id="body" class="input_textarea"><?php if($post['body']) echo $post['body'];else echo $paragraph['body'];?></textarea>
	<span class="required"><?php echo $this->lang->line('text_required');?></span>
</fieldset>