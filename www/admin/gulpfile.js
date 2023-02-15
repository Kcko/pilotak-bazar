
/*!
 * BOOTSTRAP GULPFILE for administration
 */


// Variables init
var gulp       = require("gulp"),
    rename     = require("gulp-rename"),
    del        = require("del"),
    stripDebug = require('gulp-strip-debug'),
    sass       = require('gulp-sass');


sass.compiler  = require('node-sass');    


var paths = {

    nodeDir: './node_modules/',
    dest: './js/packages/',
    sassDir: './scss/',
    cssDir: './css/',
	libs: [
		'./node_modules/tabler-ui/src/**',
		'./node_modules/bootstrap/dist/**',
		'./node_modules/bootstrap/scss/**',
		'./node_modules/air-datepicker/dist/**',
		'./node_modules/sortablejs/Sortable.js',
		'./node_modules/easy-autocomplete/dist/**',
		'./node_modules/spectrum-colorpicker/i18n/**',
		'./node_modules/spectrum-colorpicker/spectrum.css',
        './node_modules/spectrum-colorpicker/spectrum.js',
        './node_modules/sticky-table-headers/js/**',
        './node_modules/selectize/dist/**',
        './node_modules/nestedSortable/jquery.mjs.nestedSortable.js',
        './node_modules/jquery-sparkline/jquery.sparkline.js',
        './node_modules/file-uploader/client/**',
		
	],

};



gulp.task('copy', function() {
	return gulp
        .src(paths.libs, {base: paths.nodeDir})
		.pipe(gulp.dest(paths.dest));
});



gulp.task('del', function() {
    return del([
        paths.dest + '/**'
    ]);
  });
  


gulp.task('rename', function(){

    return gulp.src(paths.dest + "/**/*.css")
            .pipe(rename({
                prefix: '_',
                extname: '.scss',
            })).pipe(gulp.dest(paths.dest));
});

 

gulp.task('sass', function () {
    return gulp.src(paths.sassDir + 'screen.scss')
            //.pipe(sourcemaps.init())
            .pipe(sass.sync({
                outputStyle: 'compressed'
            }).on('error', sass.logError))
            //.pipe(sourcemaps.write())
            .pipe(gulp.dest(paths.cssDir));
  });




/* DEFAULT TASK  -> cmd: "gulp"
---------------------------------------------------------------------------------------------------- */
gulp.task('default',
    gulp.series('copy', 'rename', 'sass')
);





