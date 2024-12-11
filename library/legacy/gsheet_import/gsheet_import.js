;(function($, document, window, undefined){

	var total = 0;
	$output = $('#response-container');
	$authForm = $('#authForm');
	$updateForm = $('#updateForm');
	$mapdataButton = $('#mapdata');
	$projectdataButton = $('#projectdata');
	$columnsButton = $('#columns');

	var postCheck = function() {

		$('.status').remove();

		if (total > 0) {
			$updateForm.show();
		} else {
			$output.append("Nothing to update");
		}

	};

	var postUpdate = function() {

		$('.status').remove();
		generateMapData();

		$('.status').remove();
		generateProjectData();

	};

	var handleResponse = function(response) {

		console.log(response);

		if (response.auth_url) {
			$authForm.find('a').attr('href', response.auth_url);
			$authForm.show();
			return;
		}

		if (response.error) {
			$output.append(response.error);
			$output.append(response.data);
			console.log(response.data);
			return;
		}

		// if (response.new || response.updates) {
		// 	$output.append(response.success);
		// 	$output.append('<h2>' + response.sheet + '</h2>');
		// 	$output.append("<p>New: " + response.new + "   Updates: " + response.updates + '</p>');
		// 	return;
		// }

		if (response.total > 0) {
			total += response.new.length + response.updates.length;
			console.log('total updates: ' + total);
			$output.append('<h2>' + response.sheet + '</h2>');
			$output.append("<p>New: " + response.new.length + "   Updates: " + response.updates.length + "   Total: " + response.total + '</p>');
			return;
		}

		if (response.success) {
			if (response.action == 'check') {
				$('<p>').html('Finished Checking ' + response.sheet).appendTo($output);
			} else if (response.action == 'import') {
				$('<p>').html('Finished Updating ' + response.sheet).appendTo($output);
			} else if (response.data && response.data.googledoc_ids) {
				for (i in response.data.googledoc_ids) {
					var doc = response.data.googledoc_ids[i];
					console.log(doc);
				}
			} else {
				$output.append(response.success);
				console.log(response.data);
				// $output.append(data);
			}
			return;
		}

		$output.append(response);

	};

	var generateProjectData = function() {
        console.log('Rebuilding project filters...');
        $output.append('<h2><i class="fa fa-fw fa-spin fa-gear"></i> Rebuilding project data...</h2>');

        var promise = $.ajax({
            url: ajaxurl,
            data: {
                action: 'gsi_regenerate_project_data'
            },
            type: 'GET'
        });

        promise.done(function(response){
            console.log('finished rebuilding project data');
            $('i.fa-gear').remove();
            handleResponse(response);
        });
	};

	var generateMapData = function() {

		console.log('Regenerating map data...');
		$output.append('<h2><i class="fa fa-fw fa-spin fa-gear"></i> Regenerating map data...</h2>');

		var promise = $.ajax({
			url: ajaxurl,
			data: {
				action: 'gsi_regenerate_map_data'
			},
			type: 'GET'
		});

		promise.done(function(response){
			console.log('finished regenerating map data');
			$('i.fa-gear').remove();
			handleResponse(response);
		});

	};

	var clearOutput = function() {
		$output.html('');
	};

	var preStuff = function() {
		clearOutput();
		$authForm.hide();
		$updateForm.hide();
	};

	var checkUpdates = function(sheet) {

		return $.ajax({
			url: ajaxurl,
			data: {
				action: 'gsi_check',
				sheet: sheet
			},
			type: 'GET',
			success: function(response) {
				handleResponse(response);
			}
		});

	};

	var getColumns = function(sheet) {

		return $.ajax({
			url: ajaxurl,
			data: {
				action: 'gsi_get_columns',
				sheet: sheet
			},
			type: 'GET',
			success: function(response) {
				handleResponse(response);
			}
		});

	};

	var doUpdate = function(sheet) {

		return $.ajax({
			url: ajaxurl,
			data: {
				action: 'gsi_import',
				sheet: sheet
			},
			type: 'GET',
			success: function(response) {
				handleResponse(response);
			}
		});

	};

	var doUpdateProject = function(post_id) {

		return $.ajax({
			url: ajaxurl,
			data: {
				action: 'gsi_import',
				sheet: 'project',
				post_id: post_id
			},
			type: 'GET',
			success: function(response) {
				handleResponse(response);
			}
		});

	};

	var checkForUpdates = function() {

		total=0;

		preStuff();
		$output.append('<h3 class="status"><i class="fa fa-fw fa-gear fa-spin"></i> Checking for updates...</h3>');

		console.log('start developers');

		checkUpdates('developer').done(function(){
			checkUpdates('project').done(function(){
				// checkUpdates('assignment').done(function(){
					postCheck();
				// })
			})
		});

	}

	var pullUpdates = function() {
		preStuff();
		$output.append('<h3 class="status"><i class="fa fa-fw fa-gear fa-spin"></i> Pulling updates...</h3>');

		doUpdate('developer').done(function(){
			doUpdate('project').done(function(){
				// doUpdate('assignment').done(function(){
					postUpdate();
				// })
			})
		});
	};


	var updateProject = function(post_id) {
		preStuff();
		$output.append('<h3 class="status"><i class="fa fa-fw fa-gear fa-spin"></i> Pulling updates...</h3>');

		doUpdateProject(post_id).done(function(){
			postUpdate();
		});
	};


	var doImportFloorplans = function (post_id) {
		return $.ajax({
			url: ajaxurl,
			data: {
				action: 'import_floorplans_pdfs',
				post_id: post_id
			},
			type: 'GET',
			success: function(response) {
				console.log(response);
				if (response.data && response.data.message) {
					$output.append(response.data.message + '<br>');
				}
				if (response.data && response.data.continue) {
					doImportFloorplans(post_id);
				} else {
					$('h3.status i').remove();
				}
			}
		});
	};


	var importFloorplans = function (post_id) {
		preStuff();
		$output.append('<h3 class="status"><i class="fa fa-fw fa-gear fa-spin"></i> Importing Floorplans</h3>');
		doImportFloorplans(post_id);
	}


	$('button#columns').on('click', function(){
		getColumns('project');
	});

	// $('button#check').on('click', function(){
		// checkForUpdates();
	// });
	$('button#check').on('click', checkForUpdates);

	$('button#update').on('click', function(){
		pullUpdates();
	});

	$('button#update-project').on('click', function(){
		post_id = $('select#post_id option:selected').val();
		updateProject(post_id);
	});

	$('button#import-floorplans').on('click', function(){
		post_id = $('select#post_id option:selected').val();

		return importFloorplans(post_id);
	});

	$mapdataButton.on('click', function(){
		generateMapData();
	});

    $projectdataButton.on('click', function(){
        generateProjectData();
    });

})(jQuery, document, window);
