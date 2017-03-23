var gulp = require('gulp');
var sass = require('gulp-sass');
var pug = require('gulp-pug');
var browserSync = require('browser-sync').create();
var useref = require('gulp-useref');
var uglify = require('gulp-uglify');
var gulpIf = require('gulp-if');
var cssnano = require('gulp-cssnano');

gulp.task('sass', function(){
  return gulp.src('app/scss/**/*.scss')
    .pipe(sass()) // Converts Sass to CSS with gulp-sass
    .pipe(gulp.dest('app/css'))
    .pipe(browserSync.reload({
        stream: true
    }))
});

gulp.task('pug', function(){
    return gulp.src('app/*.pug')
        .pipe(pug())
        .pipe(gulp.dest('app'))
        .pipe(browserSync.reload({
            stream: true
        }))
});

//recompiles/reloads browser upon file changes
gulp.task('watch', ['browserSync', 'sass', 'pug'], function() {
    gulp.watch('app/scss/**/*.scss', ['sass']);
    gulp.watch('app/*.pug', ['pug']);
    gulp.watch('app/*.html', browserSync.reload);
    gulp.watch('app/js/**/*.js', browserSync.reload);
});

//performs automatic reload of browser on changes to html, css, or javascript files
gulp.task('browserSync', function() {
    browserSync.init({
        server: {
            baseDir: 'app'
        }
    })
});

// creates one optimized JavaScript and CSS file
gulp.task('useref', function() {
    return gulp.src('app/*.html')
        .pipe(useref())
        .pipe(gulpIf('*.js', uglify()))
        .pipe(gulpIf('*.css', cssnano()))
        .pipe(gulp.dest('dist'))
});