'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var eslint = require('gulp-eslint');
var babel = require('gulp-babel');
var uglify = require('gulp-uglify');
var webpack = require('webpack');
var gulpWebpack = require('webpack-stream');

gulp.task('lint', function() {
    return gulp.src(['js/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});

gulp.task('style', function () {
    return gulp.src('style/linkstack.scss')
        .pipe(sass({
            outputStyle: 'compressed',
            includePaths: [
                './bower_components/bootstrap-sass/assets/stylesheets/bootstrap/'
            ]
        }))
        .pipe(gulp.dest('./web/css'));
});

gulp.task('fonts', function () {
    return gulp.src(['./bower_components/bootstrap-sass/assets/fonts/bootstrap/*']).pipe(gulp.dest('./web/fonts'));
});

gulp.task('pack:prod', ['lint'], function() {
    var buildVarsPlugin = new webpack.DefinePlugin({
        BUILD_DEV: false
    });

    webpackConf.plugins = [buildVarsPlugin];
    // TODO make minifying plugin work

    return gulp.src('./js/dashboard.js')
        .pipe(gulpWebpack(webpackConf))
        .pipe(gulp.dest('./web/js'));

});

var webpackConf = {
    output: {
        filename: 'dashboard.min.js'
    },
    module: {
        loaders: [
            {
                test: /\.jsx?$/,
                loader: 'babel',
                query: {
                    presets: ['react', 'es2015']
                }
            }
        ]
    }
};

gulp.task('pack', function() {
    var buildVarsPlugin = new webpack.DefinePlugin({
        BUILD_DEV: true
    });
    webpackConf.plugins = [buildVarsPlugin];
    webpackConf.watch = true;
    webpackConf.devtool = 'source-map';

    return gulp.src('./js/dashboard.js')
        .pipe(gulpWebpack(webpackConf))
        .pipe(gulp.dest('./web/js'));
});


gulp.task('build', ['fonts', 'style', 'pack']);
gulp.task('build:dist', ['fonts', 'style', 'pack:prod' ]);

gulp.task('default', ['build']);
