module.exports = function(grunt) {

    grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),
      sass: {
        dev: {
          options: {
            style: 'compressed'
          },
          files: {
            './app/css/stack.css': './sass/stack.scss'
          }
        },
        prod: {
          options: {
            style: 'compressed',
            sourcemap : 'none'
          },
          files: {
            './build/stack.min.css': './sass/stack.scss'
          }
        },
        bootstrap: {
          options: {
            style: 'compressed',
            sourcemap : 'none',
            loadPath: './bower_components/bootstrap-sass-twbs/assets/stylesheets/bootstrap'
          },
          files: {
            './build/bootstrap.min.css': './sass/bootstrap.scss'
          }
        }
      },
      watch: {
          files: ["./sass/*","./js/app.js"],
          tasks: ['jshint', "copy", 'sass:dev'],
          options: {
            livereload: true
          }
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
            './app/js/jquery.min.js': './bower_components/jquery/dist/jquery.js',
            './app/js/bootstrap.min.js': './bower_components/bootstrap-sass-twbs/assets/javascripts/bootstrap.js',
            './build/app.js': './js/app.js'
          }
        }
      },
      copy: {
        prod: {
          files:[
            {
              src: './build/app.js',
              dest: './app/js/stack.js'
            },
            {
              src: './build/dest.svg',
              dest: './app/img/dest.svg'
            },
            {
              src: './build/stack.css',
              dest: './app/css/stack.css'
            }
          ]
        },
        dev: {
          src: "./js/app.js",
          dest: "./app/js/stack.js"
        }
      },
      jshint: {
        all: ['Gruntfile.js', './js/app.js']
      },
      notify_hooks: {
        options: {
          enabled: true,
          max_jshint_notifications: 5,
          title: "Linkstack"
        }
      },
      svgstore: {
        options: {
          prefix: 'icon-',
          svg: {
            style: 'display:none;'
          }
        },
        default : {
          files: {
            'build/dest.svg': ['images/*.svg']
          }
        }
      },
      concat: {
        options: {
          stripBanners: true,
          banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
          '<%= grunt.template.today("yyyy-mm-dd") %> */'
        },
        dist: {
          src: ['./build/*.min.css'],
          dest: './build/stack.css'
        }
      },
      clean: {
        build: {
          src: ['./build', './app/css', './app/js']
        }
      }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-svgstore');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-clean');

    grunt.registerTask('default','watch');
    grunt.registerTask('build',['clean:build', 'sass:prod', 'sass:bootstrap', 'uglify:prod', 'svgstore', 'concat', 'copy:prod']);
};
