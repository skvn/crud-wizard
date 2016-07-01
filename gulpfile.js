var config = require('./gulp-config.json');
var gulp = require('gulp');
var concat = require('gulp-concat');

gulp.task('default',['js']);


gulp.task('js', function() {
    return gulp
        .src(config.paths.vendor_js)
        .pipe(concat('vendor.min.js'))
        .pipe(gulp.dest(config.paths.dist_js));
});