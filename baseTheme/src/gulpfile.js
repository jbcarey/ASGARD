/**
 * Gulp Packages
 */

// General
var gulp = require('gulp');
var fs = require('fs');
var del = require('del');
var lazypipe = require('lazypipe');
var plumber = require('gulp-plumber');
var tap = require('gulp-tap');
var rename = require('gulp-rename');
var watch = require('gulp-watch');


// Scripts and tests
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var optimizejs = require('gulp-optimize-js');

// Styles
var less = require('gulp-less');
var lessPluginFunctions = require('less-plugin-functions');
var LessAutoprefix = require('less-plugin-autoprefix');
var LessPluginCleanCSS = require('less-plugin-clean-css');
var cssc = require('gulp-css-condense');
var cssnano = require('gulp-cssnano');

// Fonts
var googleWebFonts = require('gulp-google-webfonts');

// Images
var svgmin = require('gulp-svgmin');
var svgstore = require('gulp-svgstore');
var imageResize = require('gulp-image-resize-ar');

// favicon
var realFavicon = require('gulp-real-favicon');




/**
 * Paths to project folders
 */

var paths = {

  scripts: {
    input: './js/*',
    output: '../js/',
    plugins: [
      'node_modules/smooth-scroll/dist/js/smooth-scroll.js',
      'node_modules/gumshoe/dist/js/gumshoe.js',
      'node_modules/Modals/dist/js/modals.js',
    ],
  },
  
  styles: {
    input: './less/*.less',
    output: '../css/'
  },
  
  svgs: {
    input: ['./svg**', '!./svg/{b64,b64/**}'],
    output: '../svg/'
  },
  
  images: {
    input: './img/*',
    output: '../img/'
  },
  
  fonts: {
    input: './fonts/fonts.list',
    output: '../fonts/'
  }
  
};




/**
 * Gulp Taks
 */


// Merge js
gulp.task('build:plugins', function() {
  return gulp.src(paths.scripts.plugins)
    .pipe(gulp.dest('./js/'));
});



// Lint, minify, and concatenate scripts
gulp.task('build:js', function() {
  var jsTasks = lazypipe()
    .pipe(optimizejs)
    .pipe(gulp.dest, paths.scripts.output)
    .pipe(rename, { suffix: '.min' })
    .pipe(uglify)
    .pipe(optimizejs)
    .pipe(gulp.dest, paths.scripts.output);

  return gulp.src(paths.scripts.input)
    .pipe(plumber())
    .pipe(tap(function (file, t) {
      if ( file.isDirectory() ) {
        var name = file.relative + '.js';
        return gulp.src(file.path + '/*.js')
          .pipe(concat(name))
          .pipe(jsTasks());
      }
    }))
    .pipe(jsTasks());
});



// Process and minify Less files
gulp.task('build:less', function() {
  return gulp.src(paths.styles.input)
    .pipe(plumber())
    .pipe(less({
      plugins: [
        lessFunctions = new lessPluginFunctions(),
        cleanCSSPlugin = new LessPluginCleanCSS({
          advanced: true
        }),
        autoprefix = new LessAutoprefix({
          browsers: ['last 2 versions']
        })
      ]
    }))
    .pipe(cssc({
      consolidateViaDeclarations: false,
      compress: false
    }))
    .pipe(gulp.dest(paths.styles.output))
    .pipe(cssnano())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.styles.output))
});



// Generate svg sprites
gulp.task('build:svgs', function () {
  return gulp.src(paths.svgs.input)
    .pipe(plumber())
    .pipe(tap(function (file, t) {
      if ( file.isDirectory() ) {
        var name = file.relative + '.svg';
        return gulp.src(file.path + '/*.svg')
          .pipe(svgmin())
          .pipe(svgstore({
            fileName: name,
            prefix: 'icon-',
            inlineSvg: true
          }))
          .pipe(gulp.dest(paths.svgs.output));
      }
    }))
    .pipe(svgmin())
    .pipe(gulp.dest(paths.svgs.output));
});



// Copy image files into output folder
gulp.task('build:images', function() {
  return gulp.src(paths.images.input)
    .pipe(plumber())
    .pipe(imageResize({ 
      width : 1200,
      height : 600,
      crop : true,
      upscale : false
    }))
    .pipe(gulp.dest(paths.images.output));
});



// Generate fonts
gulp.task('build:fonts', function () {
  return gulp.src(paths.fonts.input)
    .pipe(googleWebFonts())
    .pipe(gulp.dest(paths.fonts.output));
});



// Generate favicons
gulp.task('build:favicon', function() {
  var faviconSettings = require('./favicon/config.json');
  realFavicon.generateFavicon(faviconSettings);
});



// Lint scripts
gulp.task('lint:js', function () {
  return gulp.src(paths.scripts.input)
    .pipe(plumber())
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'));
});









/**
 * Task Runners
 */


// Run al tasks
gulp.task('default', [
  'lint:js',
  'build:plugins',
  'build:js',
  'build:less',
  'build:images',
  'build:svgs',
  'build:fonts',
  'build:favicon'
]);



// watch scripts
gulp.task('watch:js', ['lint:js','build:js'], function () {
    gulp.watch('./js/**/*.js', ['build:js']);
});



// watch less styles
gulp.task('watch:less', ['build:less'], function () {
    gulp.watch('./less/**/*.less', ['build:less']);
});



