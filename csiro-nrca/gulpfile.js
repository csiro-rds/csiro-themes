'use strict';

const gulp = require('gulp');
const less = require('gulp-less');
const rimraf = require('rimraf');

/* Clean tasks (inverse of build tasks) */

gulp.task('clean:less', (callback) => rimraf('css', callback));

gulp.task('clean:fonts', (callback) => rimraf('fonts', callback));

gulp.task('clean:js', (callback) => rimraf('js', callback));

gulp.task('clean', [ 'clean:less', 'clean:fonts', 'clean:js' ]);

/* Build tasks */

gulp.task(
    'build:less',
    [ 'clean:less' ],
    () => gulp.src('less/base.less')
        .pipe(less({
            paths: [ 'less' ]
        }))
        .pipe(gulp.dest('css')));

gulp.task(
    'build:fonts',
    [ 'clean:fonts' ],
    () => gulp.src('node_modules/bootstrap/fonts/**')
        .pipe(gulp.dest('fonts')));

gulp.task(
    'build:js',
    [ 'clean:js' ],
    () => gulp.src('node_modules/bootstrap/dist/js/bootstrap.min.js')
        .pipe(gulp.dest('js')));

gulp.task('build', [ 'clean', 'build:less', 'build:fonts', 'build:js' ]);

/* File watching tasks */

gulp.task(
    'watch:less',
    () => gulp.watch(
        [ 'less/**/*.less', 'views/**/*.less', 'less/orig/*.css' ],
        [ 'build:less' ]));

gulp.task('watch', [ 'watch:less' ]);

/* Default task (build now and then watch for changes) */

gulp.task('default', [ 'build', 'watch' ]);
