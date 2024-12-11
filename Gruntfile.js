'use strict';
module.exports = function (grunt) {

    require('load-grunt-tasks')(grunt, {
        scope: 'devDependencies'
    });

    grunt.initConfig({

        watch: {
            sass: {
                files: ['assets/sass/**/*.{scss,sass}'],
                exclude: ['assets/sass/vendor'],
                tasks: ['sass:dist'],
                options: {
                    livereload: true
                }
            },
            js: {
                files: ['assets/js/source/**/*.js', 'assets/js/talkMap.jquery.js'],
                tasks: ['jshint', 'uglify'],
                options: {
                    livereload: true
                }
            }
        },

        sass: {
            dist: {
                files: [{
                    sourceMap: true,
                    src: 'assets/sass/editor-style.scss',
                    dest: 'assets/css/editor-style.css'
                }, {
                    sourceMap: true,
                    src: 'assets/sass/style.scss',
                    dest: 'assets/css/style.css'
                }]
            },
            vendor: {
                files: [{
                    expand: true,
                    sourceMap: true,
                    outputStyle: 'compressed',
                    cwd: 'assets/sass/vendor',
                    src: ['**/*.scss'],
                    dest: 'assets/css/vendor',
                    ext: '.css'
                }]
            }
        },

        autoprefixer: {
            options: {
                browsers: ['last 2 versions', 'ie 9', 'ios 6', 'android 4'],
                map: true
            },
            files: {
                expand: true,
                flatten: true,
                src: 'assets/css/*.css',
                dest: 'assets/css'
            },
        },

        cssmin: {
            options: {
                keepSpecialComments: 1
            },
            minify: {
                expand: true,
                cwd: 'assets/css',
                src: ['**/*.css', '**/!*.min.css'],
                dest: 'assets/css',
                ext: '.min.css'
            }
        },

        jshint: {
            options: {
                "jshintrc": '.jshintrc',
                "force": true
            },
            all: [
                'Gruntfile.js',
                'assets/js/source/**/*.js'
            ]
        },

        uglify: {
            plugins: {
                options: {
                    sourceMap: 'assets/js/plugins.js.map',
                    sourceMappingURL: 'plugins.js.map',
                    sourceMapPrefix: 2
                },
                files: {
                    'assets/js/plugins.min.js': [
                        'assets/js/source/plugins.js',
                        'assets/js/vendor/**/*.js'
                    ]
                }
            },
            main: {
                options: {
                    sourceMap: 'assets/js/scripts.js.map',
                    sourceMappingURL: 'scripts.js.map',
                    sourceMapPrefix: 2
                },
                files: {
                    'assets/js/scripts.min.js': [
                        'assets/js/source/scripts.js'
                    ]
                }
            }
        },

        jsbeautifier: {
            css: {
                src: ['assets/css/**/*.css', 'assets/css/**/!*.min.css']
            },
            js: {
                src: ['assets/js/**/*.js', 'assets/js/**/!*.min.js', '!assets/js/jquery.touchSwipe.js']
            }
        },

        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7,
                    progressive: true,
                    interlaced: true
                },
                files: [{
                    expand: true,
                    cwd: 'assets/img/',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'assets/img/'
                }]
            }
        },

        makepot: {
            target: {
                options: {
                    domainPath: 'library/languages',
                    potFilename: '_s.pot',
                    type: 'wp-theme'
                }
            }
        },

        copy: {
            main: {
                files: [{
                    src: ['node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'],
                    dest: 'assets/js/vendor/bootstrap.js',
                },{
                    expand: true,
                    cwd: 'node_modules/@fortawesome/fontawesome-free/css/',
                    src: ['**'],
                    dest: 'assets/vendor/fontawesome-free/css',
                },{
                    expand: true,
                    cwd: 'node_modules/@fortawesome/fontawesome-free/webfonts/',
                    src: ['**'],
                    dest: 'assets/vendor/fontawesome-free/webfonts/',
                },{
                    expand: true,
                    cwd: 'node_modules/material-design-icons/iconfont/',
                    src: ['**'],
                    dest: 'assets/vendor/material-design-icons/',
                },{
                    expand: true,
                    cwd: 'node_modules/select2/dist/',
                    src: ['**'],
                    dest: 'assets/vendor/select2/',
                },{
                    expand: true,
                    cwd: 'node_modules/nouislider/distribute/',
                    src: ['**'],
                    dest: 'assets/vendor/nouislider/',
                },{
                    expand: true,
                    cwd: 'node_modules/ez-plus/src/',
                    src: ['**'],
                    dest: 'assets/vendor/ez-plus/',
                },{
                    expand: true,
                    cwd: 'node_modules/@fancyapps/fancybox/dist/',
                    src: ['**'],
                    dest: 'assets/vendor/fancybox/',
                },{
                    expand: true,
                    cwd: 'node_modules/flexslider/',
                    src: ['**'],
                    dest: 'assets/vendor/flexslider/',
                },{
                    expand: true,
                    cwd: 'node_modules/jquery-touchswipe/',
                    src: ['**'],
                    dest: 'assets/vendor/jquery-touchswipe',
                }]
            },
        },

        dataUri: {
            dist: {
                src: ['assets/css/*.css'],
                dest: 'assets/css',
                options: {
                    target: ['assets/img/**/*.*'],
                    fixDirLevel: true,
                    maxBytes: 2048
                }
            }
        },

        clean: {
            reset: [
                'assets/css',
                'assets/js/plugins.min.js',
                'assets/js/plugins.min.js.map',
                'assets/js/scripts.min.js',
                'assets/js/scripts.min.js.map',
                '.sass-cache',
                'node_modules'
            ],
            prebuild: [
                'assets/css'
            ],
            build: [
                '.sass-cache',
                'node_modules',
            ]
        }

    });

    /**
     * Register the 'reset' task used to reset the theme to default.
     */
    grunt.registerTask(
        'reset', [
            'clean:reset'
        ]
    );

    /**
     * Register the 'default' task used for development.
     */
    grunt.registerTask(
        'default', [
            'sass:vendor', 'sass:dist', 'uglify', 'jsbeautifier', 'dataUri', 'watch'
        ]
    );

    /**
     * Register the 'setup' task used for initialization.
     */
    grunt.registerTask(
        'setup', [
            'copy', 'sass:vendor', 'sass:dist', 'uglify', 'jsbeautifier', 'watch'
        ]
    );

    /**
     * Register the 'prebuild' task used to test the distributable version of the theme.
     */
    grunt.registerTask(
        'prebuild', [
            'clean:prebuild', 'copy', 'sass:vendor', 'sass:dist', 'autoprefixer', 'jsbeautifier', 'cssmin', 'uglify', 'imagemin', 'makepot', 'dataUri'
        ]
    );

    /**
     * Register the 'build' task used to build the distributable theme.
     */
    grunt.registerTask( 'build', function() {
        grunt.task.run( 'prebuild' );
        // grunt.task.run( 'clean:build' );
    });

};
