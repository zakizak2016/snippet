module.exports = function(grunt) {

    grunt.initConfig({
        concat: {
            options: {
                separator: ';',
            },
            dist: {
                src: ['node_modules/bootstrap/dist/js/bootstrap.min.js', 'src/outro.js'],
                dest: 'built.js',
            },
        },
        watch: {
            scripts: {
                // files: ['**/*.js'],
                files: [
                '*.less',
                '*.php',
                'partials/*.php',
                'assets/bootstrap-custom/less/*.less',
                'assets/less/*.less'
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
                    "assets/css/style.css": "assets/less/style.less"
                }
            }
            
        },


    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['watch']);

};
