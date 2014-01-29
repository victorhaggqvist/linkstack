module.exports = function(grunt) {

    grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),
      sass: {
        dev: {
          options: {
            style: 'compressed',
            sourcemap : true
          },
          files: {
            './assets/css/stack.css': './assets/sass/*.scss'
          }
        },
        prod: {
          options: {
            style: 'compressed',
            sourcemap : false
          },
          files: {
            './assets/css/stack.min.css': './assets/sass/*.scss'
          }
        }
      },
      watch: {
          files: ["./assets/sass/*","./assets/js/stack.js"],
          tasks: ["sass","uglify"]
      },
      uglify: {
        prod: {
           options: {
            report: 'min',
            mangle: {
              except: ['jQuery']
            }
          },
          files: {
            './assets/js/stack.min.js': ['./assets/js/stack.js']
          }
        }
      }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default','watch');
};
