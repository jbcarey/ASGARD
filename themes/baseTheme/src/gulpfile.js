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
var livereload = require('gulp-livereload');

// html
var htmlmin = require('gulp-htmlmin');

// Scripts and tests
//var jshint = require('gulp-jshint');
//var stylish = require('jshint-stylish');
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
    input: './**/*',
    output: '../',
    scripts: {
        input: './js/*',
        output: '../js/'
    },
    styles: {
        input: './less/template.less',
        output: '../less-css/'
    },
    svgs: {
        input: ['./svg**', '!./svg/{b64,b64/**}'],
        output: '../svg/'
    },
    images: {
        input: './img/pictures/*',
        output: '../img/pictures/',
        thumbs: {
            input: './img/pictures/thumbs/*',
            output: '../img/pictures/thumbs/'
        }
    },
    html: {
        input: './html/*',
        output: '../'
    },
    fonts: {
        input: './fonts/fonts.list',
        output: '../fonts/'
    }
};





/**
 * Gulp Taks
 */

// merge js
gulp.task('build:plugins', function() {
  return gulp.src(paths.scripts.plugins)
    .pipe(gulp.dest('./js/plugins/'));
});


// Lint, minify, and concatenate scripts
gulp.task('build:scripts', function() {
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
gulp.task('build:styles', function() {
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
        .pipe(livereload());
});



// Generate SVG sprites
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
gulp.task('build:images', ['build:thumbs'], function() {
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


// Copy image thumbnail files into output folder
gulp.task('build:thumbs', function() {
    return gulp.src(paths.images.thumbs.input)
        .pipe(plumber())
        .pipe(imageResize({ 
          width : 180,
          height : 180,
          crop : true,
          upscale : false
        }))
        .pipe(gulp.dest(paths.images.thumbs.output));
});


// Copy html files into output folder
gulp.task('build:html', function() {
    return gulp.src(paths.html.input)
        .pipe(plumber())
        .pipe(htmlmin({
            collapseWhitespace: true,
            collapseInlineTagWhitespace: true,
            collapseBooleanAttributes: true,
            minifyCSS: true,
            minifyJS: true,
            removeEmptyAttributes: true,
            removeEmptyElements: true
        }))
        .pipe(gulp.dest(paths.html.output));
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
gulp.task('lint:scripts', function () {
    return gulp.src(paths.scripts.input)
        .pipe(plumber())
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'));
});

// Remove pre-existing content from output and test folders
gulp.task('clean:dist', function () {
    del.sync([
        paths.output
    ]);
});

// Spin up livereload server and listen for file changes
gulp.task('listen', function () {
    livereload.listen();
    gulp.watch(paths.input).on('change', function(file) {
        gulp.start('devel');
        //gulp.start('refresh');
    });
});

// Run livereload after file change
gulp.task('refresh', ['devel'], function () {
    livereload.changed();
});







/**
 * Task Runners
 */

// Compile files
gulp.task('default', [
//  'lint:scripts',
//  'clean:dist',
    'build:plugins',
    'build:scripts',
    'build:styles',
//  'build:images',
//  'build:html',
    'build:svgs',
    'build:fonts',
    'build:favicon'
]);

/**
 * Task Runners
 */

// Compile files
gulp.task('devel', [
//  'lint:scripts',
    'build:scripts',
    'build:styles',
//  'build:html'
]);


// Compile files and generate docs when something changes
gulp.task('watch', [
    'listen',
    'devel'
]);

// Spin up livereload server and listen for file changes 
gulp.task('watch-less', function () {
    gulp.watch('./less/**/*.less', ['build:styles']);
});
