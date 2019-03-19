<div id="<?php echo $job->id;?>" class="job-item <?php echo strtolower($job->state);?> clearfix">
	<div class="job-top clearfix">
		<div class="job-id"><?php echo $job->type; ?> started at <?php echo $job->created_at; ?></div>
		<div class="job-actions">
			<div class="loader"></div>
			<div class="refresh"></div>
			<div class="start"></div>
			<div class="pause"></div>
			<div class="cancel"></div>
			<div class="delete"></div>
		</div>

		<div class="clr"></div>
	</div>

	<div class="job-bottom clearfix">
		<div class="job-updated"><?php echo $job->status; ?></div>
		<div class="job-state"><?php echo $job->state;?> (<?php echo $job->percent;?>%)</div>

		<div class="clr"></div>
	</div>

	<div class="clr"></div>
</div>
