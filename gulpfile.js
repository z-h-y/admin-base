'use strict';

var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var livereload = require('gulp-livereload');
var replace = require('gulp-replace');

var baseLibs = [
  'public/lib/jQuery/jquery.js',
  'public/lib/jQuery-datatable/js/jquery.dataTables.js',
  'public/lib/bootstrap/js/bootstrap.js',
  'public/lib/metisMenu/metisMenu.js',
  'public/lib/lodash/lodash.js',
  'public/lib/moment/moment.js',
  'public/lib/md5/md5.js',
  'public/lib/angular/angular.js',
  'public/lib/angular/angular-locale_zh-cn.js',
  'public/lib/angular-datatable/angular-datatables.js',
  'public/lib/angular-datatable-bootstrap/angular-datatables.bootstrap.js',
  'public/lib/datatables-fixedcolumns/js/dataTables.fixedColumns.js',
  'public/lib/angular-fixedcolumns/fixedcolumns/angular-datatables.fixedcolumns.js',
  'public/lib/angular/angular-cookies.js',
  'public/lib/angular/angular-animate.js',
  'public/lib/angular-ui-router/angular-ui-router.js',
  'public/lib/restangular/restangular.js',
  'public/lib/angular-loading-bar/loading-bar.js',
  'public/lib/highcharts-ng/highcharts-ng.js',
  'public/lib/highstock/highstock.src.js',
  'public/lib/highcharts-more/highcharts-more.js',
  'public/lib/ui-bootstrap/ui-bootstrap-tpls-0.13.4.js',
  'public/lib/angular-multi-select-master/isteven-multi-select.js',
  'public/lib/ng-file-upload/ng-file-upload.js',
  'public/lib/textAngular/textAngular.js',
  'public/lib/textAngular/textAngularSetup.js',
  'public/lib/textAngular/textAngular-rangy.min.js',
  'public/lib/textAngular/textAngular-sanitize.js',
  'public/lib/angular-emoji-popup-master/js/config.js',
  'public/lib/angular-emoji-popup-master/js/emoji.min.js',
  'public/lib/angular-daterangepicker/daterangepicker.min.js',
  'public/lib/angular-daterangepicker/angular-daterangepicker.min.js',
  'public/lib/angular-local-storage/angular-local-storage.min.js'
];

var baseJs = [
  'public/js/app.js',
  'public/js/utils.js',
  'public/js/config.js',
  'public/js/controllers/admin/user.js',
  'public/js/controllers/admin/role.js',
  'public/js/controllers/admin/permission.js',
  'public/js/controllers/admin/codec.js',
  'public/js/controllers/main.js',
  'public/js/controllers/login.js',
  'public/js/controllers/index.js',
  'public/js/controllers/dashboard.js',
  'public/js/services/common.js',
  'public/js/services/leancloud.js',
  'public/js/services/actions.js'
];

// Add app's addtional libs here
var appLibs = [];

// Add app's js files here
var appJs = [];

gulp.task('build-lib', function() {
  var libFiles = baseLibs.concat(appLibs);
  gulp.src(libFiles)
    .pipe(concat('lib.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./public/dist'));
});

gulp.task('build-app', function() {
  var jsFiles = baseJs.concat(appJs);
  if (jsFiles && jsFiles.length) {
    gulp.src(jsFiles)
      .pipe(concat('app.js'))
      .pipe(uglify())
      .pipe(gulp.dest('./public/dist'));
  }
});

gulp.task('dist', ['build-lib', 'build-app', 'cachebust']);

gulp.task('watch', function() {
  var views = ['public/views/**/*.html'];
  var files = appLibs.concat(appJs).concat(views);
  if (files && files.length) {
    livereload.listen();
    gulp.watch(files, function() {
      gulp.src(files)
        .on('error', console.log.bind(console))
        .pipe(livereload());
    });
  }
});

//build datestamp for cache busting
var getStamp = function() {
  var myDate = new Date();
  var myYear = myDate.getFullYear().toString();
  var myMonth = ('0' + (myDate.getMonth() + 1)).slice(-2);
  var myDay = ('0' + myDate.getDate()).slice(-2);
  var hour = ('0' + myDate.getHours()).slice(-2);
  var minute = ('0' + myDate.getMinutes()).slice(-2);
  var myFullDate = myYear + myMonth + myDay + hour + minute;
  return myFullDate;
};

// Cache busting task
gulp.task('cachebust', function() {
  return gulp.src('./public/app.html')
    .pipe(replace(/app.js\?([0-9]*)/g, 'app.js?' + getStamp()))
    .pipe(replace(/lib.js\?([0-9]*)/g, 'lib.js?' + getStamp()))
    .pipe(gulp.dest('./public/'));
});
