const mix = require('laravel-mix');
const glob = require('glob');

mix.setPublicPath('./')

// glob.sync('src/scss2/*.scss').map(function(file) {
  // mix.sass(file, 'public_html/dist/css2')
  // .options({
  //   postCss: [
  //     require('autoprefixer')({
  //       grid: true
  //     })
  //   ]
  // });
  // mix.sass(file, 'public_html/public/dev/css2')
  // .options({
  //   postCss: [
  //     require('autoprefixer')({
  //       grid: true
  //     })
  //   ]
  // });
// });

glob.sync('src/js2/*.js').map(function(file) {
  mix.js(file, 'public_html/dist/js2');
  mix.js(file, 'public_html/public/dev/js2');
});

// mix.options({
// 	processCssUrls: false,
// });
