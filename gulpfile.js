var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var minifycss = require('gulp-minify-css');
var notify = require("gulp-notify");
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');

// --

var onError = function(err) {
    console.log(err.message);
    this.emit('end');
}

gulp.task('styles', function() {
    return gulp.src('./style.scss')
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sass())
        .pipe(autoprefixer({ browsers: ['last 3 versions', 'IE 9'] }))
        .pipe(minifycss())
        .pipe(gulp.dest('.'))
        .pipe(notify({
            'title': 'Swift',
            'message': 'Sass has been processed and minified!'
        }));
});

gulp.task('scripts', function() {
    return gulp.src(['./assets/js/app.js'])
        .pipe(plumber({ errorHandler: onError }))
        .pipe(uglify())
        .pipe(concat('app.min.js'))
        .pipe(gulp.dest('./assets/js'))
        .pipe(notify({
            'title': 'Swift',
            'message': 'Scripts have been concatenated and minified!'
        }));
});

gulp.task('watch', ['styles', 'scripts'], function() {
    gulp.watch(['./style.scss'], ['styles']);
    gulp.watch('./assets/js/**/*.js', ['scripts']);
});

gulp.task('default', ['styles', 'scripts']);