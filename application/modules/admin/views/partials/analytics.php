<?php if(isset($referrers) && is_array($referrers)): ?>
<div class="dashboard_left analytics">
	<h3><?php echo $this->lang->line('title_analytics_websites_referrers');?></h3>
	<h4><?php echo $this->lang->line('text_analytics_to');?> <?php echo $referrers['summary']->startDate?> <?php echo $this->lang->line('text_analytics_at');?> <?php echo $referrers['summary']->endDate;?></h4>
	<table class="table_list">
		<thead>
			<tr>
				<th width="40%"><?php echo $this->lang->line('text_analytics_websites');?> (<?php echo $referrers['summary']->totalResults?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_visits');?> (<?php echo $referrers['summary']->metrics->visits?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_pages_views');?> (<?php echo $referrers['summary']->metrics->pageviews?>)</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;foreach($referrers as $key => $ref): if ($key != 'summary'):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td><?php echo $key?></td>
				<td><?php echo $ref->visits?></td>
				<td><?php echo $ref->pageviews?></td>
			</tr>
			<?php endif;$i++;endforeach ?>
		</tbody>
	</table>
</div>
<?php endif;?>

<?php if(isset($cities) && is_array($cities)): ?>
<div class="dashboard_left analytics last">
	<h3><?php echo $this->lang->line('title_analytics_visit_by_city');?></h3>
	<h4><?php echo $this->lang->line('text_analytics_to');?> <?php echo $cities['summary']->startDate?> <?php echo $this->lang->line('text_analytics_at');?> <?php echo $cities['summary']->endDate;?></h4>
	<table class="table_list">
		<thead>
			<tr>
				<th width="40%"><?php echo $this->lang->line('text_analytics_cities');?> (<?php echo $cities['summary']->totalResults?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_visits');?> (<?php echo $cities['summary']->metrics->visits?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_pages_views');?> (<?php echo $cities['summary']->metrics->pageviews?>)</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;foreach($cities as $key => $ref): if ($key != 'summary'):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td><?php echo $key?></td>
				<td><?php echo $ref->visits?></td>
				<td><?php echo $ref->pageviews?></td>
			</tr>
			<?php endif;$i++;endforeach ?>
		</tbody>
	</table>
</div>
<?php endif;?>

<?php if(isset($browsers) && is_array($browsers)): ?>
<div class="dashboard_left analytics">
	<h3><?php echo $this->lang->line('title_analytics_browsers_referrers');?></h3>
	<h4><?php echo $this->lang->line('text_analytics_to');?> <?php echo $browsers['summary']->startDate?> <?php echo $this->lang->line('text_analytics_at');?> <?php echo $browsers['summary']->endDate;?></h4>
	<table class="table_list">
		<thead>
			<tr>
				<th width="40%"><?php echo $this->lang->line('text_analytics_browsers');?> (<?php echo $browsers['summary']->totalResults?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_visits');?> (<?php echo $browsers['summary']->metrics->visits?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_pages_views');?> (<?php echo $browsers['summary']->metrics->pageviews?>)</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;foreach($browsers as $key => $ref): if ($key != 'summary'):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td><?php echo $key?></td>
				<td><?php echo $ref->visits?></td>
				<td><?php echo $ref->pageviews?></td>
			</tr>
			<?php endif;$i++;endforeach ?>
		</tbody>
	</table>
</div>
<?php endif;?>

<?php if(isset($operating_systems) && is_array($operating_systems)): ?>
<div class="dashboard_left analytics last">
	<h3><?php echo $this->lang->line('title_analytics_os_referrers');?></h3>
	<h4><?php echo $this->lang->line('text_analytics_to');?> <?php echo $operating_systems['summary']->startDate?> <?php echo $this->lang->line('text_analytics_at');?> <?php echo $operating_systems['summary']->endDate;?></h4>
	<table class="table_list">
		<thead>
			<tr>
				<th width="40%"><?php echo $this->lang->line('text_analytics_os');?> (<?php echo $operating_systems['summary']->totalResults?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_visits');?> (<?php echo $operating_systems['summary']->metrics->visits?>)</th>
				<th width="30%"><?php echo $this->lang->line('text_analytics_pages_views');?> (<?php echo $operating_systems['summary']->metrics->pageviews?>)</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;foreach($operating_systems as $key => $ref): if ($key != 'summary'):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td><?php echo $key?></td>
				<td><?php echo $ref->visits?></td>
				<td><?php echo $ref->pageviews?></td>
			</tr>
			<?php endif;$i++;endforeach ?>
		</tbody>
	</table>
</div>
<?php endif;?>