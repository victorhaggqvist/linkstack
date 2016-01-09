'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var eslint = require('gulp-eslint');
var babel = require('gulp-babel');
var uglify = require('gulp-uglify');

gulp.task('lint', function() {
    return gulp.src(['js/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});

gulp.task('babel', function () {
    return gulp.src('./js/*.js')
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(gulp.dest('./web/js'));
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

gulp.task('compress', ['babel'], function() {
    return gulp.src('./web/js/linkstack.js')
        .pipe(uglify())
        .pipe(gulp.dest('./web/js'));
});

gulp.task('build', ['fonts', 'style', 'babel']);
gulp.task('build:dist', ['fonts', 'style', 'compress' ]);

gulp.task('default', ['build']);
