var gulp = require('gulp');
var sass = require('gulp-sass');
var rename = require('gulp-rename');
var gulpSequence = require('gulp-sequence');
var coffee = require('gulp-coffee');
var concat = require('gulp-concat');
var ftp = require('vinyl-ftp');
var path = require('path');
var del = require('del');

const credentials = require('./credentials')()

const config = {
    src: 'src',
    dist: 'dist'
}

const paths = require('./path-config')(config)

gulp.task('clean', function() {
    return del(paths.build.dist);
});

gulp.task('php', function() {
   return gulp
       .src(path.resolve(paths.src.backend, '**/*'))
       .pipe(gulp.dest(paths.build.backend));
});

gulp.task('root', function() {
    return gulp
        .src(path.resolve(paths.src.root, '*.php'))
        .pipe(gulp.dest(paths.build.dist));
});

gulp.task('rename-to-70', function(){
    return gulp
        .src(path.resolve(paths.build.dist, 'proposalCollector.php'))
        .pipe(rename('proposalCollector.php70'));
//        .pipe(gulp.dest(paths.build.dist));
});

gulp.task('backend', function(cb) {
    gulpSequence('php', 'root')(cb);
});

gulp.task('sass', function () {
  return gulp
    .src(path.resolve(paths.src.styles, '**/*.scss'))
    .pipe(sass())
    .pipe(gulp.dest(paths.build.app.styles));
});

gulp.task('scripts',['coffee'], function() {
    return gulp
        .src(path.resolve(paths.src.scripts, '**/*'))
        .pipe(gulp.dest(paths.build.app.scripts));
});

gulp.task('coffee', function() {
    return gulp
        .src(path.resolve(paths.src.scripts, '**/*.coffee'))
        .pipe(coffee({bare: true}))
        .pipe(concat('coffee.js'))
        .pipe(gulp.dest(paths.build.app.scripts));
});

gulp.task('partials', function() {
    return gulp
        .src(path.resolve(paths.src.partials,'**/*'))
        .pipe(gulp.dest(paths.build.app.partials));
});

gulp.task('assets', function() {
    return gulp
        .src(path.resolve(paths.src.assets,'**/*'))
        .pipe(gulp.dest(paths.build.app.assets));
});

gulp.task('bootstrap', function() {
    return gulp
        .src([paths.src.bootstrap.css,paths.src.bootstrap.map])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('jsurl', function() {
    return gulp
        .src(paths.src.jsurl)
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('angular',['angular-route','angular-sanitize','angular-animate','angular-filter','angular-ui-bootstrap'], function() {
    return gulp
        .src([paths.src.angular.js,paths.src.angular.map])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('angular-route', function() {
    return gulp
        .src([paths.src.angular.route.js,paths.src.angular.route.map])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('angular-sanitize', function() {
    return gulp
        .src([paths.src.angular.sanitize.js,paths.src.angular.sanitize.map])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('angular-animate', function() {
    return gulp
        .src([paths.src.angular.animate.js,paths.src.angular.animate.map])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('angular-filter', function() {
    return gulp
        .src(paths.src.angular.filter.js)
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('angular-ui-bootstrap', function() {
    return gulp
        .src([paths.src.angular.uiBootstrap.js,paths.src.angular.uiBootstrap.tpls])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('app',['sass','scripts','partials','assets','bootstrap','jsurl', 'angular'], function() {
    return gulp
        .src(path.resolve(paths.src.app, 'index.html'))
        .pipe(gulp.dest(paths.build.app.root));
});

gulp.task('watch-app', function() {
  return gulp
    .watch(path.resolve(paths.src.app), ['app'])
    .on('change', function(event) {
      console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

gulp.task('watch-backend', function() {
    return gulp
        .watch(path.resolve(paths.src.backend), ['backend'])
        .on('change', function(event) {
            console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        });
});

gulp.task('watch', ['watch-app','watch-backend'], function(){

});

gulp.task('credentials', function() {
    return gulp
        .src('credentials.php')
        .pipe(gulp.dest(paths.build.dist));
});

gulp.task('publish', function() {

    console.log('Publishing to ' + credentials.server + ' ...');
    var conn = ftp.create( {
        host: credentials.server,
        user: credentials.user,
        password: credentials.pw
    } );

    return gulp.src(path.resolve(paths.build.dist, '**/*'), { base: '.', buffer: false } )
//        .pipe( conn.newer( '/test' ) ) // only upload newer files
        .pipe( conn.dest( '/test' ) );
});

gulp.task('dev', function (cb) {
    gulpSequence('clean',['backend','app'],'credentials')(cb);
});

gulp.task('build', function (cb) {
    gulpSequence('clean',['backend','app'],'rename-to-70')(cb);
});

gulp.task('deploy', function (cb) {
    gulpSequence('build','publish')(cb);
});

gulp.task('default', ['build'], function() {
  // place code for your default task here
});
