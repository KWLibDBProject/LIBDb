module.exports = function(grunt) {

    // 1. Вся настройка находится здесь
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            js: {
                src: [
                    'www/template.etks/_assets/jquery/jquery-1.11.0.js',
                    'www/template.etks/_assets/bootstrap/bootstrap.js',
                    'www/template.etks/_assets/jquery/jquery.colorbox.min.js',
                    'www/template.etks/_assets/jquery/jquery.cookie.js',
                    'www/frontend.js',
                    'www/frontend.options.js ',
                ],
                dest: 'www/scripts.min.js',
            }
        },
        uglify: {
            js: {
                src: 'www/scripts.min.js',
                dest: 'www/scripts.min.js'
            }
        },

        cssmin: {
            target: {
                files: {
                    'www/styles.min.css': [
                        'www/template.etks/_assets/bootstrap/bootstrap.css',
                        'www/template.etks/_assets/bootstrap/bootstrap-theme.css',
                        'www/template.etks/_assets/colorbox.css',
                        'www/template.etks/theme.css'
                    ]
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // 4. Указываем, какие задачи выполняются, когда мы вводим «grunt» в терминале
    grunt.registerTask('default', ['concat', 'uglify', 'cssmin']);
};