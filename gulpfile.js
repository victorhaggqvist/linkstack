'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var eslint = require('gulp-eslint');

gulp.task('eslint', function() {
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

gulp.task('build', ['fonts', 'style']);
//
gulp.task('default', function () {
    gulp.start('build');
});
//
//gulp.task('watch', ['serve'], function () {
//
//    // watch for changes
//    gulp.watch(['app/*.html'], reload);
//
//    gulp.watch('app/styles/**/*.scss', ['styles']);
//    gulp.watch('app/scripts/**/*.js', ['scripts']);
//    gulp.watch('app/images/**/*', ['images']);
//    gulp.watch('bower.json', ['wiredep']);
//});
