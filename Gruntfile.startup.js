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
                    "static/css/style.dev.css": "static/less/style.less"
                }
            }
            
        },


    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['watch']);

    /* 
    Live reload
    <script src="//localhost:35729/livereload.js"></script>
    */

};
