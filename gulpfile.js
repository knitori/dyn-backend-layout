var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var cleanCSS = require('gulp-clean-css');

gulp.task('sass', function () {
    return gulp.src('Resources/Private/Scss/flexlayout.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions', 'Safari 5'],
            cascade: false
        }))
        .pipe(cleanCSS())
        .pipe(gulp.dest('Resources/Public/Css'));
});


gulp.task('watch', function () {
    gulp.watch('Resources/Private/Scss/**/*.scss', ['sass']);
});

gulp.task('default', ['watch']);