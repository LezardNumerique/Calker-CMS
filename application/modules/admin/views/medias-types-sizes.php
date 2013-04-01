<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<?php if(isset($medias_sizes) && $medias_sizes):?>
<?php //pre_affiche($medias_sizes);?>
<table class="table_list" id="table_sort_news">
	<thead>
		<tr>
			<th width="10%" class="center">#</th>
			<th width="30%"><?php echo $this->lang->line('td_name');?></th>
			<th width="30%" class="right"><?php echo $this->lang->line('td_width');?></th>
			<th width="30%" class="right"><?php echo $this->lang->line('td_height');?></th>
		</tr>
	</thead>
	<tbody id="sortable_news">		
		<?php $i=1;foreach($medias_sizes as $media_size):?>
		<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
		<tr class="<?php echo $rowClass?>">
			<td class="center"><?php echo $i;?></td>
			<td><?php echo $media_size['name'];?></td>
			<td class="right"><?php echo $media_size['width'];?> px</td>
			<td class="right"><?php echo $media_size['height'];?> px</td>
		</tr>
		<?php $i++;endforeach;?>		
	</tbody>
</table>
<?php endif;?>
<!-- [Main] end -->