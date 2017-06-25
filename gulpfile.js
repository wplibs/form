'use strict';

var del          = require('del');
var gulp         = require('gulp');
var bs           = require('browser-sync').create();
var series       = require('run-sequence');
var buffer       = require('vinyl-buffer');

var sass         = require('gulp-sass');
var cssnano      = require('gulp-cssnano');
var autoprefixer = require('gulp-autoprefixer');

var babelify     = require('babelify');
var browserify   = require('browserify');
var uglify       = require('gulp-uglify');
var concat       = require('gulp-concat');
var include      = require('gulp-include');

var tap          = require('gulp-tap');
var rename       = require('gulp-rename');
var sourcemaps   = require('gulp-sourcemaps');
var gutil        = require('gulp-util');
var changed      = require('gulp-changed');
var notify       = require('gulp-notify');
var plumber      = require('gulp-plumber');
var sort         = require('gulp-sort');
var wppot        = require('gulp-wp-pot');

/**
 * Handle errors and alert the user.
 */
function handleErrors() {
  var args = Array.prototype.slice.call(arguments);

  notify.onError({
    'title': 'Task Failed! See console.',
    'message': "\n\n<%= error.message %>",
    'sound': 'Sosumi' // See: https://github.com/mikaelbr/node-notifier#all-notification-options-with-their-defaults
  }).apply(this, args);

  gutil.beep(); // Beep 'sosumi' again

  // Prevent the 'watch' task from stopping
  this.emit('end');
}

gulp.task('wp-pot', function () {
  return gulp.src(['inc/**/*.php', 'i18n/*.php'])
    .pipe(plumber({ 'errorHandler': handleErrors }))
    .pipe(sort())
    .pipe(wppot({
      'domain': 'skeleton',
      'package': 'awethemes/skeleton',
    }))
    .pipe(gulp.dest('i18n/skeleton.pot'));
});

gulp.task('sass', function () {
  return gulp.src('resouces/sass/*.scss')
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(sass({ errLogToConsole: true }))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('css'))
    .pipe(bs.stream({ match: '**/*.css' }));
});

gulp.task('css:minify', ['sass'], function () {
  return gulp.src(['css/*.css', '!css/*.min.css'])
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(rename({ extname: '.min.css' }))
    .pipe(cssnano())
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('css'))
    .pipe(bs.stream({ match: '**\/*.css' }));
});


gulp.task('js', function () {
  var tapCallback = function(file) {
    gutil.log('Bundling: ' + file.path);

    file.contents = browserify(file.path, { debug: true })
      .transform(babelify, { presets: ['es2015'] })
      .bundle();
  };

  return gulp.src('resouces/js/*.js', { read: false })
    .pipe(plumber({ errorHandler: handleErrors }))
    // .pipe(changed('js'))
    .pipe(tap(tapCallback))
    .pipe(buffer())
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(include({ includePaths: __dirname + '/resouces/js' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('js'))
    .pipe(bs.stream());
});

gulp.task('js:minify', ['js'], function () {
  return gulp.src(['js/skeleton.js'])
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(rename({ extname: '.min.js' }))
    .pipe(uglify({ preserveComments: 'license' }))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('js'))
    .pipe(bs.stream());
});

gulp.task('watch', function () {
  // bs.init({
  //   // files: ['inc/**/*.php', '*.php'],
  //   proxy: 'wp.dev',
  //   snippetOptions: {
  //     // whitelist: ['/wp-admin/admin-ajax.php'],
  //     // blacklist: ['/wp-admin/**'],
  //   }
  // });

  gulp.watch(['resouces/js/**/*.js'], ['js']);
  gulp.watch(['resouces/sass/**/*.scss'], ['sass']);
});

gulp.task('clean', function () {
  return del([
    'css/maps',
    'css/skeleton*.css',
    'js/maps',
    'js/skeleton*.js',
    'i18n/skeleton.pot',
  ]);
});

gulp.task('build', function (callback) {
  series('clean', ['css:minify', 'js:minify'], 'wp-pot', callback);
});

gulp.task('default', function (callback) {
  series('build', 'watch', callback);
});
