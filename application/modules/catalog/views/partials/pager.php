<div class="pager_left">
		<?php echo $total;?> <?php echo $this->lang->line('text_total_products');?>
</div>
<?php if(isset($pager) && $pager):?>
<div class="pager_right">
		<?php echo $pager?>
</div>
<?php endif;?>
