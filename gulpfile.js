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
    return gulp.src('style/*.scss')
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
    webpackConf.loaders[0].query.plugins =  ['uglify:after']; // REVIEW why does nothing happen??

    return gulp.src('js/linkstack.js')
        .pipe(gulpWebpack(webpackConf))
        .pipe(gulp.dest('./web/js'));

});

gulp.task('build', ['fonts', 'style', 'pack']);
gulp.task('build:dist', ['fonts', 'style', 'pack:prod' ]);

gulp.task('default', ['build']);

var webpackConf = {
    output: {
        filename: 'linkstack.js'
    },
    loaders: [
        {
            test: /\.js?$/,
            exclude: /(bower_components)/,
            loader: 'babel',
            query: {
                presets: ['es2015']
            }
        }
    ]
};

gulp.task('pack', ['lint'], function() {
    var buildVarsPlugin = new webpack.DefinePlugin({
        BUILD_DEV: true
    });
    webpackConf.plugins = [buildVarsPlugin];
    return gulp.src('js/linkstack.js')
        .pipe(gulpWebpack(webpackConf))
        .pipe(gulp.dest('web/js'));
});


gulp.task('pack:watch', function() {
    gulp.watch('js/linkstack.js', ['pack']);
});
