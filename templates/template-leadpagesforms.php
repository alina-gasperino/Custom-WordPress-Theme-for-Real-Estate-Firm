<div id="leadpagesbuttons" style='display:none;'>
<?php if ($mapdata && $mapdata->projects): ?>
	<?php foreach ($mapdata->projects as $project): ?>
		<?php if ($project->leadpageslink): ?>
			<div class="leadpagesbutton" data-id="<?php echo $project->id ?>">
				<a href="<?php echo $project->leadpageslink; ?>" target="_blank">Register Now!</a>
			</div>
		<?php endif ?>
	<?php endforeach ?>
<?php endif ?>
</div>
