var gulp = require('gulp');
var sass = require('gulp-sass');
var rename = require('gulp-rename');
var gulpSequence = require('gulp-sequence');
var path = require('path');
var del = require('del');

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

gulp.task('rename-to-70', function(){
    return gulp
        .src(path.resolve(paths.build.dist, 'proposalCollector.php'))
        .pipe(rename('proposalCollector.php70'));
//        .pipe(gulp.dest(paths.build.dist));
});

gulp.task('backend', function(cb) {
    gulpSequence('php','rename-to-70')(cb);
});

gulp.task('sass', function () {
  return gulp
    .src(path.resolve(paths.src.styles, '**/*.scss'))
    .pipe(sass())
    .pipe(gulp.dest(paths.build.app.styles));
});

gulp.task('scripts', function() {
    return gulp
        .src(path.resolve(paths.src.scripts, '**/*'))
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
        .src([paths.src.bootstrap.js,paths.src.bootstrap.css,paths.src.bootstrap.map])
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('jsurl', function() {
    return gulp
        .src(paths.src.jsurl)
        .pipe(gulp.dest(paths.build.app.vendor));
});

gulp.task('app',['sass','scripts','partials','assets','bootstrap','jsurl'], function() {
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

gulp.task('dev-db', function() {
    return gulp
        .src('db.php')
        .pipe(gulp.dest(paths.build.dist));
})

gulp.task('dev', function (cb) {
    gulpSequence('clean',['backend','app'],'dev-db')(cb);
});

gulp.task('build', function (cb) {
    gulpSequence('clean',['backend','app'])(cb);
});

gulp.task('default', ['build'], function() {
  // place code for your default task here
});
