var gulp = require('gulp');
var sass = require('gulp-sass');
var rename = require('gulp-rename');

var buildFolder = './build/';
var input = './app/scss/*.scss';
var output = './build/app/css';

gulp.task('sass', function () {
  return gulp
    // Find all `.scss` files from the `stylesheets/` folder
    .src(input)
    // Run Sass on those files
    .pipe(sass())
    // Write the resulting CSS in the output folder
    .pipe(gulp.dest(output));
});

gulp.task('watch', function() {
  return gulp
    // Watch the input folder for change,
    // and run `sass` task when something happens
    .watch(input, ['sass'])
    // When there is a change,
    // log a message in the console
    .on('change', function(event) {
      console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

gulp.task('build',['copy-home','copy-rest','copy-php','copy-app','sass'], function () {
    console.log('building project...')
});

gulp.task('copy-home',['rename-collector'], function() {
   return gulp
       .src('./*.php')
       .pipe(gulp.dest(buildFolder));
});

gulp.task('rename-collector', function () {
    return gulp
        .src(buildFolder + 'proposalCollector.php')
        .pipe(rename('proposalCollector.php70'))
        .pipe(gulp.dest(buildFolder));
})

gulp.task('copy-rest', function () {
    return gulp
        .src('./restEndpoint/index.php')
        .pipe(gulp.dest(buildFolder + 'restEndpoint'));
});

gulp.task('copy-php', function () {
    return gulp
        .src('./classes/*.php')
        .pipe(gulp.dest(buildFolder + 'classes'));
});

gulp.task('copy-js', function () {
    return gulp
        .src('./app/js/*.{js,map}')
        .pipe(gulp.dest(buildFolder + 'app/js'));
});

gulp.task('copy-partials', function () {
    return gulp
        .src('./app/partials/*.html')
        .pipe(gulp.dest(buildFolder + 'app/partials'));
});

gulp.task('copy-assets', function () {
    return gulp
        .src('./app/assets/*.{jpeg,png,jpg}')
        .pipe(gulp.dest(buildFolder + 'app/assets'));
});

gulp.task('copy-app',['copy-js','copy-assets','copy-partials'], function () {
    return gulp
        .src('./app/*.html')
        .pipe(gulp.dest(buildFolder + 'app'));
});



gulp.task('default', ['sass', 'watch'], function() {
  // place code for your default task here
});
