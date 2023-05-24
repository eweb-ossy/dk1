const { src, dest, watch } = require('gulp');
const plumber = require('gulp-plumber');
const dartSass = require('gulp-dart-sass');
const autoprefixer = require('gulp-autoprefixer');
const babel = require('gulp-babel');
const path = require("path");
const webpackStream = require("webpack-stream");
const webpackConfig = require("./webpack.config");
const webpack = require("webpack");
const named = require('vinyl-named');
const rename = require('gulp-rename');
const minifyJS = require('gulp-uglify');

const paths = {
    css: {
        src: 'src/scss/**/*.scss',
        dist: 'public_html/dist/css',
        dist2: 'public_html/public/dev/css'
    },
    js: {
        src: 'src/js/*.js',
        dist: 'public_html/dist/js',
        dist2: 'public_html/public/dev/js'
    },
    js2: {
        src: 'src/js2/*.js',
        dist: 'public_html/dist/js2',
        dist2: 'public_html/public/dev/js2'
    }
}

const cssSass = () => 
    src(paths.css.src, { sourcemap: false })
        .pipe(plumber())
        .pipe(dartSass({ outputStyle: 'compressed' }))
        .pipe(autoprefixer())
        .pipe(rename({ extname: '.min.css' }))
        .pipe(dest(paths.css.dist))
        .pipe(dest(paths.css.dist2));

const jsCompile = () =>
    src(paths.js.src)
        .pipe(plumber())
        .pipe(minifyJS())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(dest(paths.js.dist))
        .pipe(dest(paths.js.dist2));

const jsCompile2 = () =>
    src(paths.js2.src)
        .pipe(plumber())
        .pipe(babel({ presets: ['@babel/preset-env'] }))
        .pipe(named((file) => {
            return path.parse(file.relative).name;
        }))
        .pipe(webpackStream(webpackConfig, webpack))
        .pipe(dest(paths.js2.dist))
        .pipe(dest(paths.js2.dist2));

const watchFiles = () => {
    watch(paths.css.src, cssSass)
    watch(paths.js.src, jsCompile)
    watch(paths.js2.src, jsCompile2)
}

exports.default = watchFiles;