/**
 * Scripts
 *
 * Run your custom JavaScript code in this file.
 */

(function() {

    jQuery(document).ready(function($) {
        // Defaults
        var engine = {
            header: {
                element: $('.header'),
                fixed: {
                    enabled: true,
                    front: true
                },
                fold: {
                    enabled: true,
                    front: true
                },
                dropdowns: {
                    enabled: true,
                    front: true,
                    ghost: false
                }
            },
            footer: {
                element: $('.footer'),
                sticky: {
                    enabled: true
                }
            },
            modules: {
                carousels: true,
                animations: true,
                jumpers: {
                    enabled: true,
                    offset: 0
                },
                svg: {
                    enabled: true,
                    inline: true
                }
            }
        };

        // Initialize
        $.fn.ascripta(engine);

        /**
         * Home: Projects
         */
        (function() {
            $gscrolls = $('.gscroll .condos-slider__scroller');
            if ($gscrolls.length < 1) {
                return;
            }
            $gscrolls.each(function() {
                $(this).gScrollingCarousel({
                    scrolling: false,
                    // amount: 50,
                });
            });
        }());

        $('.page-template-template-builder .site-content iframe').each(function() {
            $(this).addClass('embed-responsive-item').wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
        });

        $('.nav-tabs > li').on('click', function() {
            if ($(this).hasClass('active')) {
                return false;
            }
        });

        if ($('.availability-dropdown').length) {
            var dropdown = $('.availability-dropdown');
            var toggle = dropdown.find('.dropdown-toggle');
            dropdown.find('.dropdown-menu > li > a').on('click', function(e) {
                e.preventDefault();

                var filter = $(this).data('filter');

                $(toggle).data('filter', filter);
                $(toggle).trigger('switch_filter');

                dropdown.find('.dropdown-menu > li').removeClass('active');
                $(this).parent('li').addClass('active');

                $('.availability-sort').toggle(filter === 'all' || filter === 'saved');

                // TODO: Bind this to when there's no floorplans instead of saved.
                if (filter === 'saved') {
                    $('.floorplans-empty').removeClass('hidden');
                }

                toggle.html($(this).html());
            });

            /**
             * Switch from empty to all.
             */
            $('.floorplans-empty__trigger').on('click', function(e) {

                e.preventDefault();

                $(toggle).data('filter', 'all');
                $(toggle).trigger('switch_filter');

                dropdown.find('.dropdown-menu > li').removeClass('active');
                dropdown.find('a[data-filter="all"]').parent('li').addClass('active');

                $('.availability-sort').toggle('all');

                toggle.html($(this).html());

                $('.floorplans-empty').addClass('hidden');

            });
        }

        if ($('.project-content .card.floorplans').length) {
            $(window).on('load resize', function() {
                var width = $(this).width();
                if (width < 768) {
                    $('.project-content .card.floorplans').find('a[data-layout="grid"]').click();
                } else {
                    $('.project-content .card.floorplans').find('a[data-layout="list"]').click();
                }
            });
        }

        /**
         * Project Gallery
         */
        (function() {
            if ($('.project-gallery').length) {

                $('.project-gallery').slick({
                    infinite: true,
                    arrows: true,
                    dots: false,
                    variableWidth: true,
                    centerPadding: '60px',
                });

                $('.project-gallery')[0].slick.setPosition();

                $('.project-icon a').bind('click', function(event) {
                    $('.project-gallery a').magnificPopup('open');

                    event.preventDefault();
                });
            }
        }());

        /**
         * Template: Single Project
         */
        if ($(window).width() < 480) {
            if ($('.project-overview__content').length) {
                $('.project-overview__content').swipe({
                    swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
                        var navigation = $('.nav-tabs');
                        var active = navigation.find('.active');
                        if (direction === 'left') {
                            var next = $(active).next();
                            $(next).find('a').click();

                        } else if (direction === 'right') {
                            var prev = $(active).prev();
                            $(prev).find('a').click();
                        }

                    },
                    threshold: 0
                });
            }
        }

        (function() {
            var header = $('.project-header');
            var button = $('.leadpages__button--top');

            if (header.length) {
                $(window).on('scroll', function() {
                    if ($(window).scrollTop() >= header.offset().top + header.height()) {
                        if (!button.hasClass('in')) {
                            button.addClass('in');
                        }
                    } else {
                        if (button.hasClass('in')) {
                            button.removeClass('in');
                        }
                    }
                });
            }

            $('.floorplans__layout-toggle a:not(.external)').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var layout = $this.data('layout');
                var $grid = $('.floorplans.grid');
                var $list = $('.floorplans.list');
                var $gridsort = $('.gridsort');

                $('.floorplans__layout.active').removeClass('active');
                $this.addClass('active');

                if (layout === 'grid') {

                    if ($grid.is(':visible')) {
                        return;
                    }

                    $list.fadeOut('fast');
                    $grid.fadeIn('fast');
                    $gridsort.fadeIn('fast');

                } else if (layout === 'list') {

                    if ($list.is(':visible')) {
                        return;
                    }

                    $grid.fadeOut('fast');
                    $gridsort.fadeOut('fast');
                    $list.fadeIn('fast');
                }

                /*$('img.lazy').trigger('appear');*/
            });

            $("a#activate-floorplans-tab").on('click', function(e) {
                e.preventDefault();
                // console.log("switch to #floorplans tab");
                $(".project-submenu [href='#floorplans']").tab('show');
            });


        })();

        /**
         * Floor Plans
         */
        (function() {

            var $this;

            var $availableFirst = $('#available-first');

            var sortavailablefirst = function() {
                if ($('.floorplan-count.all').parents('li.active').length) {
                    return $('#available-first').is(':checked');
                }

                return false;
            };

            var sortgrid = function() {
                var $grid = $('.floorplans.grid');

                var $items = $grid.find('.floorplan'),
                    order = $grid.data('order'),
                    sort = $grid.data('sort');


                var availfirst = sortavailablefirst();
                $items.sort(function(a, b) {
                    var $a = $(a),
                        $b = $(b);

                    if (availfirst && $a.is('.available') !== $b.is('.available')) {
                        return $b.is('.available') - $a.is('.available');
                    }

                    if (sort) {
                        if (order === 'desc') {
                            return parseFloat($b.data(sort)) - parseFloat($a.data(sort));
                        } else {
                            return parseFloat($a.data(sort)) - parseFloat($b.data(sort));
                        }
                    } else {
                        return $a.data('suite-name').localeCompare($b.data('suite-name'));
                    }
                });

                // if ($grid.data('sortavailable') !== false) {
                //     $items.sort(function(a, b) {
                //         if ($(a).hasClass('available') && !$(b).hasClass('available')) {
                //             return -1;
                //         } else if ($(b).hasClass('available') && !$(a).hasClass('available')) {
                //             return 1;
                //         }
                //     });
                // }

                $.each($items, function(index, row) {
                    var $row = $(row);
                    $grid.append($row);
                });
            };

            if ($('table.floorplans.list').length) {
                var $table = $('table.floorplans.list');
                $table.tablesorter({
                    headers: {
                        0: {
                            sorter: true
                        },
                        1: {
                            sorter: false
                        }
                    },
                    sortList: [
                        [0, 0]
                    ]
                });

                $availableFirst.click(function(e) {
                    if ($table.is(':visible')) {
                        var ts = $table.data('tablesorter');

                        if ($(this).is(':checked')) {
                            ts.sortForce = [
                                [0, 0]
                            ];
                        } else {
                            ts.sortForce = null;
                        }

                        var sortList = ts.last.sortList;
                        var sorter = sortList[sortList.length - 1];
                        ts.sortList = ts.last.sortList = [sorter];

                        $table.data('tablesorter', ts);

                        $table.trigger('sorton', [sorter]);
                    } else {
                        sortgrid();
                    }

                    return true;
                });
            }

            $('.gridsortbutton').on('click', function(e) {
                e.preventDefault();
                $(this).siblings('.submenu').slideToggle('fast');
            });

            $('.gridsortmenu .submenu div').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $grid = $('.floorplans.grid');

                $this.closest('.gridsortmenu').find('.gridsortbutton span').text($this.data('sort'));
                $this.closest('.submenu').slideToggle('fast');
                $grid.data('sort', $this.data('sort'));

                return sortgrid();
            });

            $('.floorplans.list').on('click', '.clickable', function(e) {
                if (!$(e.target).is('.passthrough')) {
                    e.preventDefault();
                    $this = $(this).closest('tr').find('.quick-view').trigger('click');
                }
            });

            $('.gridsortdirection').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $grid = $('.floorplans.grid');

                if ($this.data('direction') === 'asc') {
                    $this.data('direction', 'desc');
                    $grid.data('order', 'desc');
                    $this.find('.fa').removeClass('fa-sort-amount-up').addClass('fa-sort-amount-down');
                } else {
                    $this.data('direction', 'asc');
                    $grid.data('order', 'asc');
                    $this.find('.fa').removeClass('fa-sort-amount-down').addClass('fa-sort-amount-up');
                }

                return sortgrid();
            });

            $('.gridsortavailability').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $grid = $('.floorplans.grid');

                if ($this.data('availability') === true) {
                    $grid.data('sortavailable', false);
                    $this.data('availability', false);
                    $this.find('.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
                } else {
                    $this.data('availability', true);
                    $grid.data('sortavailable', true);
                    $this.find('.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
                }

                return sortgrid();

            });

            // Availability Dropdown
            var $statusDropdown = $('.availability-dropdown .dropdown-toggle');

            // Filter sliders

            var min;
            var max;

            var $nopricetoggle = $('#show-no-price-floorplans');

            var priceslider = document.getElementById('priceslider');
            if (priceslider) {
                min = (priceslider.dataset.min) ? parseFloat(priceslider.dataset.min) : 0;
                max = (priceslider.dataset.max) ? parseFloat(priceslider.dataset.max) : 1000000;
                if (min === max) {
                    min = 0;
                    max = 1000000;
                }
                noUiSlider.create(priceslider, {
                    start: [min, max],
                    step: 50000,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                priceslider.noUiSlider.on('update', function(values, handle) {
                    var lowerLabel = values[0] / 1000;
                    if (values[0] <= 999999) {
                        lowerLabel += 'K';
                    }
                    if (values[0] > 999999) {
                        lowerLabel = values[0] / 1000 / 1000 + 'M';
                    }
                    $('#priceslider').find('.noUi-handle-lower').html(lowerLabel);

                    var upperLabel = values[1] / 1000;
                    if (values[1] <= 999999) {
                        upperLabel += 'K';
                    }
                    if (values[1] > 999999) {
                        upperLabel = values[1] / 1000 / 1000 + 'M';
                    }
                    $('#priceslider').find('.noUi-handle-upper').html(upperLabel);
                });
            }

            var bedslider = document.getElementById('bedslider');
            if (bedslider) {
                min = (bedslider.dataset.min) ? parseFloat(bedslider.dataset.min) : 0;
                max = (bedslider.dataset.max) ? parseFloat(bedslider.dataset.max) : 3;
                if (min === max) {
                    min = 0;
                    max = 3;
                }
                noUiSlider.create(bedslider, {
                    start: [min, max],
                    step: 0.5,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                bedslider.noUiSlider.on('update', function(values, handle) {
                    $('#bedslider').find('.noUi-handle-lower').html(Math.round(values[0] * 10) / 10);
                    $('#bedslider').find('.noUi-handle-upper').html((Math.round(values[1] * 10) / 10 === max) ? max + "+" : Math.round(values[1] * 10) / 10);
                });
            }

            var bathslider = document.getElementById('bathslider');
            if (bathslider) {
                min = (bathslider.dataset.min) ? parseFloat(bathslider.dataset.min) : 0;
                max = (bathslider.dataset.max) ? parseFloat(bathslider.dataset.max) : 3;
                if (min === max) {
                    min = 0;
                    max = 3;
                }
                noUiSlider.create(bathslider, {
                    start: [min, max],
                    step: 0.5,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                bathslider.noUiSlider.on('update', function(values, handle) {
                    $('#bathslider').find('.noUi-handle-lower').html(Math.round(values[0] * 10) / 10);
                    $('#bathslider').find('.noUi-handle-upper').html((Math.round(values[1] * 10) / 10 === max) ? max + "+" : Math.round(values[1] * 10) / 10);
                });
            }

            var sizeslider = document.getElementById('sizeslider');
            if (sizeslider) {
                min = (sizeslider.dataset.min) ? parseInt(sizeslider.dataset.min) : 0;
                max = (sizeslider.dataset.max) ? parseInt(sizeslider.dataset.max) : 2000;
                noUiSlider.create(sizeslider, {
                    start: [min, max],
                    step: 100,
                    connect: [false, true, false],
                    range: {
                        'min': [min],
                        'max': [max]
                    }
                });
                sizeslider.noUiSlider.on('update', function(values, handle) {
                    $('#sizeslider').find('.noUi-handle-lower').html(Math.round(values[0]));
                    $('#sizeslider').find('.noUi-handle-upper').html((Math.round(values[1]) === max) ? max + "+" : Math.round(values[1]));
                });
            }

            var floorplanInFilter = function(value, slider) {
                value = parseFloat(value);
                slider = slider.noUiSlider;

                var bounds = slider.get().map(parseFloat),
                    min = bounds[0],
                    max = bounds[1];

                return !(value < min || (max < slider.options.range.max[0] && value > max));

            };

            var matchesPrice = function(floorplan) {
                var price = $(floorplan).data('price');

                return price ? floorplanInFilter(price, priceslider) : !$nopricetoggle.prop('checked');
            };

            var matchesAvailability = function(floorplan) {
                var matches = true;

                var filter = $statusDropdown.data('filter');

                if (filter !== 'all') {
                    if (filter === 'saved') {
                        matches = false;
                    } else {
                        matches = floorplan.data('availability') === filter;
                    }
                }

                return matches;
            };

            var filterfloorplans = function() {
                var $floorplans = $(".floorplans .floorplan");

                var totals = {
                    all: 0,
                    available: 0,
                    sold: 0
                };

                if (!priceslider && !bedslider && !bathslider && !sizeslider) {
                    return true;
                }

                $floorplans.each(function() {
                    var $this = $(this);

                    var matchesFilters = matchesPrice($this) &&
                        floorplanInFilter($this.data('beds'), bedslider) &&
                        floorplanInFilter($this.data('baths'), bathslider) &&
                        floorplanInFilter($this.data('size'), sizeslider);

                    if (matchesFilters) {
                        totals.all++;
                        $(this).is('.available') ? totals.available++ : totals.sold++;
                    }

                    $this.toggle(matchesFilters && matchesAvailability($this));
                });

                $.each(totals, function(status, total) {
                    // console.log({total, status, total})
                    $('.floorplan-count.' + status).html('(' + (total / (2 * $(".card.floorplans").length)) + ')');
                });
            };

            if (priceslider && priceslider.noUiSlider) {
                priceslider.noUiSlider.on('update', filterfloorplans);
            }

            if (bedslider && bedslider.noUiSlider) {
                bedslider.noUiSlider.on('update', filterfloorplans);
            }

            if (bathslider && bathslider.noUiSlider) {
                bathslider.noUiSlider.on('update', filterfloorplans);
            }

            if (sizeslider && sizeslider.noUiSlider) {
                sizeslider.noUiSlider.on('update', filterfloorplans);
            }

            if ($nopricetoggle) {
                $nopricetoggle.change(filterfloorplans);
            }

            if ($statusDropdown) {
                $statusDropdown.on('switch_filter', filterfloorplans);
            }

            $('.floorplans_more-filters-toggle').on('click', function() {
                var $this = $(this);
                var $div = $('.floorplans_more-filters');
                $div.toggle();
                if ($this.find('i.fa-caret-down').length) {
                    $this.find('i.fa-caret-down').removeClass('fa-caret-down').addClass('fa-caret-up');
                } else {
                    $this.find('i.fa-caret-up').removeClass('fa-caret-up').addClass('fa-caret-down');
                }
            });

            var resetFloorplanFilters = function() {
                if (priceslider) {
                    priceslider.noUiSlider.reset();
                }
                if (bedslider) {
                    bedslider.noUiSlider.reset();
                }
                if (bathslider) {
                    bathslider.noUiSlider.reset();
                }
                if (sizeslider) {
                    sizeslider.noUiSlider.reset();
                }
                if ($nopricetoggle) {
                    $nopricetoggle.prop('checked', false);
                }
            };

            $('.floorplans_reset-filters').on('click', resetFloorplanFilters);

            var $fpc = $('#floorplans-carousel');
            if ($fpc.length > 0) {
                $fpc.flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: false,
                    slideshow: false,
                    itemWidth: 80,
                    itemMargin: 5,
                    asNavFor: "#floorplans-slider"
                });
            }

            var $fps = $('#floorplans-slider');
            if ($fps.length > 0) {
                $fps.flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: false,
                    slideshow: false,
                    sync: "#floorplans-carousel"
                });
            }

            var $browsemore = $('.browse-more-floorplans .flexslider');
            if ($browsemore.length) {
                $browsemore.flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: false,
                    slideshow: false,
                    itemWidth: 220,
                    itemMargin: 5
                });
            }

        }());

        /**
         * Recently Updated
         */

        (function() {
            if ($('#recently-updated-projects').length) {
                var $this;
                $('#recently-updated-projects').on('click', '.loadmore', function(e) {
                    e.preventDefault();
                    $this = $(this);

                    if ($this.hasClass('disabled')) {
                        return;
                    }

                    var $container = $this.siblings('.projects');

                    $this.addClass('loading')
                        .html('<i class="fa fa-large fa-spinner fa-spin"></i> Loading...');

                    $.ajax({
                        url: $this.attr('href'),
                        data: {
                            action: $this.data('action'),
                            count: $this.data('count'),
                            paged: $this.data('paged'),
                            taxonomy: $this.data('taxonomy'),
                            term: $this.data('term')
                        },
                        type: 'get',
                        dataType: 'html'
                    }).done(function(responseText) {

                        if (responseText) {
                            $container.append(responseText);
                            $this.data('paged', parseInt($this.data('paged')) + 1);
                            $this.removeClass('loading').html('Load More...');
                        } else {
                            console.log('no updates found');
                            $this.removeClass('loading').html('No More Results').addClass('disabled');

                            if ($container.find('.project').length === 0) {
                                $this.closest('#recently-updated-projects').hide();
                            }

                        }

                    });

                });

                $('#recently-updated-projects').find('.loadmore').trigger('click');
            }
        }());

        /**
         * Lazy Loading
         */
        if (typeof lazyload === 'function') {
            lazyload(null, {
                src: 'data-original',
                selector: 'img.lazy'
            });
        }

        // Map Template
        if ($('.map-sidebar').length) {
            $(window).on('load resize', function() {
                var width = $(this).width();
                if (width > 768) {
                    $('.map-sidebar .project-sidebar__layout').find('label.btn:first-child').trigger('click');
                } else {
                    $('.map-sidebar .project-sidebar__layout').find('label.btn:last-child').trigger('click');
                }
            });
        }

        // Search Jump
        if ($('.jumbo-content').length) {
            $(window).on('load resize', function() {
                if ($(window).width() <= 768) {
                    $('.jumbo .slick-track').css(
                        'height', $('.jumbo-content').outerHeight()
                    );
                    $('.jumbo-content #s').click(function() {
                        var target = $(this);
                        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                        if (target.length) {
                            $.fn.jump(target, $('#wpadminbar').height() + $('#header_main').height());
                            return false;
                        }
                    });
                }
            });
        }

        /**
         * Panels
         */
        if ($('.project-panels').length) {
            $('.project-panels .panel').on('show.bs.collapse', function() {
                $(this).find('.panel-toggle').html('<i class="fa fa-caret-up"></i>Hide');
                $(window).trigger('scroll');
            });
            $('.project-panels .panel').on('hide.bs.collapse', function() {
                $(this).find('.panel-toggle').html('<i class="fa fa-caret-down"></i>Show');
                $(window).trigger('scroll');
            });
        }


        if ($('.popup-video').length) {
            $('.popup-video').each(function() {
                $(this).magnificPopup({
                    disableOn: 700,
                    type: 'iframe',
                    mainClass: 'mfp-fade',
                    removalDelay: 160,
                    preloader: false,

                    fixedContentPos: false,
                    gallery: {
                        enabled: false
                    },
                });
            });
        }

        if ($('#condos-slider--picks').length) {

            $(window).bind('load resize', function() {

                var maxHeight = 0;

                $('#condos-slider--picks .project').each(function() {
                    var compHeight = 60 + $(this).find('figcaption').height() + $(this).find('a').height();
                    if (compHeight > maxHeight) {
                        maxHeight = compHeight;
                    }
                });

                $('#condos-slider--picks').height(maxHeight);

            });

        }

        $('body.home #searchboxes .dropdown-menu').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $('body.home #searchboxes .dropdown-menu.list').on('click', 'li', function(e) {
            var $this = $(this);
            var $searchbox = $this.closest('.searchbox');
            var $form = $searchbox.find('form');
            var value = $this.attr('data-value');

            $this.closest('.dropdown').find('.dropdown-toggle').html($this.html());
            $this.closest('.dropdown').removeClass('open');

            if (value === 'nearme') {
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        $form.attr('action', home_url + '/map');
                        $form.find('input[name=city]').val('');
                        $form.find('input[name=lat]').val(position.coords.latitude);
                        $form.find('input[name=lng]').val(position.coords.longitude);
                    });
                }
            } else {
                $form.attr('action', home_url);
                $form.find('input[name=city]').val(value);
                $form.find('input[name=lat]').val('');
                $form.find('input[name=lng]').val('');
            }

            e.preventDefault();
            e.stopPropagation();
        });

        $('body.home .filter-slider').each(function() {

            var $this = $(this);

            var slider = $this.find('.slider').get(0);
            var min = parseFloat(this.dataset.min || 0);
            var max = parseFloat(this.dataset.max || 100);
            var step = parseFloat(this.dataset.step || 1);
            var start = parseFloat(this.dataset.start || 50);
            var bounds = $(slider).closest('.filter-slider').attr('data-bounds');

            noUiSlider.create(slider, {
                start: start,
                step: step,
                connect: [bounds === 'upper', bounds === 'lower'],
                range: {
                    'min': [min],
                    'max': [max]
                }
            });

            slider.noUiSlider.on('update', function(values, handle) {

                var $slider = $(this.target);
                var $container = $slider.closest('.filter-slider');
                var $dropdown = $slider.closest('.dropdown');
                var $searchbox = $slider.closest('.searchbox');
                var outputfmt = $container.attr('data-outputfmt');
                var $input = $searchbox.find('input[name=' + $container.attr('data-field') + ']');

                var value = Math.round(values[0]);

                var label = '';

                var formatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 0,
                });

                var suffix = '';

                if (outputfmt === '$') {
                    label = formatter.format(value);
                } else if (outputfmt === '%down') {
                    label = Math.round(value) + '%';
                    suffix = ' Down';
                } else if (outputfmt === 'sqft') {
                    label = Math.round(value);
                    suffix = ' sq.ft.';
                } else if (outputfmt === 'ppsqft') {
                    label = formatter.format(value);
                    suffix = ' per sq.ft.';
                }

                $container.find('.upper-label').html(label);
                $dropdown.find('.dropdown-toggle').html(label + suffix);
                $input.val(value);
            });
        });

        $(window).trigger('scroll');
        $(window).trigger('resize');
    });

}());
