<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="tabs">
	<ul>
		<li><a href="#one"><?php echo $this->lang->line('menu_content_1');?></a></li>
		<li><a href="#two"><?php echo $this->lang->line('menu_content_2');?></a></li>
		<li><a href="#three"><?php echo $this->lang->line('menu_content_3');?></a></li>				
	</ul>
	<fieldset>
		<div id="one">
			<label for="title"><?php echo $this->lang->line('label_title')?></label>
			<input name="title" id="title" class="input_text" value="<?php if($post['title']) echo $post['title'];else echo html_entity_decode($paragraph['title'])?>" maxlength="64"/>
			<label for="class"><?php echo $this->lang->line('label_class')?></label>
			<input name="class" id="class" class="input_text" value="<?php if($post['class']) echo $post['class'];else echo $paragraph['class']?>" maxlength="32"/>
			<label for="body"><?php echo $this->lang->line('label_body')?></label>
			<textarea name="body" id="body" class="input_textarea"><?php if($post['body']) echo $post['body'];else echo $paragraph['body'];?></textarea>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
		</div>
		<div id="two">
			<label for="title_2"><?php echo $this->lang->line('label_title')?></label>
			<input name="title_2" id="title_2" class="input_text" value="<?php if($post['title_2']) echo $post['title_2'];else echo html_entity_decode($paragraph['title_2'])?>" maxlength="64"/>
			<label for="class_2"><?php echo $this->lang->line('label_class')?></label>
			<input name="class_2" id="class" class="input_text" value="<?php if($post['class_2']) echo $post['class_2'];else echo $paragraph['class_2']?>" maxlength="32"/>
			<label for="body_2"><?php echo $this->lang->line('label_body')?></label>
			<textarea name="body_2" id="body_2" class="input_textarea"><?php if($post['body_2']) echo $post['body_2'];else echo $paragraph['body_2'];?></textarea>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
		</div>
		<div id="three">
			<label for="title_3"><?php echo $this->lang->line('label_title')?></label>
			<input name="title_3" id="title_3" class="input_text" value="<?php if($post['title_3']) echo $post['title_3'];else echo html_entity_decode($paragraph['title_3'])?>" maxlength="64"/>
			<label for="class_3"><?php echo $this->lang->line('label_class')?></label>
			<input name="class_3" id="class" class="input_text" value="<?php if($post['class_3']) echo $post['class_3'];else echo $paragraph['class_3']?>" maxlength="32"/>
			<label for="body_3"><?php echo $this->lang->line('label_body')?></label>
			<textarea name="body_3" id="body_3" class="input_textarea"><?php if($post['body_3']) echo $post['body_3'];else echo $paragraph['body_3'];?></textarea>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
		</div>
	</fieldset>
</div>