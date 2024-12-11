<?php

global $wpdb;

$projects = $wpdb->get_results("select ID, post_title from $wpdb->posts where post_type = 'project' and post_status = 'publish' order by post_title asc");
?>

<div class="main">

	<h2>Google Sheets Import</h2>

	<p>
		<button id='check' class="button button-primary">Check for Updates</button>
		<button id='mapdata' class="button button-primary">Regenerate Map Data</button>
        <button id='projectdata' class="button button-primary">Rebuild Project Data</button>
		<?php /*
		<button id='columns' class="button button-primary">List Column Names</button>
		*/ ?>
	</p>

	<?php if ($projects): ?>
	<div>
		<p>Update a specific project:</p>
		<select id="post_id" name="post_id">
			<?php foreach ($projects as $project): ?>
				<option value="<?php echo $project->ID ?>"><?php echo $project->post_title ?></option>
			<?php endforeach; ?>
		</select>
		<button id="update-project" class="button button-primary">Update</button>
		<button id="import-floorplans" class="button button-primary">Import Floorplans</button>
	</div>
	<?php endif; ?>

	<div id="response-container"></div>

	<div id="authForm" style='display:none;'>
		<h4>Expired or Missing Authentication Token...</h4>
		<p><a class='button button-secondary' href='#'>Generate New Token</a></p>
	</div>

	<div id="updateForm" style='display:none;'>
		<button id="update" class="button button-primary">Update</button>
	</div>

	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . "/library/legacy/gsheet_import/gsheet_import.js" ?>"></script>

</div>