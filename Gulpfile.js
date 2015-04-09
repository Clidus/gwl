'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    watch = require('gulp-watch');

gulp.task('scss', function () {
    return gulp.src('./style/scss/*.scss')
        .pipe(sass({
            errLogToConsole: true,
            outputStyle: 'compressed'
        }))
        .pipe(gulp.dest('./style/css'));
});

gulp.task('css', function () {
    return gulp.src(['./style/css/bootstrap.min.css','./style/css/ignition.css','./style/css/gwl.css'])
        .pipe(concat('ignition.css'))
        .pipe(gulp.dest('./style/crushed'));
});

gulp.task('js', function () {
    return gulp.src(['./script/js/jquery-2.0.3.min.js','./script/js/jquery.autogrow-textarea.js','./script/js/bootstrap.min.js',
        './script/js/admin.js','./script/js/comments.js','./script/js/global.js',
        './script/js/collection.js','./script/js/game.js','./script/js/platforms.js','./script/js/user.js'])
        .pipe(uglify())
        .pipe(concat('ignition.js'))
        .pipe(gulp.dest('./script/crushed'))
    return true;
});

gulp.task('default', gulp.series('scss', gulp.parallel('css', 'js')));