// Utility
if (typeof Object.create !== 'function') {
    Object.create = function(obj) {
        function F() {};
        F.prototype = obj;
        return new F();
    };
}

(function($, window, document, undefined) {

    var TalkMap = {

        init: function(options, elem) {
            var self = this;

            // debug
            if (!Array.isArray(window.gmaps)) {
                window.gmaps = [];
            }
            window.gmaps.push(self)

            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.talkMap.options, options);
            self.options.mapOptions = $.extend({}, $.fn.talkMap.options.mapOptions, options.mapOptions);

            self.$mapCanvas = self.$elem.find('.map-canvas');
            self.$mapInner = self.$elem.find('.map-inner');
            self.$filtersContainer = self.$elem.find('.filters');
            self.$selectFilters = self.$filtersContainer.find('select#developers');
            self.$mapSidebar = self.$elem.find('.map-sidebar');
            self.$sidebarTabs = self.$elem.find('.map-sidebar__tabs');
            self.$projectsTab = self.$sidebarTabs.find('a.tab-condos');
            self.$floorplansTab = self.$sidebarTabs.find('a.tab-floorplans');
            self.$projectsTabContent = self.$elem.find('#tab-condos');
            self.$floorplansTabContent = self.$elem.find('#tab-floorplans');
            self.$filterButtons = self.$elem.find('.filter-button');
            self.$filterOptions = self.$elem.find('.filter-option');

            self.$regionHover = self.$elem.find('.region-hover').hide();
            self.$projectHover = self.$elem.find('.project-hover').hide();
            self.$projectInfoCard = self.$elem.find('.project-infocard').hide();

            self.$projectSidebar = self.$elem.find('.project-sidebar');
            self.$projectSidebarHeader = self.$projectSidebar.find('header');
            self.$projectContainer = self.$projectSidebar.find('.projects');
            self.$projectSort = self.$projectSidebar.find('.project-sidebar__sort');
            self.$viewToggle = self.$elem.find('.view-toggle');
            self.$pinDataToggle = self.$elem.find('.pin-data-toggle');

            self.$floorplanSidebar = self.$elem.find('.floorplan-sidebar');
            self.$floorplanSidebarHeader = self.$floorplanSidebar.find('header');
            self.$floorplanContainer = self.$floorplanSidebar.find('.floorplans');
            self.$floorplanSort = self.$floorplanSidebar.find('.project-sidebar__sort');
            self.$floorplanSortDirButton = self.$floorplanSidebar.find('.project-sidebar__sort__dir');

            self.initialProject = self.options.initialProject ? self.options.initialProject : self.$elem.attr('data-projectid');
            self.center = self.$elem.attr('data-center');
            self.floorplanLayout = self.options.floorplanLayout ? self.options.floorplanLayout : 'list';

            self.priceslider = self.$elem.find('#map-priceslider').get(0);
            self.bedsslider = self.$elem.find('#map-bedsslider').get(0);
            self.bathsslider = self.$elem.find('#map-bathsslider').get(0);
            self.sizeslider = self.$elem.find('#map-sizeslider').get(0);
            self.depositslider = self.$elem.find('#map-depositslider').get(0);
            self.pricepersqftslider = self.$elem.find('#map-pricepersqftslider').get(0);

            self.$projectsUngroup = self.$projectSidebar.find('.view-toggle button')
            self.$projectsGroup = self.$floorplanSidebar.find('.view-toggle button')

            self.pinData = self.options.pinData ? self.options.pinData : 'price-range';

            if (typeof($.fn.select2) === 'function') {
                self.$elem.find('select.regions').select2();
            }

            self.bounds = new google.maps.LatLngBounds();

            self.dataTaxonomy = self.$elem.data('taxonomy');
            self.dataTerm = self.$elem.data('term');

            self.setView(self.options.initView);

            self.initializeGoogleMap();

        },

        initializeGoogleMap: function() {

            var self = this;

            self.mapCanvas = self.$mapCanvas.get(0);
            self.infowindow = new google.maps.InfoWindow(self.options.infoWindowOptions);

            if (self.$elem.data('lat') && self.$elem.data('lng')) {
                self.options.mapOptions.center = {
                    lat: self.$elem.data('lat'),
                    lng: self.$elem.data('lng')
                };
            }

            if (self.$elem.data('zoom')) {
                self.options.mapOptions.zoom = self.$elem.data('zoom');
            }

            self.map = new google.maps.Map(self.mapCanvas);
            if (self.options.cluster && typeof(MarkerClusterer) === 'function') {
                self.cluster = new MarkerClusterer(self.map, [], self.options.clusterOptions);
            }

            google.maps.event.addListenerOnce(self.map, 'tilesloaded', function() {
                self.fetchData();
            });

            // self.fetchData();
            self.map.setOptions(self.options.mapOptions);

        },

        fetchData: function() {
            var self = this;

            if (self.options.mapData || self.options.projects) {
                if (!self.options.projects) {
                    self.mapData = self.options.mapData;
                    self.initializeTalkMap();
                    self.loadProjects().done(self.plotInitialData());

                } else if (!self.options.mapData) {
                    self.setProjects(self.options.projects);
                    self.loadMapData().done(function() {
                        self.redraw();
                        self.centerMap();
                    });

                } else {
                    self.setProjects(self.options.projects);
                    self.mapData = self.options.mapData;

                    self.initializeTalkMap();
                    self.plotInitialData()
                }
            } else {
                $.when(self.loadMapData())
                    .then(function() {
                        self.refresh();
                    })
                    .then(function() {
                        self.centerMap();
                    });
            }
        },

        loadMapData: function() {
            var self = this;

            if (self.options.dataURL) {
                var url = self.options.dataURL;
                var query = {};

            } else {
                url = avia_framework_globals.ajaxurl;
                query = {
                    action: 'ajax_get_map_data',
                    taxonomy: 'city',
                    term: self.$elem.data('region')
                };
            }

            return $.ajax({
                dataType: "json",
                url: url,
                data: query,
                success: function(json) {
                    self.mapData = json;
                    self.initializeTalkMap();
                },
                statusCode: {
                    404: function() {
                        console.log('404: not found');
                    }
                }
            });

        },

        loadProjects: function() {
            var self = this;

            var query = {
                action: 'talkcondo_map_query'
            };

            if (self.initialProject) {
                query.id = self.initialProject;
            }

            return $.ajax({
                dataType: 'json',
                url: avia_framework_globals.ajaxurl,
                data: query,
                success: function(json) {
                    self.setProjects(json);
                }
            });
        },

        setProjects: function(projects) {
            var self = this;
            for (p in projects) {
                if (projects[p].size) {
                    if (projects[p].size.min < 100)
                        projects[p].size.min = 0
                    if (projects[p].size.max < 100)
                        projects[p].size.max = 0
                }

                if (projects[p].floorplans && projects[p].floorplans.length) {
                    let s = 0
                    for (f in projects[p].floorplans) {
                        s += projects[p].floorplans[f].pricepersqft
                    }
                    projects[p].pricepersqft = Math.floor(s / projects[p].floorplans.length)
                }
            }
            self.projects = projects;
            self.setProjectIndexes();
        },

        setProjectIndexes: function() {
            var self = this;
            self.projectIndexes = [];
            for (p in self.projects) {
                self.projectIndexes[self.projects[p].post_id] = p;
            }
        },

        getProject: function(project_id) {
            var self = this;
            var index = self.projectIndexes[project_id];
            return self.projects[index];
        },

        plotInitialData: function() {
            var self = this;

            self.refresh();

            self.centerMap();
        },

        centerMap: function() {
            var self = this;

            if (self.center === 'poi') {
                self.poiMarker = new google.maps.Marker({
                    position: new google.maps.LatLng({
                        lat: self.$elem.data('lat'),
                        lng: self.$elem.data('lng')
                    }),
                    map: self.map
                });
            } else if (self.center === 'user-location') {
                self.toggleUserLocation();
            }
        },

        initializeTalkMap: function() {
            var self = this;

            self.bindEventListeners();
            self.developerTypeahead();

            if (!self.selectedLocations)
                self.selectedLocations = {};

            if (self.$elem.find('.floorplans__filter-sliders').length) {
                self.initializeFloorplanSliders();
            }

            if (self.isMobile()) {
                self.$elem.removeClass('sidebar-projects');
                self.$elem.removeClass('sidebar-floorplans');
            }

            if (self.options.initialZoom === 'markers') {
                self.setBounds(self.projectMarkerBounds);
            }

            var shouldZoomToRegions = self.filterRegions();
            self.generateChipForSelecterRegion();
            self.drawRegions();



            if (shouldZoomToRegions) {
                self.zoomToLocations();
            }
        },

        initializeFloorplanSliders: function() {

            var self = this;

            if (self.priceslider) {
                var min = (self.priceslider.dataset.min) ? parseFloat(self.priceslider.dataset.min) : 200000;
                var max = (self.priceslider.dataset.max) ? parseFloat(self.priceslider.dataset.max) : 2500000;

                var initMin = (self.priceslider.dataset.init_min) ? parseFloat(self.priceslider.dataset.init_min) : min;
                var initMax = (self.priceslider.dataset.init_max) ? parseFloat(self.priceslider.dataset.init_max) : max;

                if (min === max) {
                    min = 200000;
                    max = 2500000;
                }
                noUiSlider.create(self.priceslider, {
                    start: [initMin, initMax],
                    step: 50000,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                self.priceslider.noUiSlider.on('update', function(values, handle) {
                    var $slider = $('#map-priceslider');
                    var $container = $slider.closest('.filter-button');
                    var $buttonTitle = $container.find('span.title');

                    var min = this.options.range.min[0];
                    var max = this.options.range.max[0];
                    var lowerLabel = values[0] / 1000;
                    if (values[0] <= 999999) lowerLabel += 'K';
                    if (values[0] > 999999) {
                        lowerLabel = values[0] / 1000 / 1000 + 'M';
                    }

                    var upperLabel = values[1] / 1000;
                    if (values[1] <= 999999) upperLabel += 'K';
                    if (values[1] > 999999) {
                        upperLabel = values[1] / 1000 / 1000 + 'M';
                    }

                    // $slider.find('.noUi-handle-lower').html(lowerLabel);
                    // $slider.find('.noUi-handle-upper').html(upperLabel);
                    $slider.closest('.filter-button').find('.lower-label').html(lowerLabel);
                    $slider.closest('.filter-button').find('.upper-label').html(upperLabel);

                    if (values[0] == min && values[1] == max) {
                        $container.find('.filter-option').removeClass('active');
                        $buttonTitle.html($buttonTitle.attr('data-placeholder'));
                    } else {
                        $container.find('.filter-option').addClass('active');
                        $buttonTitle.html('$' + lowerLabel + ' - ' + '$' + upperLabel);
                    }
                });
                self.priceslider.noUiSlider.on('change', function(values, handle) {
                    self.refresh();
                });
            }

            if (self.bedsslider) {
                var min = (self.bedsslider.dataset.min) ? parseFloat(self.bedsslider.dataset.min) : 0;
                var max = (self.bedsslider.dataset.max) ? parseFloat(self.bedsslider.dataset.max) : 3;

                var initMin = (self.bedsslider.dataset.init_min) ? parseFloat(self.bedsslider.dataset.init_min) : min;
                var initMax = (self.bedsslider.dataset.init_max) ? parseFloat(self.bedsslider.dataset.init_max) : max;

                if (min == max) {
                    min = 0;
                    max = 3;
                }
                noUiSlider.create(self.bedsslider, {
                    start: [initMin, initMax],
                    step: 0.5,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                self.bedsslider.noUiSlider.on('update', function(values, handle) {
                    var $slider = $('#map-bedsslider');
                    var $container = $slider.closest('.filter-button');
                    var $buttonTitle = $container.find('span.title');

                    var min = this.options.range.min[0];
                    var max = this.options.range.max[0];
                    var lowerLabel = Math.round(values[0] * 10) / 10;
                    var upperLabel = (Math.round(values[1] * 10) / 10 == max) ? max + "+" : Math.round(values[1] * 10) / 10;

                    // $slider.find('.noUi-handle-lower').html(lowerLabel);
                    // $slider.find('.noUi-handle-upper').html(upperLabel);
                    $slider.closest('.filter-button').find('.lower-label').html(lowerLabel);
                    $slider.closest('.filter-button').find('.upper-label').html(upperLabel);

                    if (values[0] == min && values[1] == max) {
                        $container.find('.filter-option').removeClass('active');
                        $buttonTitle.html($buttonTitle.attr('data-placeholder'));
                    } else {
                        $container.find('.filter-option').addClass('active');
                        $buttonTitle.html(lowerLabel + ' - ' + upperLabel + ' beds');
                    }
                });
                self.bedsslider.noUiSlider.on('change', function(values, handle) {
                    self.refresh();
                });
            }

            if (self.depositslider) {
                var min = (self.depositslider.dataset.min) ? parseInt(self.depositslider.dataset.min) : 0;
                var max = (self.depositslider.dataset.max) ? parseInt(self.depositslider.dataset.max) : 25;

                var initMin = (self.depositslider.dataset.init_min) ? parseInt(self.depositslider.dataset.init_min) : min;
                var initMax = (self.depositslider.dataset.init_max) ? parseInt(self.depositslider.dataset.init_max) : max;

                if (min == max) {
                    min = 0;
                    max = 25;
                }
                noUiSlider.create(self.depositslider, {
                    start: [initMin, initMax],
                    step: 5,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                self.depositslider.noUiSlider.on('update', function(values, handle) {
                    var $slider = $('#map-depositslider');
                    var $container = $slider.closest('.filter-button');
                    var $buttonTitle = $container.find('span.title');

                    var min = this.options.range.min[0];
                    var max = this.options.range.max[0];
                    var lowerLabel = (Math.round(values[0] * 10) / 10) + '%';
                    var upperLabel = ((Math.round(values[1] * 10) / 10 == max) ? max + "+" : Math.round(values[1] * 10) / 10) + '%';

                    $slider.closest('.filter-button').find('.lower-label').html(lowerLabel);
                    $slider.closest('.filter-button').find('.upper-label').html(upperLabel);

                    if (values[0] == min && values[1] == max) {
                        $container.find('.filter-option').removeClass('active');
                        $buttonTitle.html($buttonTitle.attr('data-placeholder'));
                    } else {
                        $container.find('.filter-option').addClass('active');
                        $buttonTitle.html(lowerLabel + ' - ' + upperLabel);
                    }
                });
                self.depositslider.noUiSlider.on('change', function(values, handle) {
                    self.refresh();
                });
            }

            if (self.pricepersqftslider) {
                var min = (self.pricepersqftslider.dataset.min) ? parseInt(self.pricepersqftslider.dataset.min) : 500;
                var max = (self.pricepersqftslider.dataset.max) ? parseInt(self.pricepersqftslider.dataset.max) : 2000;

                var initMin = (self.pricepersqftslider.dataset.init_min) ? parseInt(self.pricepersqftslider.dataset.init_min) : min;
                var initMax = (self.pricepersqftslider.dataset.init_max) ? parseInt(self.pricepersqftslider.dataset.init_max) : max;

                if (min == max) {
                    min = 500;
                    max = 2000;
                }
                noUiSlider.create(self.pricepersqftslider, {
                    start: [initMin, initMax],
                    step: 100,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                self.pricepersqftslider.noUiSlider.on('update', function(values, handle) {

                    var $slider = $('#map-pricepersqftslider');
                    var $container = $slider.closest('.filter-button');
                    var $buttonTitle = $container.find('span.title');

                    var min = this.options.range.min[0];
                    var max = this.options.range.max[0];
                    var lowerLabel = "$" + Math.round(values[0]);
                    var upperLabel = "$" + ((Math.round(values[1]) == max) ? max + "+" : Math.round(values[1]));

                    $slider.closest('.filter-button').find('.lower-label').html(lowerLabel);
                    $slider.closest('.filter-button').find('.upper-label').html(upperLabel);

                    if (values[0] == min && values[1] == max) {
                        $container.find('.filter-option').removeClass('active');
                        $buttonTitle.html($buttonTitle.attr('data-placeholder'));
                    } else {
                        $container.find('.filter-option').addClass('active');
                        $buttonTitle.html(lowerLabel + ' - ' + upperLabel);
                    }
                });
                self.pricepersqftslider.noUiSlider.on('change', function(values, handle) {
                    self.refresh();
                });
            }

            if (self.bathsslider) {
                var min = (self.bathsslider.dataset.min) ? parseFloat(self.bathsslider.dataset.min) : 0;
                var max = (self.bathsslider.dataset.max) ? parseFloat(self.bathsslider.dataset.max) : 3;

                var initMin = (self.bathsslider.dataset.init_min) ? parseFloat(self.bathsslider.dataset.init_min) : min;
                var initMax = (self.bathsslider.dataset.init_max) ? parseFloat(self.bathsslider.dataset.init_max) : max;

                if (min == max) {
                    min = 0;
                    max = 3;
                }
                noUiSlider.create(self.bathsslider, {
                    start: [initMin, initMax],
                    step: 0.5,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                self.bathsslider.noUiSlider.on('update', function(values, handle) {

                    var $slider = $('#map-bathsslider');
                    var $container = $slider.closest('.filter-button');
                    var $buttonTitle = $container.find('span.title');

                    var min = this.options.range.min[0];
                    var max = this.options.range.max[0];
                    var lowerLabel = Math.round(values[0] * 10) / 10;
                    var upperLabel = (Math.round(values[1] * 10) / 10 == max) ? max + "+" : Math.round(values[1] * 10) / 10;

                    // $slider.find('.noUi-handle-lower').html(lowerLabel);
                    // $slider.find('.noUi-handle-upper').html(upperLabel);
                    $slider.closest('.filter-button').find('.lower-label').html(lowerLabel);
                    $slider.closest('.filter-button').find('.upper-label').html(upperLabel);

                    if (values[0] == min && values[1] == max) {
                        $container.find('.filter-option').removeClass('active');
                        $buttonTitle.html($buttonTitle.attr('data-placeholder'));
                    } else {
                        $container.find('.filter-option').addClass('active');
                        $buttonTitle.html(lowerLabel + ' - ' + upperLabel + ' baths');
                    }
                });
                self.bathsslider.noUiSlider.on('change', function(values, handle) {
                    self.refresh();
                });
            }

            if (self.sizeslider) {
                var min = (self.sizeslider.dataset.min) ? parseInt(self.sizeslider.dataset.min) : 0;
                var max = (self.sizeslider.dataset.max) ? parseInt(self.sizeslider.dataset.max) : 2000;

                var initMin = (self.sizeslider.dataset.init_min) ? parseInt(self.sizeslider.dataset.init_min) : min;
                var initMax = (self.sizeslider.dataset.init_max) ? parseInt(self.sizeslider.dataset.init_max) : max;

                noUiSlider.create(self.sizeslider, {
                    start: [initMin, initMax],
                    step: 100,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                self.sizeslider.noUiSlider.on('update', function(values, handle) {

                    var $slider = $('#map-sizeslider');
                    var $container = $slider.closest('.filter-button');
                    var $buttonTitle = $container.find('span.title');

                    var min = this.options.range.min[0];
                    var max = this.options.range.max[0];
                    var lowerLabel = Math.round(values[0]);
                    var upperLabel = (Math.round(values[1]) == max) ? max + "+" : Math.round(values[1]);

                    // $slider.find('.noUi-handle-lower').html(lowerLabel);
                    // $slider.find('.noUi-handle-upper').html(upperLabel);
                    $slider.closest('.filter-button').find('.lower-label').html(lowerLabel);
                    $slider.closest('.filter-button').find('.upper-label').html(upperLabel);

                    if (values[0] == min && values[1] == max) {
                        $container.find('.filter-option').removeClass('active');
                        $buttonTitle.html($buttonTitle.attr('data-placeholder'));
                    } else {
                        $container.find('.filter-option').addClass('active');
                        $buttonTitle.html(lowerLabel + ' - ' + upperLabel + ' sq.ft.');
                    }

                });
                self.sizeslider.noUiSlider.on('change', function(values, handle) {
                    self.refresh();
                });
            }

        },

        setView: function(view) {
            var self = this;

            // view = 'projects';

            if (this.view == view) return;

            this.view = view;

            this.$filtersContainer.find('.floorplan-filter').not('.project-filter').toggle(view == 'floorplans');
            this.$filtersContainer.find('.project-filter').not('.floorplan-filter').toggle(view == 'projects');

            if (view == 'projects') {
                self.$projectsTab.tab('show');
                // self.$viewToggle.find('button').html('<i class="far fa-building"></i> Viewing Condo Buildings <span class="caret"></span>');
                $('.toggle-floorplans').hide();
                $('.toggle-projects').show();
                // self.$pinDataToggle.hide();
            } else if (view == 'floorplans') {
                self.$floorplansTab.tab('show');
                // self.$viewToggle.find('button').html('<i class="fa fa-layer-group"></i> Viewing Available Suites <span class="caret"></span>');
                $('.toggle-projects').hide();
                $('.toggle-floorplans').show();
                self.$pinDataToggle.show();
            }

            self.$viewToggle.find('li').removeClass('selected');
            self.$viewToggle.find('li[data-view=' + view + ']').addClass('selected');

            this.clearActiveProject();
        },

        setPinData: function(view, label) {
            var self = this;
            if (self.pinData == view) return;

            self.pinData = view;
            self.$pinDataToggle.find('.pin-data__label').html(label);
            self.$pinDataToggle.find('li').removeClass('selected');
            self.$pinDataToggle.find('li[data-view=' + view + ']').addClass('selected');

            self.redraw();
        },

        buildPreviousResults: function() {
            // $('#map-search .ajax_search_entry.for_map .previous-result').remove();

            // generated search-result, should be called here and
            // if($("#map-search .ajax_search_response").find('h4').length) {
            for (e in self.selectedLocations) {
                self.selectedLocations[e].$cloned.prependTo($("#map-search .search-form"))
            }
            // }
        },

        bindEventListeners: function() {

            var self = this;

            $('#map-search').on('ready', '.add-previous-results', function(e) {
                self.buildPreviousResults();
                $('#map-search .add-previous-results').removeClass('add-previous-results')
            })

            $('body').on('click', '#map-search .ajax_search_entry.for_map', function(e) {
                var location = $(this).data('json');

                if (self.selectedLocations[location.id]) {
                    delete self.selectedLocations[location.id]
                } else {
                    // keep only one user location
                    self.selectedLocations = {};

                    location.$cloned = $('<div>').append($(this).clone()).remove().addClass('previous-result').addClass('chip');
                    self.selectedLocations[location.id] = location;
                }


                // show selected chips
                $("#map-search .search-form .chip").remove();
                $("#map-search .search-form .chip").remove();
                for (e in self.selectedLocations) {
                    self.selectedLocations[e].$cloned.prependTo($("#map-search .search-form"))
                }

                self.prettySearch();

                $("#map-search #s").val("")
                $("#map-search #s").trigger("keyup")

                // self.buildPreviousResults();

                // temporary solution
                self.$elem.find('select#regions').val(Object.keys(self.selectedLocations));
                self.$elem.find('select#regions').trigger('change')

                // find different way to hide search results
                $('body').trigger('click')

            })

            if (self.$sidebarTabs.length) {
                self.$sidebarTabs.find('a[data-toggle="tab"]').on('shown.bs.tab', function() {
                    self.refresh();
                });
            }

            self.$mapSidebar.on('scroll', function(e) {
                var $this = $(this);
                if ($this.scrollTop() + $this.innerHeight() == this.scrollHeight) {
                    self.$floorplanContainer.find('.load-more').trigger('click');
                }
            });

            self.$floorplanContainer.on('click', '.load-more', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.html('<i class="fa fa-spin fa-spinner"></i> Loading more...').show();
                self.populateFloorplans(this.dataset.page);
            });

            self.$projectsTab.on('click', function() {
                self.setView('projects');
            });

            self.$floorplansTab.on('click', function() {
                self.setView('floorplans');
            });

            self.$floorplanSidebar.on('click', '.floorplan__latest-pricing', function(e) {
                e.preventDefault();
                $this = $(this);
                var projectid = $this.closest('.floorplan').data('projectid');
                var $btn = $(".leadpagesbutton[data-id='" + projectid + "'] a");
                $btn.trigger('click');
                e.stopPropagation();
            });

            self.$floorplanSidebar.on('click', '.floorplan__project-title', function(e) {
                e.preventDefault();
                $this = $(this);

                $floorplan = $this.closest('.floorplan');
                $floorplan.toggleClass('open');
                $project = $floorplan.find('.project-info');
                $project.slideToggle('fast');

                $floorplan.siblings('.open').removeClass('open').find('.project-info').slideUp('fast');
            });

            self.$projectSort.find('.project-sidebar__sort__dir').on('click', function() {
                var $this = $(this);
                if (this.dataset.direction == 'asc') {
                    this.dataset.direction = 'desc';
                    $this.find('.fa').removeClass('fa-sort-amount-up').addClass('fa-sort-amount-down');
                } else {
                    this.dataset.direction = 'asc';
                    $this.find('.fa').removeClass('fa-sort-amount-down').addClass('fa-sort-amount-up');
                }
                self.sortProjects();
            });

            self.$projectSort.find('li').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                self.$projectSort.find('.dropdown button')
                    .attr('data-sort', this.dataset.sort)
                    .html('Sort by ' + $this.text() + ' <span class="caret"></span>');
                self.sortProjects();
            });

            self.$viewToggle.find('li').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                self.setView(this.dataset.view);
            });

            self.$projectsGroup.on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                self.setView('projects');
            });

            self.$projectsUngroup.on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                self.setView('floorplans');
            });

            self.$pinDataToggle.find('li').on('click', function(e) {
                e.preventDefault();
                self.setPinData(this.dataset.view, this.dataset.label);
            });


            self.$floorplanSort.find('.project-sidebar__sort__dir').on('click', function() {
                var $this = $(this);
                if (this.dataset.direction == 'asc') {
                    this.dataset.direction = 'desc';
                    $this.find('.fa').removeClass('fa-sort-amount-up').addClass('fa-sort-amount-down');
                } else {
                    this.dataset.direction = 'asc';
                    $this.find('.fa').removeClass('fa-sort-amount-down').addClass('fa-sort-amount-up');
                }
                self.sortFloorplans();
            });

            self.$floorplanSort.find('li').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                self.$floorplanSort.find('.dropdown button')
                    .attr('data-sort', this.dataset.sort)
                    .html('Sort by ' + $this.text() + ' <span class="caret"></span>');
                self.sortFloorplans();
            });

            self.$floorplansTabContent.on('click', '.list-entry-headers *[data-sort]', function() {
                var sort = this.dataset.sort;
                var current_sort = self.$floorplanSort.find('.dropdown button').attr('data-sort');
                if (sort === current_sort) {
                    self.$floorplanSort.find('.project-sidebar__sort__dir').trigger('click');
                } else {
                    self.$floorplanSort.find('li[data-sort=' + sort + ']').trigger('click');
                }
            });

            $('.toggle-filters').on('click', function() {
                self.$mapSidebar.removeClass('open-mobile');
                // self.$filtersContainer.toggle();
                // self.$filtersContainer.find('.floorplan-filter').addClass('active');
                // self.$filtersContainer.toggleClass('active');
                self.$filtersContainer.css('display', 'block');
                var $this = $(this);
                $this.addClass('active');
                $this.siblings().removeClass('active');
            });

            $('.close-filters').on('click', function() {
                $('.toggle-map').trigger('click');
            });

            $('.toggle-projects').on('click', function() {
                var $this = $(this);
                // if ($this.hasClass('active')) {
                //     self.$mapSidebar.removeClass('open-mobile');
                //     $this.removeClass('active');
                // } else {
                //     $this.siblings().removeClass('active');
                //     self.$filtersContainer.hide();
                //     self.$mapSidebar.addClass('open-mobile');
                //     self.$projectsTab.tab('show');
                //     $this.addClass('active');
                // }

                $this.siblings().removeClass('active');
                self.$filtersContainer.hide();

                if (!self.$mapSidebar.hasClass('open-mobile')) {
                    self.$mapSidebar.addClass('open-mobile');
                }
                self.$filtersContainer.removeClass('active');
                self.$filtersContainer.css('display', 'none');
                self.$projectsTab.tab('show');
                $this.addClass('active');

                self.setView('projects');
            });

            $('.toggle-map').on('click', function() {
                var $this = $(this);
                self.$mapSidebar.removeClass('open-mobile');
                $this.addClass('active');
                $this.siblings().removeClass('active');
                self.$filtersContainer.removeClass('active');
                self.$filtersContainer.css('display', 'none');
                self.$filtersContainer.toggle(false);
            });

            $('.toggle-floorplans').on('click', function() {
                var $this = $(this);
                self.$filtersContainer.hide();

                if (!self.$mapSidebar.hasClass('open-mobile')) {
                    self.$mapSidebar.addClass('open-mobile');
                }

                $this.siblings().removeClass('active');
                self.$floorplansTab.tab('show');
                $this.addClass('active');
                self.$filtersContainer.css('display', 'none');

                self.setView('floorplans');
            });

            $('.reset-filters').click(function() {
                self.$filtersContainer.find('.filter-option.active').removeClass('active');
                self.$filtersContainer.find('.filter-option.rounded').remove();

                self.priceslider.noUiSlider.reset();
                self.sizeslider.noUiSlider.reset();
                self.bedsslider.noUiSlider.reset();
                self.bathsslider.noUiSlider.reset();
                self.depositslider.noUiSlider.reset();
                self.pricepersqftslider.noUiSlider.reset();

                self.refresh();
            });

            $('.share-toggle').click(function() {
                $this = $(this);
                var url = self.getShareLink();

                $this.find('input').val(url);

            });

            $('.share-toggle .copy-share-link').click(function() {
                var $this = $(this);
                $this.closest('.submenu').find('input').select();
                document.execCommand('copy');
                $this.html('<i class="fa fa-check"></i> Copied!');
                setTimeout(function() {
                    $this.html('<i class="fa fa-copy"></i> Copy Link');
                }, 1000);
            });

            $('.map-nav .zoom-in').on('click', function(e) {
                e.preventDefault();
                self.map.setZoom(self.map.getZoom() + 1);
            });

            $('.map-nav .zoom-out').on('click', function(e) {
                e.preventDefault();
                self.map.setZoom(self.map.getZoom() - 1);
            });

            $('.map-nav .toggle.location').on('click', function(e) {
                e.preventDefault();
                if (!navigator.geolocation) return;
                self.toggleUserLocation();
            });

            self.$filterButtons.on('click', function(e) {
                $this = $(this);
                var state = $this.hasClass('active');
                self.$filterButtons.removeClass('active');
                $this.toggleClass('active', !state);
                e.stopPropagation();
            });

            // Remove the active menu on body click
            $('body').on('click.test', function() {
                self.$filterButtons.removeClass('active');
            });

            $('.submenu').on('click.test', '.filter-option .close', function(e) {
                e.stopPropagation();
                $this = $(this);
                $this.closest('.filter-option').remove();
                $title = $this.closest('.filter-button').find('span.title');
                $title.html($title.data('placeholder'));
                self.refresh();
            });

            $('.submenu').on('click.test', function(e) {
                e.stopPropagation();
            });

            self.$elem.find('form').on('submit', function(e) {
                e.preventDefault();
            });

            self.$elem.find('select#regions').on('change', function(e) {
                self.clearOverlays();
                self.clearRegions();
                self.filterRegions();
                self.drawRegions();
                self.zoomToLocations();
            });

            self.$filterOptions.on('click', function(e) {
                var $this = $(this);
                $this.toggleClass('active');
                clearTimeout(self.filterTimer);
                self.filterTimer = setTimeout(function() {
                    self.refresh();
                }, 100);
            });

            /*
            self.$projectSidebar.on('click', '.project', function(e) {
                var $target = $(e.target);
                if ($target.hasClass('project-title')) {
                    window.location = $target.attr('src');
                } else {
                    e.preventDefault();
                    var id = $(this).data('id');
                    self.setActiveProject(id);
                }
            });
            */

            self.$projectSidebar.on('click', 'a.leadpages', function(e) {
                e.preventDefault();
                $this = $(this);
                var projectid = $this.closest('.project').data('projectid');
                var $btn = $(".leadpagesbutton[data-id='" + projectid + "'] a");
                $btn.trigger('click');
                e.stopPropagation();
            });

            self.$projectSidebar.on('click', '.floorplans-available', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $project = $this.closest('.project');
                var projectid = $project.data('projectid');

                if ($this.hasClass('open')) {
                    $this.closest('.projects').find('.floorplan-sidebar').remove();
                    $this.find('i.fa').removeClass('fa-minus').addClass('fa-plus');
                    $this.removeClass('open');
                } else {
                    var query = self.getQuery();
                    query.action = 'talkcondo_floorplan_query';
                    query.project_id = projectid;
                    $.ajax({
                        url: avia_framework_globals.ajaxurl,
                        data: query
                    }).done(function(response) {
                        if (!response.length) return;

                        self.$projectSidebar.find('.floorplan-sidebar').remove();
                        var $html = '';
                        var $div = $('<div class="floorplan-sidebar" style="display: none;"></div>');
                        for (f in response) {
                            var floorplan = response[f];
                            if (self.isMobile()) {
                                $html = self.generateFloorplanGridTemplate(floorplan.project, floorplan);
                            } else {
                                $html = self.generateFloorplanListTemplate(floorplan.project, floorplan);
                            }
                            $html.appendTo($div);
                        }
                        $project.after($div);
                        $div.slideDown('fast');
                        $this.find('i.fa').removeClass('fa-plus').addClass('fa-minus');
                        $this.addClass('open');
                    });
                }

            });

            self.$projectSidebar.on('mouseenter', '.project', function() {
                var project = self.getProject(this.dataset.projectid);
                self.$projectHover.hide();
                if (typeof project.marker === 'object') {
                    project.marker.setActive();
                    self.setProjectHover(project);
                }
            });

            self.$projectSidebar.on('mouseleave', '.project', function() {
                var project = self.getProject(this.dataset.projectid);
                self.$projectHover.hide();
                if (typeof project.marker === 'object') {
                    project.marker.setInactive();
                }
            });

            self.$projectSidebar.on('mouseleave', function() {
                self.$projectHover.hide();
                self.$regionHover.hide();
            });

            self.$floorplanSidebar.on('mouseenter', '.floorplan', function() {
                var project = self.getProject(this.dataset.projectid);

                self.$projectHover.hide();
                if (project && typeof(project.marker) === 'object') {
                    project.marker.setActive();
                }
                if (!self.activeProject) {
                    self.setProjectHover(project);
                }
            });

            self.$floorplanSidebar.on('mouseleave', '.floorplan', function() {
                var project = self.getProject(this.dataset.projectid);

                self.$projectHover.hide();
                if (project && typeof(project.marker) === 'object') {
                    project.marker.setInactive();
                }
            });

            $('.map-sidebar__toggle').on('click', function(e) {
                e.preventDefault();
                self.$elem.toggleClass('sidebar-open-desktop');
                if (self.options.cluster) {
                    self.refresh();
                }
            });

            self.$projectInfoCard.html('');
            self.$projectInfoCard.append('<div class="handle"></div>');
            self.$projectInfoCard.append('<div class="project"></div>');
            self.$projectInfoCard.append('<div class="floorplan-sidebar"></div>');

            self.$projectInfoCard.find('.handle').on('click', function(e) {
                e.preventDefault();
                self.$projectInfoCard.toggleClass('full');
                self.$projectInfoCard.find('.floorplan-sidebar').slideToggle();
            });

            self.$projectInfoCard.find('.project').on('click', function(e) {
                e.preventDefault();
                self.$projectInfoCard.find('.floorplan-sidebar').slideToggle(function() {
                    self.$projectInfoCard.toggleClass('full');
                });
            });

            // self.$projectInfoCard.find('.project').swipe({
            //     threshold: 120,
            //     allowPageScroll: "none",
            //     swipeStatus: function(event, phase, direction, distance, duration, fingerCount, fingerData, currentDirection) {

            //         if (phase === 'start') {
            //             self.$projectInfoCard.addClass('swiping');
            //         }

            //         if (phase === 'move') {
            //             var $el = self.$projectInfoCard.find('.floorplan-sidebar');
            //             var closing = $el.hasClass('open');

            //             if (closing) {
            //                 self.$projectInfoCard.css('top', distance);
            //             } else {
            //                 $el.show().css('height', distance);
            //             }
            //         }

            //         if (phase === 'end' || phase === 'cancel') {
            //             self.$projectInfoCard.removeClass('swiping');
            //         }
            //     },
            //     swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
            //         if (direction === 'up') {
            //             self.toggleProjectInfoCard(true);
            //         } else if (direction === 'down') {
            //             self.toggleProjectInfoCard(false);
            //         } else {
            //             self.$projectInfoCard.css('top', '');
            //             self.$projectInfoCard.find('.floorplan-sidebar').css('height', distance);
            //         }
            //     }
            // });

            google.maps.event.addListener(self.map, 'idle', function() {
                self.refresh();
            });

            google.maps.event.addListener(self.map, 'dragstart', function() {
                self.clearOverlays();
            });

            google.maps.event.addListener(self.map, 'click', function(e) {
                if (self.markerClicked) {
                    self.markerClicked = null;
                    return;
                }
                self.clearOverlays();
            });

            google.maps.event.addListener(self.map, 'resize', function() {
                self.clearOverlays();
            });

            google.maps.event.addListener(self.map, 'bounds_changed', function() {
                clearTimeout(self.bounds_changed_timeout);
                self.bounds_changed_timeout = setTimeout(function() {
                    self.clearOverlays();
                }, 100);
                // self.clearOverlays();
            });

            google.maps.event.addListener(self.map, 'zoom_changed', function() {
                self.clearOverlays();
            });

        },

        isMobile: function() {
            var self = this;

            return self.$elem.find('.mobile-tablet:visible').length;
        },

        toggleUserLocation: function() {

            var self = this;

            if (self.userLocation) {

                self.userLocation.setMap(null);
                self.userLocation = null;

            } else {

                navigator.geolocation.getCurrentPosition(function(pos) {

                    var latLng = new google.maps.LatLng({
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude
                    });

                    self.userLocation = new google.maps.Marker({
                        position: latLng,
                        icon: self.options.userLocationMarker,
                        map: self.map
                    });

                    self.map.setCenter(latLng);
                    self.map.setZoom(self.options.myLocationZoom);

                }, function(error) {
                    console.log(error);
                });

            }

        },

        sortProjects: function() {

            var self = this;

            var sort = self.$projectSort.find('.dropdown button').attr('data-sort');
            var order = self.$projectSort.find('.project-sidebar__sort__dir').attr('data-direction');

            var $items = self.$projectContainer.find('.project');

            if (sort == 'price' || sort == 'updated') {
                $items.sort(function(a, b) {
                    var cmp = 0;

                    a = parseFloat(a.dataset[sort]);
                    b = parseFloat(b.dataset[sort]);

                    if (a && b) {
                        cmp = a - b;
                    } else if (a || b) {
                        cmp = a ? 1 : -1;
                    }

                    return order === 'desc' ? -cmp : cmp;
                });
            } else {
                $items.sort(function(a, b) {
                    var cmp = 0;

                    if (a.dataset['priority'] !== b.dataset['priority']) {
                        cmp = parseInt(a.dataset['priority']) > parseInt(b.dataset['priority']) ? 1 : -1;
                    } else {
                        cmp = parseFloat(a.dataset['updated']) - parseFloat(b.dataset['updated']);
                    }

                    return order === 'desc' ? -cmp : cmp;
                });
            }

            $items.appendTo(self.$projectContainer);

            self.lazyLoadImages();
        },

        sortFloorplans: function() {
            var self = this;

            var sort = self.$floorplanSort.find('.dropdown button').attr('data-sort');
            var order = self.$floorplanSort.find('.project-sidebar__sort__dir').attr('data-direction');

            if (sort != 'size' && sort != 'pricepersqft' && sort != 'beds' && sort != 'projectname') sort = 'price';

            var $items = self.$floorplanContainer.find('.floorplan');

            $items.sort(function(a, b) {
                if (a.dataset[sort] && b.dataset[sort]) {
                    if (sort == 'projectname') {
                        if (a.dataset[sort].toLowerCase() > b.dataset[sort].toLowerCase()) {
                            return (order == 'desc') ? 1 : -1;
                        } else {
                            return (order == 'desc') ? -1 : 1;
                        }
                    } else {
                        if (parseFloat(a.dataset[sort]) < parseFloat(b.dataset[sort])) {
                            return (order == 'desc') ? 1 : -1;
                        } else {
                            return (order == 'desc') ? -1 : 1;
                        }
                    }
                } else if (a.dataset[sort]) {
                    return -1;
                } else if (b.dataset[sort]) {
                    return 1;
                }
            });

            $items.appendTo(self.$floorplanContainer);

            self.lazyLoadImages();
        },

        lazyLoadImages: function() {
            var self = this;

            if ("function" !== typeof lazyload) return;

            self.lazyload = lazyload(null, {
                src: 'data-original',
                selector: 'img.lazy'
            });
        },

        clearOverlays: function() {
            var self = this;

            self.clearActiveProject();
            self.$projectHover.hide();
            self.$regionHover.hide();
        },

        getQuery: function() {
            var self = this;
            var query = self.getFilters();

            if (self.activeProject) query.project_id = self.activeProject.post_id;

            if (self.initialProject) {
                query = {
                    id: self.initialProject
                };
            }

            var sliders = ['deposit'];

            sliders = sliders.concat(['pricepersqft', 'price', 'beds', 'baths', 'size']);

            if (self.view == 'floorplans') {
                query.include_floorplans = true;
                // query.min_floorplans = 1;

                sliders = sliders.concat(['price', 'beds', 'baths', 'size']);
            } else {
                sliders = sliders.concat(['pricepersqft']);
            }


            sliders.forEach(function(name) {
                if (self[name + 'slider'] && self[name + 'slider'].noUiSlider) {
                    var slider = self[name + 'slider'].noUiSlider;

                    var bounds = slider.get().map(parseFloat),
                        min = bounds[0],
                        max = bounds[1];

                    if (max < slider.options.range.max[0]) {
                        query['max_' + name] = max;
                    }

                    if (min > slider.options.range.min[0]) {
                        query['min_' + name] = min;
                    }
                }
            });

            query['min_lat'] = self.map.getBounds().getSouthWest().lat();
            query['max_lat'] = self.map.getBounds().getNorthEast().lat();
            query['min_lng'] = self.map.getBounds().getSouthWest().lng();
            query['max_lng'] = self.map.getBounds().getNorthEast().lng();

            return query;
        },

        refresh: function() {
            var self = this;

            var query = self.getQuery();

            query.action = 'talkcondo_map_query';

            // condos: status, pricefq, deposit, more
            // floors: price, size, beds, baths, deposit, more
            // floors-specific: price, size, beds, baths

            var isQueryFloorplansSpecifc = false
            var props = ['size', 'beds', 'baths'];
            for (p in props) {
                prop = props[p];
                if (query[`min_${prop}`] || query[`max_${prop}`]) {
                    isQueryFloorplansSpecifc = true;
                    break;
                }
            }

            if (!isQueryFloorplansSpecifc)
                delete query.include_floorplans;
            else
                query.include_floorplans = true;

            // $('.map-inner .overlay').removeClass('hidden');

            $('.project-sidebar').addClass('loading')

            if (this.refreshAjax)
                this.refreshAjax.abort();

            this.refreshAjax = $.ajax({
                url: avia_framework_globals.ajaxurl,
                data: query,
                type: 'GET',
                success: function(projects) {
                    self.clearMarkers();
                    self.setProjects(projects);
                    self.redraw();
                    // $('.map-inner .overlay').addClass('hidden');
                    $('.project-sidebar').removeClass('loading')
                }
            });

            return this.refreshAjax;
        },

        redraw: function() {
            var self = this;

            $('.map-inner').addClass('redrawing');

            self.clearProjectList();
            self.clearFloorplans();
            self.clearMarkers();
            self.plotProjectMarkers();

            if (self.isMobile()) {
                self.$elem.removeClass('sidebar-projects');
                self.$elem.removeClass('sidebar-floorplans');
            }

            if (self.view == 'floorplans') {
                self.populateFloorplans();
            } else {
                self.populateProjects();
            }

            $('.map-inner').removeClass('redrawing');
        },

        drawRegions: function() {
            var self = this;

            if (!self.options.regions) return;

            self.regions = {};
            self.selectedRegionsBounds = new google.maps.LatLngBounds();

            for (n in self.mapData.taxonomies.neighbourhood) {
                var neighbourhood = self.mapData.taxonomies.neighbourhood[n];

                if (!neighbourhood.coordinates) continue;

                var coordArray = [];
                var isSelected = (self.selectedRegions && self.selectedRegions[n]);

                if (self.options.regionsOnlySelected && !isSelected) continue;


                // TODO: avoid loop by formatting coordinates string to pass directly to poly paths object
                for (var c in neighbourhood.coordinates) {
                    var lat = neighbourhood.coordinates[c][0];
                    var lng = neighbourhood.coordinates[c][1];
                    var latLng = new google.maps.LatLng(lat, lng);
                    coordArray.push(latLng);
                }

                if (!coordArray.length) continue;

                var poly = new google.maps.Polygon({
                    paths: coordArray
                });
                poly.neighbourhoodID = n;

                var isSelected = (self.selectedRegions && self.selectedRegions[n]);

                if (isSelected) {
                    poly.setOptions(self.options.regionActiveStyle);
                    self.selectedRegionsBounds.extend(latLng);
                } else {
                    poly.setOptions(self.options.regionStyle);
                }

                poly.setMap(self.map);

                google.maps.event.addListener(poly, 'mouseover', function(e) {

                    var isSelected = (self.selectedRegions && self.selectedRegions[this.neighbourhoodID]);

                    if (self.options.regionHover && !isSelected) {
                        this.setOptions(self.options.regionHoverStyle);
                    }

                    if (self.options.regionHoverLabel) {
                        clearTimeout(self.$regionHover.timer);

                        neighbourhood = self.mapData.taxonomies.neighbourhood[this.neighbourhoodID];
                        latlng = new google.maps.LatLng(neighbourhood.center.lat, neighbourhood.center.lng);

                        if (!self.map.getBounds().contains(latlng)) return;

                        var position = self.calculatePositionfromlatlng(latlng);

                        self.$regionHover.timer = setTimeout(function() {
                            self.$regionHover.html(neighbourhood.name);
                            self.$regionHover.fadeIn('fast');
                            self.$regionHover.css('left', position.left - (self.$regionHover.outerWidth() / 2));
                            self.$regionHover.css('right', 'auto');
                            self.$regionHover.css('bottom', position.bottom);
                        }, 200);
                    }
                });

                google.maps.event.addListener(poly, 'mouseout', function(e) {

                    var isSelected = (self.selectedRegions && self.selectedRegions[this.neighbourhoodID]);

                    if (self.options.regionHover && !isSelected) {
                        this.setOptions(self.options.regionStyle);
                    }

                    if (self.options.regionHoverLabel) {
                        if (e.Ua !== undefined && e.Ua.relatedTarget !== null && e.Ua.relatedTarget.className === 'region-hover') return;
                        clearTimeout(self.$regionHover.timer);
                        self.$regionHover.hide();
                    }

                });

                self.regions[neighbourhood.term_id] = poly;
            }

        },

        zoomToDefault: function() {
            var self = this;

            self.map.setZoom(self.options.mapOptions.zoom);
            self.map.panTo(self.options.mapOptions.center);
            self.map.setCenter(self.options.mapOptions.center);
        },

        zoomToLocations: function() {
            var self = this;
            var bounds = new google.maps.LatLngBounds();
            var count = false;

            // old version based on regions, handles taxonomy term links corretly
            for (n in self.selectedRegions) {
                count = true;
                var neighbourhood = self.mapData.taxonomies.neighbourhood[n];

                if (!neighbourhood) continue;
                if (!neighbourhood.coordinates) continue;

                for (var c in neighbourhood.coordinates) {
                    var lat = neighbourhood.coordinates[c][0];
                    var lng = neighbourhood.coordinates[c][1];
                    var latLng = new google.maps.LatLng(lat, lng);
                    bounds.extend(latLng);
                }
            }

            //  new version, works fine with seaches, fails on links
            for (i in self.selectedLocations) {
                count = true;
                var item = self.selectedLocations[i];

                if (!item || !item.location) continue;

                if ('Point' == item.location.type) {
                    var lng = item.location.coordinates[0];
                    var lat = item.location.coordinates[1];
                    var latLng = new google.maps.LatLng(lat, lng);
                    bounds.extend(latLng);
                    // in case we have issues with precision
                    var precision = 0.0003
                    latLng = new google.maps.LatLng(+precision + lat, +precision + lng);
                    bounds.extend(latLng);
                    latLng = new google.maps.LatLng(-precision + lat, -precision + lng);
                    bounds.extend(latLng);
                } else if ('Region' == item.location.type) {
                    var geometry = JSON.parse(item.location.geometry)
                    for (var c in geometry) {
                        var lng = geometry[c][1];
                        var lat = geometry[c][0];
                        var latLng = new google.maps.LatLng(lat, lng);
                        bounds.extend(latLng);
                    }
                }
            }

            // add bounds.extend for selectedMarkers ?

            if (count) {
                self.map.fitBounds(bounds);
            }
        },

        clearRegions: function() {
            var self = this;

            for (n in self.regions) {
                var region = self.regions[n];
                if (typeof(region) === 'object' && typeof(region.setMap) === 'function') {
                    region.setMap(null);
                }
            }

            self.regions = {};
        },

        clearProjectMarkers: function() {
            var self = this;

            for (var p in self.projects) {
                var project = self.projects[p];
                if (project && typeof(project.marker) === 'object' && typeof(project.marker.setMap) === 'function') {
                    project.marker.setMap(null);
                }
            }
        },

        clearMarkers: function() {
            var self = this;

            if (self.cluster) {
                self.cluster.clearMarkers();
            } else {
                self.clearProjectMarkers();
            }
        },

        plotGeoJson: function(url) {
            var self = this;

            self.map.data.loadGeoJson(self.options.geoJsonURL);
        },

        plotProjectMarkers: function() {
            var self = this;
            var bounds = new google.maps.LatLngBounds();

            for (p in self.projects) {

                var project = self.projects[p];

                if (!self.cluster && !self.inBounds(project)) continue;
                var latLng = new google.maps.LatLng(project.coords[1], project.coords[0]);
                bounds.extend(latLng);

                var opts = {
                    position: latLng,
                    projectid: project.post_id,
                    classes: ['tc-marker'],
                }

                for (i in project.terms) {
                    opts.classes.push(project.terms[i]);
                }

                // if (self.view === 'floorplans') {
                if (project.floorplans) {
                    available_floorplans = project.floorplans.length;
                } else {
                    available_floorplans = project.available_floorplans;
                }

                if (self.pinData === 'avg-price') {
                    opts.content = self.money(project.pricepersqft, false);
                } else if (self.pinData === 'price-range') {
                    opts.content = self.money(project.price.min, true) + ' - ' + self.money(project.price.max, true);
                } else if (self.pinData === 'suite-size-range') {
                    opts.content = project.size.min + ' - ' + project.size.max + ' sq.ft';
                } else if (self.pinData === 'suites') {
                    opts.content = available_floorplans + ' units';
                } else {
                    opts.content = self.money(project.price.min, true) + ' - ' + self.money(project.price.max, true);
                }
                if (available_floorplans === 1) opts.content = opts.content.replace('units', 'unit');

                //TODO: do not rely on side effects
                if (opts.content.includes('undefined') || opts.content.includes('NaN') || opts.content.includes('0 - 0') || opts.content.includes('0 units')) {
                    opts.content = "";
                } else {
                    opts.classes.push('floorplans');
                }




                // }

                if (self.initialProject && project.post_id == self.initialProject) {
                    opts.classes.push('active');
                }

                var mark = new CustomMarker(opts);

                if (self.cluster) {
                    self.cluster.addMarker(mark);
                } else {
                    mark.setMap(self.map);
                }

                google.maps.event.addListener(mark, 'click', function() {
                    self.markerClicked = true;
                    self.setActiveProject(this.projectid);
                });

                google.maps.event.addListener(mark, 'mouseover', function(e) {
                    var thisProject = self.getProject(this.projectid);
                    if (thisProject != self.activeProject) {
                        if (typeof thisProject.marker === 'object') {
                            thisProject.marker.setActive();
                            self.setProjectHover(thisProject);
                        }
                    }
                });

                google.maps.event.addListener(mark, 'mouseout', function(e) {
                    clearTimeout(self.$projectHover.timer);
                    var thisProject = self.getProject(this.projectid);
                    if (thisProject != self.activeProject) {
                        if (typeof thisProject.marker === 'object') {
                            thisProject.marker.setInactive();
                            self.$projectHover.fadeOut()
                        }
                    }
                });

                self.projects[p].marker = mark;

                // extract as plot featured projects ?
                for (i in self.selectedLocations) {
                    var item = self.selectedLocations[i];

                    if (!item || !item.location) continue

                    if ('Point' == item.location.type) {
                        if (project.post_id == item.id) {
                            mark.setAsSearchResultOn()
                        }
                    }
                }
            }

            self.projectMarkerBounds = bounds;
            self.bounds = self.projectMarkerBounds;
        },

        clearProjectList: function() {
            var self = this;

            self.$projectContainer.find('.project').remove();
            self.$projectContainer.html('');
        },

        clearFloorplans: function() {
            var self = this;

            self.$floorplanContainer.find('.floorplan').remove();
            self.$floorplanContainer.html('');
        },

        populateProjects: function() {

            var self = this;

            clearTimeout(self.populateProjectsTimeout);

            if (!self.options.enableProjectSidebar) return;
            if (!self.$projectSidebar.is(':visible') && !self.isMobile()) return;

            self.$projectSidebar.addClass('loading');

            self.visibleProjects = 0;

            for (p in self.projects) {

                var project = self.projects[p];

                // // todo: remove me for tests only
                // if(project.platinum_access) {
                //     project.terms.push('launching-soon');
                // }


                if (!self.inBounds(project)) continue;

                var $listItem = $('<div>')
                    .addClass('project')
                    .addClass('project-card')
                    .attr('data-projectid', project.post_id)
                    .attr('data-pricepersqft', project.pricepersqft)
                    .attr('data-priority', project.sort_priority)
                    .attr('data-updated', project.updated);

                if (project.featured) $listItem.attr('data-featured', project.featured);

                if (project.strings.salesstatus.indexOf('Selling') >= 0) $listItem.attr('data-selling', true);


                $thumbnail = $('<div>').addClass('project-thumbnail');

                var imgurl = (project.thumbnail && !self.options.imagePlaceholders) ? project.thumbnail : self.options.imagePlaceholder;
                if (self.options.lazyLoadImages) {
                    $img = $('<img>').addClass('lazy').attr('data-original', imgurl).attr('src', self.options.imagePlaceholder);
                } else {
                    $img = $('<img>').attr('src', imgurl);
                }

                var $thumbnailLink = $('<a>').attr('href', project.permalink).attr('target', '_blank');

                $img.appendTo($thumbnailLink);

                $thumbnailLink.appendTo($thumbnail);

                var $info = $('<div>').addClass('project-info');

                var $header = $('<div>').addClass('info-header');

                $('<a>')
                    .addClass('project-title')
                    .attr('href', project.permalink)
                    .attr('target', '_blank')
                    .append(project.title)
                    .appendTo($header);

                if (project.platinum_access) $('<span class="platinum-access"></span>').appendTo($header);
                if (project.terms.includes('launching-soon')) $('<span class="launching-soon"></span>').appendTo($header);
                if (project.terms.includes('special-incentives')) $('<span class="special-incentives"></span>').appendTo($header);

                $header.appendTo($info);

                if (project.price.min || project.pricepersqft) {
                    var $p = $('<p>');

                    $price = $('<span>').addClass('project-price')
                        .append('<span class="project-pricedfrom">' + self.money(project.price.min) + '</span>');

                    if (project.price.max) {
                        $price.append(' to <span class="project-pricedto">' + self.money(project.price.max) + '</span>');
                    }
                    $p.append($price);

                    $p.appendTo($info);

                    if (project.pricepersqft) {
                        $p = $('<p>');
                        $p.append('<span class="project-avgpricepersqft"><b class="money-lg" style="font-size: 30px">' + self.money(project.pricepersqft) + '</b>/sqft Avg.');
                        $p.appendTo($info);
                    }

                }

                if (project.size.min || project.available_floorplans > 0) {

                    $p = $('<p>');

                    if (project.size.min) {
                        str = project.size.min +
                            (project.size.max && project.size.min != project.size.max ? ' to ' + project.size.max : '') +
                            ' sq.ft.';

                        $('<span>').addClass('project-sqfootage').append(str).appendTo($p);
                    }

                    if (project.occupancy_date) {
                        $p.append('<span class="project-occupancydate">' + project.occupancy_date + '</span>');
                    }

                    $p.appendTo($info);

                }

                // $p = $('<p>').addClass('project-status');
                // if (project.strings.salesstatus) {
                // $p.append('<span class="project-status__current">' + project.strings.salesstatus + '</span>');
                // }
                // $p.appendTo($info);

                $footer = $('<div>').addClass('info-footer');

                $('<p>').addClass('project-location')
                    .append(project.address)
                    .append((project.strings.neighbourhood) ? ', ' + project.strings.neighbourhood : '')
                    .append(project.strings.city ? ', ' + project.strings.city : '')
                    .appendTo($footer);

                if (project.strings.developer) {
                    $('<p class="project-developers">')
                        .append('By ' + (project.strings.developer.length > 40 ? project.strings.developer.substring(0, 37) + '...' : project.strings.developer))
                        .appendTo($footer);
                }

                $footer.appendTo($info);

                $more = $('<div>')
                    .addClass('moreinfo');
                // .append('<a href="' + project.permalink + '">More Info <i class="fa fa-fw fa-arrow-right"></i></a>');

                var count = project.floorplans ? project.floorplans.length : 0;
                count = count ? count : project.available_floorplans;

                if (count) {
                    $('<a>').addClass('floorplans-available')
                        .attr('href', project.permalink + '/floorplans')
                        .append(count + (count == 1 ? ' Condo' : ' Condos') + ' for Sale.  View Floor Plans <i class="fa fa-sm fa-plus"></i>')
                        .appendTo($more);
                } else {
                    $('<span>').addClass('project-sqfootage')
                        .append('Floor Plans Coming Soon')
                        .appendTo($more);
                }

                $listItem.append($('<div>').addClass('flex').append($thumbnail).append($info));
                $listItem.append($more);

                self.projects[p].listItem = $listItem;

                $listItem.appendTo(self.$projectContainer);

                self.visibleProjects++;

            }

            self.populateProjectsTimeout = setTimeout(function() {
                if (!self.visibleProjects) {
                    self.$projectSidebarHeader.find('.count').html('0 Results');
                    self.$projectContainer.html('<div class="no-results"><i class="fa fa-fw fa-warning"></i> Your criteria returned no results.<br>Move the map and change your criteria to see more results</div>');
                } else {
                    self.$projectSidebarHeader.find('.count').html(self.visibleProjects + ' Results');
                    if (self.options.lazyLoadImages) self.lazyLoadImages();
                }
                self.$projectSidebar.removeClass('loading');
            }, 500);

        },

        populateFloorplans: function(page) {
            var self = this;
            var query = self.getQuery();

            query.action = 'talkcondo_floorplan_query';

            if (self.activeProject) {
                query.project_id = self.activeProject.post_id;
            }
            query.per_page = '30';
            query.page = (page) ? page : 1;

            $.ajax({
                url: avia_framework_globals.ajaxurl,
                data: query,
                type: 'GET',
                success: function(response) {

                    if (!response[0]) return;
                    response = response[0];
                    if (!response.results.length) return;

                    self.visibleFloorplans = response.total;
                    clearTimeout(self.populateFloorplansTimeout);

                    self.$floorplanSidebar.addClass('loading');

                    self.renderFloorplans(response.results);

                    self.populateFloorplansTimeout = setTimeout(function() {
                        if (!self.visibleFloorplans) {
                            self.$floorplanSidebarHeader.find('.count').html('0 Results');
                            self.$floorplanContainer.html('<div class="no-results"><i class="fa fa-fw fa-warning"></i> Your criteria returned no results.<br>Move the map and change your criteria to see more results</div>');
                        } else {
                            self.$floorplanSidebarHeader.find('.count').html(self.visibleFloorplans + ' Available Suites in ' + Object.keys(self.projects).length + ' Condos');
                            if (self.options.lazyLoadImages) self.lazyLoadFloorplanImages();
                            self.$floorplanSidebarHeader.find('.count').html(self.visibleFloorplans + ' Results');
                            if (self.options.lazyLoadImages) self.lazyLoadImages();
                        }
                        self.$floorplanSidebar.removeClass('loading');
                    }, 500);

                    self.$floorplanContainer.find('.load-more').remove();
                    if ((response.page * response.per_page) < response.total) {
                        self.$floorplanContainer.append('<div class="load-more" style="text-align: center; display: none;" data-page="' + (response.page + 1) + '">Load More...</div>');
                    }

                }
            });

        },

        renderFloorplans(floorplans) {
            var self = this;

            for (f in floorplans) {
                var floorplan = floorplans[f];
                self.generateFloorplanTemplate(floorplan.project, floorplan).appendTo(self.$floorplanContainer);
            }

        },

        generateFloorplanTemplate(project, floorplan) {
            var self = this;

            if (self.isMobile() || self.floorplanLayout == 'grid') {
                self.$floorplanContainer.removeClass('list').addClass('grid');
                self.$floorplanSidebar.find('.list-entry-headers').hide();
                return self.generateFloorplanGridTemplate(project, floorplan);
            } else {
                self.$floorplanContainer.removeClass('grid').addClass('list');
                self.$floorplanSidebar.find('.list-entry-headers').show();
                return self.generateFloorplanListTemplate(project, floorplan);
            }
        },

        generateFloorplanGridTemplate(project, floorplan) {
            var self = this;

            var $floorplan = $('<div>').addClass('floorplan').addClass('grid-entry');
            $floorplan.addClass('available');

            $floorplan.attr('data-id', floorplan.image);
            $floorplan.attr('data-projectid', project.post_id);
            $floorplan.attr('data-projectname', project.title);
            $floorplan.attr('data-suite-name', floorplan.suite_name);
            $floorplan.attr('data-size', floorplan.size);
            $floorplan.attr('data-beds', floorplan.beds);
            $floorplan.attr('data-baths', floorplan.baths);
            $floorplan.attr('data-exposure', floorplan.exposure);
            $floorplan.attr('data-thumbnail', floorplan.thumbnail);
            $floorplan.attr('data-fullimage', floorplan.fullimage);
            $floorplan.attr('data-project-url', project.permalink);
            $floorplan.attr('data-floorplan-url', floorplan.url);

            if (!project.hide_pricing) {
                $floorplan.attr('data-price', floorplan.price);
                $floorplan.attr('data-pricepersqft', floorplan.pricepersqft);
            }

            var thumbnail = (self.options.imagePlaceholders) ? self.options.imagePlaceholder : floorplan.thumbnail;
            if (self.options.lazyLoadImages) {
                var $img = $('<img>').addClass('lazy').attr('data-original', thumbnail).attr('alt', floorplan.alt);
            } else {
                var $img = $('<img>').attr('alt', floorplan.alt).attr('src', thumbnail);
            }
            var $thumbnail = $('<div>').addClass('floorplan__thumbnail').append($img);
            var fullimage = floorplan.fullimage;
            var html = '';
            html += '<div class="floorplan__thumbnail-overlay">';
            html += '<a class="quick-view" href="#"><i class="fa fa-fw fa-search-plus"></i><br>Quick View</a>';
            html += '</div>';
            $thumbnail.append(html);
            var $floorplan_left = $('<div>').addClass('grid-entry-left').append($thumbnail);
            $left_buttons = $('<div>').addClass('simpleflex').append('<a class="floorplan_save"><i class="fa fa-heart-o fa-lg"></i></a>');
            $left_buttons.append('<a class="floorplan__latest-pricing hidden-xs" href="' + floorplan.url + '">Buy</a>');
            $floorplan_left.append($left_buttons);

            $floorplan.append($floorplan_left);
            var $floorplan_right = $('<div>').addClass('grid-entry-right');
            $floorplan_right.append(
                $('<div>').addClass('floorplan__title')
                .append(floorplan.suite_name)
                .append('<span class="floorplan__availability--label"></span>')
            );

            var $info = $('<div>').addClass('project-info');

            if (project.thumbnail && !self.options.imagePlaceholders) {
                $info.append($('<img>').attr('src', project.thumbnail));
            } else {
                $info.append($('<img>').attr('src', self.options.imagePlaceholder));
            }

            $('<p>').addClass('project-location')
                .append('<i class="fa fa-fw fa-map-marker"></i> ')
                .append(project.strings.city)
                .append((project.strings.neighbourhood) ? ' (' + project.strings.neighbourhood + ')' : '')
                .appendTo($info);
            if (project.strings.developer) {
                $('<p>').addClass('project-developers')
                    .append('<span data-av_iconfont="entypo-fontello" data-av_icon="" aria-hidden="true" class="label iconfont"></span> &nbsp;')
                    .append(project.strings.developer)
                    .appendTo($info);
            }
            $p = $('<p>').addClass('project-status');
            if (project.strings.status) {
                $p.append('<i class="fa fa-fw fa-wrench"></i> ').append(project.strings.status);
            }
            if (project.occupancy_date) {
                $p.append(' <i class="fa fa-fw fa-check-circle"></i> ').append(project.occupancy_date);
            }
            $p.appendTo($info);

            $floorplan_right.append($('<div>').addClass('floorplan__project_t')
                .append(project.title)
                // .append(' <i class="fa fa-fw fa-angle-down"></i> ')
                // .append(' <i class="fa fa-fw fa-angle-up"></i> ')
            );

            $floorplan_right.append($info);

            $floorplan_right.append(
                $('<div>').addClass('floorplan__info')
                .append('<span class="floorplan__beds">' + floorplan.beds + ' Bed</span>')
                .append('<span class="floorplan__baths">' + floorplan.baths + ' Bath</span>')
                .append('<span class="floorplan__size">' + floorplan.size + 'sq.ft.')
                .append('<br>')
                .append('<span class="floorplan__exposure">' + floorplan.exposure + '</span>')
                //.append('<span class="floorplan__floors">' + floorplan.floor_range + '</span>')
            );
            if (project.hide_pricing) {
                $floorplan.append('<div class="floorplan__view-all">Contact For Pricing');
            } else {
                $floorplan_right.append($('<div>').addClass('floorplan__price').append(self.money(floorplan.price) + '<small>' + self.money(floorplan.pricepersqft) + '/ft</small>'));
            }

            $floorplan_right.append($('<a>').addClass('floorplan__latest-pricing hidden-xs').attr('href', project.leadpageslink).html('Get Latest Pricing'));

            $floorplan.append($floorplan_right);

            return $floorplan;

        },

        generateFloorplanListTemplate(project, floorplan) {
            var self = this;

            var html = '';
            var $floorplan = $('<div>').addClass('floorplan').addClass('list-entry');
            $floorplan.addClass('available');
            $floorplan.attr('data-id', floorplan.image);
            $floorplan.attr('data-projectid', project.post_id);
            $floorplan.attr('data-projectname', project.title);
            $floorplan.attr('data-suite-name', floorplan.suite_name);
            $floorplan.attr('data-size', floorplan.size);
            $floorplan.attr('data-beds', floorplan.beds);
            $floorplan.attr('data-baths', floorplan.baths);
            $floorplan.attr('data-exposure', floorplan.exposure);
            $floorplan.attr('data-thumbnail', floorplan.thumbnail);
            $floorplan.attr('data-fullimage', floorplan.fullimage);
            $floorplan.attr('data-project-url', project.permalink);
            $floorplan.attr('data-floorplan-url', floorplan.url);
            $floorplan.attr('data-availability', 'available');

            if (!project.hide_pricing) {
                $floorplan.attr('data-price', floorplan.price);
                $floorplan.attr('data-pricepersqft', floorplan.pricepersqft);
            }

            // $floorplan.append('<div class="floorplan__availability"><i class="fa fa-circle"></i></div>');

            var thumbnail = (self.options.imagePlaceholders) ? self.options.imagePlaceholder : floorplan.thumbnail;
            if (self.options.lazyLoadImages) {
                var $img = $('<img>').addClass('lazy').attr('src', self.options.imagePlaceholder).attr('data-original', thumbnail).attr('alt', floorplan.alt);
            } else {
                var $img = $('<img>').attr('alt', floorplan.alt).attr('src', thumbnail);
            }
            var $thumbnail = $('<div>').addClass('floorplan__thumbnail').append($img);

            html = '<div class="floorplan__thumbnail-overlay">';
            html += '<a class="quick-view" href="#"><i class="fa fa-fw fa-search-plus"></i><br>Quick View</a>';
            html += '</div>';
            $thumbnail.append(html);
            $floorplan.append($thumbnail);

            $floorplan.append('<div class="floorplan__title"><a href="' + floorplan.url + '" target="_blank">' + floorplan.suite_name + '</a></div>');

            html = '<div class="floorplan__project">';
            if (project.platinum_access) html += '<span class="platinum-access star"></span>';
            if (project.terms.includes('launching-soon')) html += '<span class="launching-soon rocket"></span>';
            if (project.terms.includes('special-incentives')) html += '<span class="special-incentives tag"></span>';
            html += '<a href="' + project.permalink + '">' + project.title + '</a>';
            html += '</div>';
            $floorplan.append(html);

            $floorplan.append('<div class="floorplan__type">' + floorplan.beds + ' beds' + '<br>' + floorplan.baths + ' bath' + '</div>');
            $floorplan.append('<div class="floorplan__size">' + floorplan.size + ' sq.ft</div>');
            if (project.hide_pricing) {
                $floorplan.append('<div class="floorplan__view-all">Contact For Pricing');
            } else {
                $floorplan.append('<div class="floorplan__view-all"><b>' + self.money(floorplan.price) + '</b>' + self.money(floorplan.pricepersqft) + '/ft</div>');
            }

            return $floorplan;
        },

        triggerFloorplanQuickView: function() {

            var self = this;

            var floorplan_popup = {
                type: 'ajax',
                mainClass: 'mfp-zoom-in floorplan-quickview',
                tLoading: '',
                tClose: '',
                removalDelay: 300,
                closeBtnInside: false,
                closeOnBgClick: false,
                closeOnContentClick: false,
                midClick: false,
                showCloseBtn: false,
                enableEscapeKey: true,
                fixedContentPos: true,
                alignTop: true,

                image: {
                    titleSrc: function(item) {
                        var title = item.el.attr('title');
                        if (!title) title = item.el.find('img').attr('title');
                        if (typeof title == "undefined") return "";
                        return title;
                    }
                },

                gallery: {
                    enabled: true,
                    preload: [1, 1],
                    arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"><i class="fa fa-chevron-right"></i><i class="fa fa-chevron-left"></i></button>',
                    navigateByImgClick: false,
                    tPrev: '',
                    tNext: '',
                    tCounter: '%curr% / %total%',
                },

                callbacks: {
                    ajaxContentAdded: function() {
                        var self = this;
                        $('.floorplan-quickview__image > img').ezPlus({
                            zoomType: 'inner',
                            cursor: 'crosshair'
                        });
                        $('.floorplan-quickview__close').on('click', function() {
                            $.magnificPopup.instance.close();
                        });
                    },
                    open: function() {
                        $.magnificPopup.instance.next = function() {
                            var self = this;
                            self.wrap.removeClass('mfp-image-loaded');
                            setTimeout(function() {
                                $.magnificPopup.proto.next.call(self);
                            }, 120);
                        };
                        $.magnificPopup.instance.prev = function() {
                            var self = this;
                            self.wrap.removeClass('mfp-image-loaded');
                            setTimeout(function() {
                                $.magnificPopup.proto.prev.call(self);
                            }, 120);
                        };
                    },
                    imageLoadComplete: function() {
                        var self = this;
                        setTimeout(function() {
                            self.wrap.addClass('mfp-image-loaded');
                        }, 16);
                    }
                }
            };

        },

        setActiveProject: function(projectid) {

            var self = this;
            var project = self.getProject(projectid);

            self.clearActiveProject();

            self.activeProject = project;

            if (typeof project.marker === 'object') {
                project.marker.setActive();
            }

            if (self.isMobile()) {

                self.createActiveProjectCard();

            } else if (self.view == 'floorplans') {

                self.clearFloorplans();
                self.populateFloorplans();

            } else if (self.view == 'projects') {

                self.$elem.addClass('active-project');

                if (project.listItem !== undefined) {
                    project.listItem.addClass('active');

                    self.$mapSidebar.animate({
                        scrollTop: project.listItem.offset().top - self.$mapSidebar.offset().top + self.$mapSidebar.scrollTop()
                    }, 500);

                    $(window).trigger('resize');
                }

            }

        },

        createActiveProjectCard: function() {
            var self = this;

            if (!self.activeProject) return;

            self.$projectInfoCard.find('.project').html('').append(self.projectInfoCardTemplate(self.activeProject));

            var query = self.getQuery();
            query.id = self.activeProject.post_id;
            query.action = 'talkcondo_map_query';
            query.include_floorplans = true;

            $.ajax({
                url: avia_framework_globals.ajaxurl,
                data: query,
            }).done(function(response) {
                var project = response[0];
                var $div = $('<div class="floorplan-sidebar"></div>');
                if (project && project.floorplans) {
                    for (i in project.floorplans) {
                        $div.append(self.generateFloorplanGridTemplate(project, project.floorplans[i]));
                    }
                }
                self.$projectInfoCard.find('.floorplan-sidebar').replaceWith($div);
                self.$projectInfoCard.show();
            });
        },

        // toggleProjectInfoCard: function(dir) {

        //     var self = this;
        //     var $sidebar = self.$projectInfoCard.find('.floorplan-sidebar');

        //     if (dir === true) {
        //         self.$projectInfoCard.css('top', '');
        //         $sidebar.css('height', 'auto').addClass('open');
        //     } else if (dir === false) {
        //         self.$projectInfoCard.css('top', '');
        //         $sidebar.css('height', 0).removeClass('open');
        //     } else {
        //         self.$projectInfoCard.css('top', '');
        //         $sidebar.css('height', '').toggleClass('open');
        //     }
        // },

        clearActiveProject: function() {

            var self = this;

            if (!self.activeProject) return;
            self.$elem.removeClass('active-project');

            self.$projectSidebar.find('.project').show();

            if (self.activeProject) {
                if (self.activeProject.listItem) self.activeProject.listItem.removeClass('active');
                if (typeof self.activeProject.marker === 'object') {
                    self.activeProject.marker.setInactive();
                }
                self.activeProject = null;
            }

            self.$projectInfoCard.find('.floorplan-sidebar').removeClass('open');
            self.$projectInfoCard.find('.project').html('');
            self.$projectInfoCard.hide();
        },

        setProjectHover: function(project) {
            var self = this;

            var $content = self.projectHoverTemplate(project);
            var position = self.calculatePosition(project.marker);

            clearTimeout(self.$projectHover.timer);
            self.$projectHover.timer = setTimeout(function() {
                self.$projectHover.html($content);
                self.$projectHover.css('bottom', position.bottom + 30);
                self.$projectHover.fadeIn('fast');
                self.$projectHover.css('left', position.left - (self.$projectHover.outerWidth() / 2));
                self.$projectHover.css('right', 'auto');
            }, 200);

        },

        projectHoverTemplate(project) {
            var self = this;
            var $content = $('<div>');
            var $title = $('<p>').addClass('project-title')
                .html('<b>' + project.title + '</b>')
                .appendTo($content);

            if (project.platinum_access) $title.append('&nbsp;&nbsp;<span class="platinum-access"></span>');
            if (project.terms.includes('launching-soon')) $title.append('&nbsp;&nbsp;<span class="launching-soon"></span>');
            if (project.terms.includes('special-incentives')) $title.append('&nbsp;&nbsp;<span class="special-incentives"></span>');

            if (project.price.min || project.pricepersqft) {
                var $p = $('<p>');
                if (project.price) {
                    $price = $('<span>').addClass('project-price')
                        .append('<b>' + self.money(project.price.min) + '</b>');
                    if (project.price.max) {
                        $price.append(' to <b>' + self.money(project.price.max) + '</b>');
                    }
                    $p.append($price);
                }

                if (project.pricepersqft) {
                    $p.append('<span>' + self.money(project.pricepersqft) + '/sqft Avg.</span>');
                }
                $p.appendTo($content);
            }
            return $content;
        },

        projectInfoCardTemplate: function(project) {
            var self = this;
            var $content = self.projectHoverTemplate(project);

            return $content;
        },

        getFilters: function() {
            this.setFilters();

            return this.filters || {};
        },

        setFilters: function() {

            var self = this;

            var filters = {};

            if (self.dataTaxonomy && self.dataTerm) {
                filters[self.dataTaxonomy] = [self.dataTerm];
            }

            $buttons = $('.filters .filter-button');

            if ($buttons.length == 0) return false;

            self.resalechecked = false;
            self.soldoutchecked = false;

            $buttons.each(function() {
                var $this = $(this);
                var $total = $this.find('.count');
                var $title = $this.find('.title');
                var $active_options = $this.find('.filter-option.active');
                var count = $active_options.length;

                if (!count) {
                    $this.removeClass('filtered');
                    $total.html('').hide();
                    $title.html($title.attr('data-placeholder'));
                    return;
                }

                $this.addClass('filtered');

                if ($this.hasClass('floorplans__filter-sliders')) return;

                var title = '';
                $active_options.each(function() {
                    var $this = $(this);
                    var taxonomy = $this.data('taxonomy');
                    var term = $this.data('term');
                    var label = $this.html();
                    if (typeof(filters[taxonomy]) !== 'object') filters[taxonomy] = [];
                    title += label;
                    title += ', ';

                    filters[taxonomy].push(term);

                    if (term === 'resale') self.resalechecked = true;
                    if (term === 'developer-sold-out') self.soldoutchecked = true;

                });

                $title.html(title.substr(0, title.length - 2));

            });

            self.filters = filters;

        },

        // restoreFilters: function () {
        //     var self = this;
        //
        //
        //     // JSON.parse(
        //     //     decodeURI(
        //     //         encodeURI(
        //     //             JSON.stringify(filters)
        //     //         )
        //     //     )
        //     // )
        //
        //     // {"city":["ajax"],"developer":["emery-homes","carttera-private-equities","55-wellesley-e-developments-inc"],"type":["condo","luxury","townhouse"],"status":["complete","pre-construction","under-construction"],"occupancy_date":[1970,1971,1972,1973,1974],"max_pricepersqft":1800,"min_pricepersqft":700,"max_price":2300000,"min_price":450000,"max_beds":2.5,"min_beds":0.5,"max_baths":2.5,"min_baths":0.5,"min_size":600,"min_lat":43.81670118026036,"max_lat":43.92164522572318,"min_lng":-79.15672294355468,"max_lng":-78.97132865644531,"action":"talkcondo_map_query"}
        //
        //
        //     // var filters = JSON.parse(decodeURI(location.hash));
        //     var filters =
        //         {
        //             "city": ["ajax"],
        //             "developer": ["emery-homes", "carttera-private-equities", "55-wellesley-e-developments-inc"],
        //             "type": ["condo", "luxury", "townhouse"],
        //             "status": ["complete", "pre-construction", "under-construction"],
        //             "occupancy_date": [1970, 1971, 1972, 1973, 1974],
        //             "max_pricepersqft": 1800,
        //             "min_pricepersqft": 700,
        //             "max_price": 2300000,
        //             "min_price": 450000,
        //             "max_beds": 2.5,
        //             "min_beds": 0.5,
        //             "max_baths": 2.5,
        //             "min_baths": 0.5,
        //             "min_size": 600,
        //             "min_lat": 43.81670118026036,
        //             "max_lat": 43.92164522572318,
        //             "min_lng": -79.15672294355468,
        //             "max_lng": -78.97132865644531,
        //             "action": "talkcondo_map_query"
        //         }
        //
        //
        //     //todo: replace history
        //
        //     var $filters = $('.filters')
        //     for (f in filters) {
        //         var [minmax, name] = f.split("_")
        //         if (slider = self[name + 'slider']) {
        //             // sliders
        //             var range = slider.noUiSlider.get()
        //             range["min" == minmax ? 0 : 1] = filters[f];
        //             slider.noUiSlider.set(range)
        //         } else {
        //             // taxonomies
        //             taxonomy = f;
        //             if (Array.isArray(filters[taxonomy])) {
        //                 for (t in filters[taxonomy]) {
        //                     term = filters[taxonomy][t];
        //
        //                     $button = $filters.find(`button.filter-option[data-taxonomy="${taxonomy}"][data-term="${term}"]`);
        //                     console.log({taxonomy, term, button: $button.length })
        //                     $button.trigger('click');
        //                 }
        //             }
        //         }
        //     }
        //
        //     // suggestion = {name: "Allegra Homes", slug: "allegra-homes"};
        //     // get developer suggestion
        //     if(filters.developer && filters.developer.length > 0){
        //
        //         var developers = self.mapData.taxonomies.developer;
        //         for(i in filters.developer){
        //             var slug = filters.developer[i];
        //             for(o in developers){
        //                 if(developers[o].slug == slug){
        //                     var suggestion = {slug, name: developers[o].name};
        //                     self.$developerInput.trigger('typeahead:select', suggestion);
        //                     break;
        //                 }
        //             }
        //         }
        //     }
        // },

        inBounds: function(project) {

            var self = this;
            var bounds = self.map.getBounds();

            if (!bounds) {
                return true;
            }

            return bounds.contains(new google.maps.LatLng(project.coords[1], project.coords[0]));
        },

        prettySearch() {
            $("#map-search .search-form #s").parent().addClass("input-container")

            $wrapper = $("#map-search .input-container");
            $chips = $("#map-search .chip");
            if ($chips.length && $wrapper.offset().top > $chips.offset().top) {
                $wrapper.css("display", "none")
            } else {
                $wrapper.css("display", "inline-block")
            }
        },

        getShareLink() {
            var self = this;
            var $this = $(this);

            var query = self.getQuery();

            delete query['min_lat'];
            delete query['min_lng'];
            delete query['max_lat'];
            delete query['max_lng'];

            query.lat = self.map.getCenter().lat();
            query.lng = self.map.getCenter().lng();
            query.zoom = self.map.getZoom();

            var url = document.location.origin + document.location.pathname;

            if (Object.keys(query).length > 0) {
                url = url + '?' + decodeURIComponent($.param(query));
            }

            return url;
        },

        generateChipForSelecterRegion() {
            var self = this;

            var count = false;
            var html = ''

            for (r in self.selectedRegions) {
                count = true;

                var data = {
                    id: r,
                    title: self.selectedRegions[r],
                    taxonomy: 'neighbourhood',
                }

                self.selectedLocations[r] = data;

                var template = `<div class="previous-result chip"><a class="ajax_search_entry for_map" href="javascript:void(0)" data-json='{"id": ${data.id},"taxonomy": "neighbourhood","title": "${data.title}"}'><span class="ajax_search_content"><span class="ajax_search_title">${data.title}</span></span></a></div>`;
                html += template;

                break;
            }

            $(html).prependTo($("#map-search .search-form"))

            self.prettySearch();
        },

        filterRegions: function() {

            var self = this;
            var regions = self.$elem.find('select#regions').val();

            self.selectedRegions = {};

            if (!regions || !regions.length) return false;

            for (r in regions) {
                var id = regions[r];
                self.selectedRegions[id] = self.mapData.taxonomies.neighbourhood[id].name;
            }

            return true;

        },

        setBounds: function(bounds) {

            var self = this;

            self.bounds = bounds;

            self.map.fitBounds(self.bounds);

        },

        calculatePosition: function(marker) {

            var self = this;

            return self.calculatePositionfromlatlng(marker.getPosition());

        },

        calculatePositionfromlatlng: function(latlng) {

            var self = this;

            var scale = Math.pow(2, self.map.getZoom());
            var point = self.map.getProjection().fromLatLngToPoint(latlng);
            var ne = self.map.getProjection().fromLatLngToPoint(self.map.getBounds().getNorthEast());
            var sw = self.map.getProjection().fromLatLngToPoint(self.map.getBounds().getSouthWest());

            var top = (point.y - ne.y) * scale;
            var left = (point.x - sw.x) * scale;
            var right = (ne.x - point.x) * scale;
            var bottom = (sw.y - point.y) * scale;

            return {
                top: top,
                bottom: bottom,
                left: left,
                right: right
            };

        },

        developerTypeahead: function() {

            var self = this;

            self.$developerInput = $('input#developers');

            if (typeof($.fn.typeahead) === 'function') {
                var devs = [];
                for (d in self.mapData.taxonomies.developer) {
                    devs.push({
                        slug: self.mapData.taxonomies.developer[d].slug,
                        name: self.mapData.taxonomies.developer[d].name
                    })
                }
                self.$developerInput.typeahead({
                    highlight: true
                }, {
                    name: 'developers',
                    display: 'name',
                    source: new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        local: devs
                    })
                });
            }

            self.$developerInput.on('keyup', function(e) {
                var $this = $(this);
                var key = e.keyCode || e.which;
                if (key == 13) {
                    var $first = $this.siblings('.tt-menu').find('.tt-suggestion').first();
                    if ($first) $first.trigger('click');
                }
            });

            self.$developerInput.on('typeahead:select', function(ev, suggestion) {
                $this = $(this);
                var in_array = false;
                if (self.filters && self.filters.developer) {
                    for (d in self.filters.developer) {
                        if (self.filters.developer[d].slug == suggestion.slug) in_array = true;
                    }
                }
                if (!in_array) $this.closest('div').append('<span class="filter-option rounded active" data-taxonomy="developer" data-term="' + suggestion.slug + '">' + suggestion.name + '<i class="fa fa-fw fa-times-circle fa-lg close"></i></span>');
                $this.typeahead('val', null);
                self.refresh();
            });

        },

        num: function(val, shorten) {
            if (!shorten) return Math.floor(val).toLocaleString();

            if (val >= 1000000) {
                val = Math.floor(val / 10000) / 100;
                return val + 'M';
            } else if (val >= 1000) {
                val = Math.floor(val / 1000);
                return val + 'K';
            }
        },

        money: function(val, shorten) {
            var self = this;
            return '$' + self.num(val, shorten);
        }

    };

    $.fn.talkMap = function(options) {
        return this.each(function() {
            var i = Object.create(TalkMap);
            i.init(options, this);
            $.data(this, 'TalkMap', i);
        });
    };

    $.fn.talkMap.options = {
        dataURL: map_data_url,
        imagePlaceholders: false,
        imagePlaceholder: theme_url + '/assets/images/gallery-placeholder-square.jpg',
        lazyLoadImages: true,
        initView: 'projects',
        floorplanLayout: 'list',
        initialProject: false,
        mapOptions: {
            center: {
                lat: 43.656348,
                lng: -79.373958
            },
            zoom: 12,
            scrollwheel: true,
            draggable: true,
            disableDefaultUI: true,
            zoomControl: false,
            clickableIcons: false,
            zoomControlOptions: {
                position: google.maps.ControlPosition.TOP_LEFT
            },
            styles: [{
                "featureType": "poi",
                "elementType": "all",
                "stylers": [{
                    "visibility": "off"
                }]
            }]
        },
        initialParams: {},
        initialZoom: null,
        myLocationZoom: 14,
        singleProject: false,
        enableProjectSidebar: true,
        projectCard: true,
        geoJsonURL: 'wp-content/themes/talkcondo/library/storage/geojson.json',
        verticalcutoff: 35,
        regions: true,
        regionsOnlySelected: false,
        regionHover: true,
        regionHoverLabel: true,
        suppressSoldOut: true,
        suppressResale: true,
        regionStyle: {
            fillColor: "#FE6500",
            fillOpacity: 0.0,
            strokeColor: "#444",
            strokeOpacity: 0.5,
            strokeWeight: 1.8,
            visible: true
        },
        regionHoverStyle: {
            fillColor: "#B3D1FF",
            fillOpacity: 0.3,
            strokeColor: "#444",
            strokeOpacity: 0.8,
            strokeWeight: 1.2,
            visible: true
        },
        regionActiveStyle: {
            fillColor: "#444",
            fillOpacity: 0.2,
            strokeColor: "#444",
            strokeOpacity: 0.4,
            strokeWeight: 1.2,
            visible: true
        },
        flexSliderOptions: {
            animation: "fade",
            controlNav: false,
            animationLoop: false,
            animationSpeed: 400,
            smoothHeight: true,
            slideshow: false,
            prevText: '',
            nextText: ''
        },
        cluster: true,
        clusterOptions: {
            maxZoom: 16,
            zoomOnClick: true,
            averageCenter: true,
            minimumClusterSize: 3,
            styles: [{
                url: 'none',
                height: 32,
                width: 32,
                anchor: [-2, 0],
                textSize: '14',
                textColor: '#ffffff'
            }, {
                url: 'none',
                height: 48,
                width: 48,
                anchor: [-2, 0],
                textSize: '14',
                textColor: '#ffffff'
            }, {
                url: 'none',
                height: 60,
                width: 60,
                anchor: [-2, 0],
                textSize: '14',
                textColor: '#ffffff'
            }]
        }
    };

})(jQuery, window, document);

