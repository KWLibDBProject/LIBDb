module.exports = function(grunt) {

    // 1. Вся настройка находится здесь
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            js: {
                src: [
                    'www/template/_assets/jquery/jquery-1.11.0.js',
                    'www/template/_assets/bootstrap/bootstrap.js',
                    'www/template/_assets/jquery/jquery.colorbox.min.js',
                    'www/template/_assets/jquery/jquery.cookie.js',
                    'www/frontend.js',
                    'www/frontend.options.js ',
                ],
                dest: 'www/scripts.etks.min.js',
            }
        },
        uglify: {
            js: {
                src: 'www/scripts.etks.min.js',
                dest: 'www/scripts.etks.min.js'
            }
        },

        cssmin: {
            target: {
                files: {
                    'www/styles.etks.min.css': [
                        'www/template/_assets/bootstrap/bootstrap.css',
                        'www/template/_assets/bootstrap/bootstrap-theme.css',
                        'www/template/_assets/colorbox.css',
                        'www/template.etks/theme.css'
                    ]
                }
            }
        },

        imageEmbed: {
            dist: {
                src: [ "www/styles.etks.min.css" ],
                dest: "www/styles.etks.min.css",
                options: {
                    deleteAfterEncoding : false,
                    preEncodeCallback: function (filename) { return true; },
                    baseDir: 'www',
                    
                }
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    // grunt.loadNpmTasks("grunt-image-embed");
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    

    // 4. Указываем, какие задачи выполняются, когда мы вводим «grunt» в терминале
    grunt.registerTask('default', ['concat', 'uglify', 'cssmin']);
};