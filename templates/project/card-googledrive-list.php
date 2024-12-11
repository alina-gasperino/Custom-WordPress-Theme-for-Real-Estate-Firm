<?php $json = get_field('googledrive_files'); ?>
<?php $files = ($json) ? json_decode($json) : [] ?>
<?php if ($files): ?>
<div class="card googledrive list panel panel-default" id='project-pdfs'>
	<div class="panel-heading" role="tab" id="collapsePDFHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapsePDF" aria-expanded="true" aria-controls="collapsePDF" itemprop="name">
				PDF Files for <?php the_title();?>
			</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapsePDF" aria-expanded="true" aria-controls="collapsePDF">
			<i class="fa fa-caret-up"></i>Hide
		</button>

	</div>
	<div id="collapsePDF" class="card__content panel-collapse collapse in">
		<div class="iframe__container">
			<?php foreach ($files as $file): ?>
				<p>
					<img src="https://drive-thirdparty.googleusercontent.com/16/type/application/pdf">
                    <a href="https://drive.google.com/file/d/<?= $file->id?>/view" target="_blank"> <?php echo $file->name ?></a>
				</p>
			<?php endforeach; ?>
			<?php if (!is_user_logged_in()): ?>
			<div class="iframe__overlay">
				<a class="btn btn-alt" href="<?= leadpages_form_url() ?>" data-leadbox="<?= leadpages_form_data_id() ?>">
					<i class='fa fa-fw fa-lock'></i> Register to unlock
				</a>
			</div>
			<?php endif ?>
		</div>
		<?php /*
		<a class="orange-button" href="<?php echo leadpages_form_url() ?>" data-leadbox="<?php echo leadpages_form_data_id() ?>">More Info Directly in Your Inbox <i class='fa fa-fw fa-arrow-circle-right fa-lg'></i></a>
		*/ ?>
	</div>
</div>
<?php endif ?>