CustomMarker.prototype = new google.maps.OverlayView();

function CustomMarker(options) {
    this.pos_ = options.position;
    this.map_ = options.map;
    this.classes_ = options.classes || [];
    this.content_ = options.content || '';
    this.projectid = options.projectid || 0;
    this.class_string_ = '';
    this.div_ = null;
    this.active_ = false;
    this.searchResult_ = false;
    // this.setMap(map);
}

CustomMarker.prototype.onAdd = function() {

    var div = document.createElement('div');
    div.innerHTML = '<span>' + this.content_ + '</span>';
    div.style.position = 'absolute';
    this.div_ = div;

    this.setClasses();

    // Add the element to the "overlayLayer" pane.
    var panes = this.getPanes();
    panes.overlayMouseTarget.appendChild(div);

    var that = this;
    google.maps.event.addDomListener(this.div_, 'click', function(event) {
        google.maps.event.trigger(that, 'click');
    });
    google.maps.event.addDomListener(this.div_, 'mouseover', function(event) {
        google.maps.event.trigger(that, 'mouseover');
    });
    google.maps.event.addDomListener(this.div_, 'mouseout', function(event) {
        google.maps.event.trigger(that, 'mouseout');
    });
};

CustomMarker.prototype.draw = function() {

    var overlayProjection = this.getProjection();
    var sw = overlayProjection.fromLatLngToDivPixel(this.pos_);
    var ne = overlayProjection.fromLatLngToDivPixel(this.pos_);

    // Resize the image's div to fit the indicated dimensions.
    var div = this.div_;
    div.style.left = sw.x + 'px';
    div.style.top = ne.y + 'px';
};

// The onRemove() method will be called automatically from the API if
// we ever set the overlay's map property to 'null'.
CustomMarker.prototype.onRemove = function() {
    this.div_.parentNode.removeChild(this.div_);
    this.div_ = null;
};

CustomMarker.prototype.getPosition = function() {
    return this.pos_;
};

CustomMarker.prototype.setAsSearchResultOn = function() {
    this.searchResult_ = true;
    this.setClasses();
};

CustomMarker.prototype.setAsSearchResultOff = function() {
    this.searchResult_ = false;
    this.setClasses();
};


CustomMarker.prototype.setActive = function() {
    this.active_ = true;
    this.setClasses();
};

CustomMarker.prototype.setInactive = function() {
    this.active_ = false;
    this.setClasses();
};

CustomMarker.prototype.setClasses = function() {

    if (!this.div_) return;

    this.class_string_ = '';

    for (i in this.classes_) {
        this.class_string_ += this.classes_[i] + ' ';
    }

    if (this.active_) this.class_string_ += ' active ';
    if (this.searchResult_) {
        this.class_string_ += ' search-result ';
    }

    this.div_.className = this.class_string_.trim();
};
