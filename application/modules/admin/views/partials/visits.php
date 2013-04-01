<script type="text/javascript">
var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'chart_content',
			defaultSeriesType: 'line'
		},
		title: {
			text: ''
		},
		xAxis: {
			categories: [
				<?php
				if(isset($visits) && $visits) {
					foreach($visits as $key => $ref) {
						if ($key != 'summary') echo "'".substr($key, 6, 2).' '.date("M", mktime(0, 0, 0, substr($key, 4, 2), substr($key, 6, 2), substr($key, 0, 4)))."',\n";
					}
				};
				?>
			]
		},
		yAxis: {
			min: 0,
			title: {
				text: ''
			}
		},
		tooltip: {
			formatter: function() {
				return '<strong>'+this.series.name+' :</strong>'+' '+this.y;
			}
		},
		series: [{
			name: '<?php echo $this->lang->line('label_visits');?>',
			data: [
				<?php
				if(isset($visits) && $visits)
				{
					foreach($visits as $key => $ref)
					{
						if ($key != 'summary') echo $ref->visits.",\n";
					}
				};
				?>
			]
			}, {
			name: '<?php echo $this->lang->line('label_pageviews');?>',
			data: [
				<?php
				if(isset($visits) && $visits)
				{
					foreach($visits as $key => $ref)
					{
						if ($key != 'summary') echo $ref->pageviews.",\n";
					}
				};
				?>
			]
		}]
	});
});
</script>
<h3><?php echo $this->lang->line('title_visits');?></h3>
<div id="chart_content" style="width: 100%; height: 300px;"></div>