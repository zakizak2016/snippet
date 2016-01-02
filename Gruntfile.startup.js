module.exports = function(grunt) {

    grunt.initConfig({
        concat: {
            options: {
                separator: ';',
            },
            js: {
                src: [
                // "common-files/js/jquery-1.10.2.min.js",
                // "flat-ui/js/bootstrap.min.js",
                "common-files/js/jquery.scrollTo-1.4.3.1-min.js",
                "common-files/js/modernizr.custom.js",
                "common-files/js/page-transitions.js",
                "common-files/js/easing.min.js",
                "common-files/js/jquery.svg.js",
                "common-files/js/jquery.svganim.js",
                "common-files/js/jquery.parallax.min.js",
                "common-files/js/startup-kit.js"
                ],
                dest: 'built.js',
            },
            css: {
                src: [
                // "flat-ui/bootstrap/css/bootstrap.css",
                "flat-ui/css/flat-ui.css",
                "common-files/css/icon-font.css",
                "common-files/css/animations.css",
                "ui-kit/ui-kit-header/css/style.css",
                "ui-kit/ui-kit-content/css/style.css",
                "ui-kit/ui-kit-blog/css/style.css",
                "ui-kit/ui-kit-contacts/css/style.css",
                "ui-kit/ui-kit-crew/css/style.css",
                "ui-kit/ui-kit-price/css/style.css",
                "ui-kit/ui-kit-projects/css/style.css",
                "ui-kit/ui-kit-footer/css/style.css"
                ],
                dest: 'built.css',
            },
        },
        uglify: {
            my_target: {
                files: {
                    'built.min.js': ['built.js']
                }
            }
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'built.min.css': ['built.css']
                }
            },
            target2: {
                files: {
                    'tidy.min.css': ['tidy.css']
                }
            }
        },
        watch: {
            scripts: {
                files: [
                'startup/**/**/**/*.less'
                ],
                tasks: ['less:development'],
                options: {
                    spawn: false,
                    livereload: true
                },
            },
        },
        less: {
            development: {
                options: {
                    // paths: ["assets/css"]
                },
                files: {
                    "style.dev.css": "samples/template/less/style.less"
                    // "static/css/style.dev.css": "static/less/style.less"
                }
            }
            
        },
        uncss: {
            dist: {
                 options: {
      ignore: ['#added_at_runtime',  "/^#js/"]
    },
                files: {
                    'tidy.css': ['index2.html']
                }
            }
        }
    });

grunt.loadNpmTasks('grunt-contrib-less');
grunt.loadNpmTasks('grunt-contrib-watch');

grunt.loadNpmTasks('grunt-contrib-concat');
grunt.loadNpmTasks('grunt-contrib-cssmin');
grunt.loadNpmTasks('grunt-contrib-uglify');
grunt.loadNpmTasks('grunt-uncss');

grunt.registerTask('default', ['watch']);

    /* 
    Live reload
    <script src="//localhost:35729/livereload.js"></script>

    goofoffline "http://fonts.googleapis.com/css?family=Montserrat"
    goofoffline "http://fonts.googleapis.com/css?family=Raleway"
    
    */

};
