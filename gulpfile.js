'use strict';

const gulp         = require('gulp')
const debug        = require('gulp-debug')
const plumber      = require('gulp-plumber')
const notify       = require('gulp-notify')
const sourcemaps   = require('gulp-sourcemaps')
const sass         = require('gulp-sass')
const autoprefixer = require('gulp-autoprefixer');
const gcmq         = require('gulp-group-css-media-queries')
const cleanCSS     = require('gulp-clean-css')
const rollup       = require('gulp-better-rollup')
const uglify       = require('gulp-uglify')
const rename       = require('gulp-rename')
const browserSync  = require('browser-sync').create()
const pkg          = require('./package.json')

const rollupConfig = () => {
  const resolve  = require('rollup-plugin-node-resolve')
  const commonjs = require('rollup-plugin-commonjs')
  const babel    = require('rollup-plugin-babel')

  return {
    rollup: require('rollup'),
    plugins: [
      resolve(),
      commonjs(),
      babel({
        babelrc: false,
        runtimeHelpers: true,
        externalHelpers: true,
        presets: ['@babel/preset-env']
      }),
    ]
  }
}

/**
 * Handle errors and alert the user.
 */
const handleErrors = (r) => {
  notify.onError('ERROR: <%= error.message %>\n')(r)
}

gulp.task('scss', () => {
  return gulp.src('src/Resources/scss/*.scss')
     .pipe(debug())
     .pipe(plumber(handleErrors))
     .pipe(sourcemaps.init())
     .pipe(sass().on('error', sass.logError))
     .pipe(autoprefixer())
     .pipe(gcmq())
     .pipe(sourcemaps.write('./'))
     .pipe(gulp.dest('css'))
     .pipe(browserSync.stream({ match: '**/*.css' }))
})

gulp.task('babel', () => {
  return gulp.src('src/Resources/js/*.js')
    .pipe(debug())
    .pipe(plumber(handleErrors))
    .pipe(sourcemaps.init())
    .pipe(rollup(rollupConfig(), {
      extend: true,
      format: 'iife',
      globals: pkg.globals || {}
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('js'))
});

gulp.task('minify:js', () => {
  return gulp.src(['js/*.js', '!js/*.min.js'])
     .pipe(debug())
     .pipe(plumber())
     .pipe(uglify())
     .pipe(rename({ suffix: '.min' }))
     .pipe(gulp.dest('js'))
})

gulp.task('minify:css', () => {
  return gulp.src(['css/*.css', '!css/*.min.css'])
    .pipe(debug())
    .pipe(plumber(handleErrors))
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('css'))
})

gulp.task('copy', (done) => {
  gulp.src(['node_modules/selectize/dist/css/selectize.css', 'node_modules/selectize/dist/js/standalone/selectize*.js'])
      .pipe(gulp.dest('libs/selectize'))

  done()
})

gulp.task('watch', () => {
  browserSync.init({
    open: false,
    proxy: 'http://awebooking.local',
  })

  gulp.watch('src/Resources/scss/**/*.scss', gulp.series(['scss']))
  gulp.watch('src/Resources/js/**/*.js', gulp.series(['babel']))
})

gulp.task('js', gulp.series(['babel', 'minify:js']))
gulp.task('css', gulp.series(['scss', 'minify:css']))
gulp.task('default', gulp.series(['css', 'js']))
