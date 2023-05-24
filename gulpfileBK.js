const gulp = require('gulp'); // gulp
const sass = require('gulp-sass'); // Sassをコンパイル
const minifyCSS = require('gulp-clean-css'); // cssをファイルminify
const autoprefixer = require('gulp-autoprefixer'); // cssをベンダープレフィックス
const rename = require('gulp-rename'); // ファイルをリネイム
const minifyJS = require('gulp-uglify'); // JavaScriptをファイルminify
const babel = require('gulp-babel'); // JavaScriptのES6
const imagemin = require('gulp-imagemin');
const mozjpeg = require('imagemin-mozjpeg');
const pngquant  = require('imagemin-pngquant');

// path
const paths = {
  styles : {
    src : 'src/scss/**/*.scss',
    outputPublic : 'public_html/dist/css/',
    outputDev : 'public_html/public/dev/css/'
  },
  // styles : {
  //   src : 'src/scss2/**/*.scss',
  //   outputPublic : 'public_html/dist/css2/',
  //   outputDev : 'public_html/public/dev/css2/'
  // },
  scripts : {
    src : 'src/js/**/*.js',
    outputPublic : 'public_html/dist/js/',
    outputDev : 'public_html/public/dev/js/'
  },
  images : {
    src : 'src/images/**/*.{jpg,jpeg,png,gif,svg}',
    outputPublic : 'public_html/dist/images/',
    outputDev : 'public_html/public/dev/images/'
  }
};

// styles
function styles() {
  return gulp.src(paths.styles.src, { sourcemaps: false })
    .pipe(sass()) // Sassコンパイル
    .pipe(autoprefixer({cascade: false})) // ベンダープレフィックス
    .pipe(minifyCSS()) // cssをminifyする
    .pipe(rename({extname: '.min.css'})) // ファイル名を変える
    .pipe(gulp.dest(paths.styles.outputPublic, { sourcemaps: false })) // 出力
    .pipe(gulp.dest(paths.styles.outputDev)) // 出力
}

// scripts
function scripts() {
  return gulp.src(paths.scripts.src)
    // .pipe(babel({presets: ['@babel/env']})) // ES6対応
    // .pipe(babel({presets: ['@babel/preset-env']})) // ES6対応
    .pipe(minifyJS()) // JavaScriptをminifyする
    .pipe(rename({extname: '.min.js'})) // ファイル名を変える
    .pipe(gulp.dest(paths.scripts.outputPublic)) // 出力
    .pipe(gulp.dest(paths.scripts.outputDev)) // 出力
}

// Images
function images() {
  return gulp.src(paths.images.src, {since: gulp.lastRun(images)})
    .pipe(imagemin([
      pngquant({
        quality: [.7, .85]
      }),
      mozjpeg({
        quality: 85
      }),
      imagemin.gifsicle(),
      imagemin.svgo()
    ]))
    .pipe(gulp.dest(paths.images.outputPublic))
    .pipe(gulp.dest(paths.images.outputDev))
}

// 自動監視
function watch() {
  gulp.watch(paths.styles.src, styles);
  gulp.watch(paths.scripts.src, scripts);
  gulp.watch(paths.images.src, images);
}

const build = gulp.parallel(styles, scripts);

exports.styles = styles;
exports.scripts = scripts;
exports.images = images;
exports.watch = watch;
exports.build = build;
exports.default = watch;

// run
// $ npx gulp
