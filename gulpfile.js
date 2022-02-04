//let replace = require('gulp-replace'); //.pipe(replace('bar', 'foo'))
let { src, dest } = require("gulp");
let fs = require("fs");
let gulp = require("gulp");
let browsersync = require("browser-sync").create();
let autoprefixer = require("gulp-autoprefixer");
let scss = require("gulp-sass");
let group_media = require("gulp-group-css-media-queries");
let plumber = require("gulp-plumber");
let del = require("del");
let imagemin = require("gulp-imagemin");
let uglify = require("gulp-uglify-es").default;
let rename = require("gulp-rename");
let fileinclude = require("gulp-file-include");
let clean_css = require("gulp-clean-css");
let newer = require("gulp-newer");

let webp = require("imagemin-webp");
let webpcss = require("gulp-webpcss");
let webphtml = require("gulp-webp-html");

let fonter = require("gulp-fonter");

let ttf2woff = require("gulp-ttf2woff");
let ttf2woff2 = require("gulp-ttf2woff2");

let connect = require('gulp-connect-php');
let project_name = require("path").basename(__dirname);

let src_folder = "#src";

let path = {
  build: {
    html: __dirname + "/app/templates/",
    js: __dirname + "/public/js/",
    css: __dirname + "/public/css/",
    images: __dirname + "/public/images/",
    fonts: __dirname + "/public/fonts/",
    // html: project_name + "/",
    // js: project_name + "/public/js/",
    // css: project_name + "/public/css/",
    // images: project_name + "/public/images/",
    // fonts: project_name + "/fonts/",
  },
  src: {
    favicon: src_folder + "/images/favicon.{jpg,png,svg,gif,ico,webp}",
    html: [src_folder + "/*.html", "!" + src_folder + "/_*.html"],
    js: [src_folder + "/js/app.js", src_folder + "/js/vendors.js"],
    css: src_folder + "/scss/style.scss",
    images: [
      src_folder + "/images/**/*.{jpg,png,svg,gif,ico,webp}",
      "!**/favicon.*",
    ],
    fonts: src_folder + "/fonts/*.ttf",
  },
  watch: {
    html: src_folder + "/**/*.html",
    js: src_folder + "/js/**/*.js",
    css: src_folder + "/scss/**/*.scss",
    images: src_folder + "/images/**/*.{jpg,png,svg,gif,ico,webp}",
  },
  //clean: "./" + project_name + "/",
  clean: __dirname + "/public/",
  clean_template: __dirname + "/app/templates/template_*_v1.html",

};
function browserSync(done) {
  browsersync.init({
    // server: {
    //   // baseDir: "./" + project_name + "/",
    //   baseDir: __dirname + "/",

    // },
    proxy: 'http://euroheater.local/',
    host: 'euroheater.local',
    // open: 'external'
    // notify: false,
    // port: 3000,
  });
}


// var gulp        = require('gulp');
// var browserSync = require('browser-sync').create();



function html() {
  return src(path.src.html, {})
    .pipe(plumber())
    .pipe(fileinclude())
    .pipe(webphtml()) // вызывает ошибку при появлении в названи картинки - _, прбелов итд
    .pipe(dest(path.build.html));
    // .pipe(htmlReload());
}

function css() {
  return (
    src(path.src.css, {})
      .pipe(plumber())

      .pipe(
        scss({
          outputStyle: "expanded",
        })
      )

      .pipe(group_media())
      .pipe(browsersync.stream())
      /*
		.pipe(
			autoprefixer({
				grid: true,
				overrideBrowserslist: ["last 5 versions"],
				cascade: true
			})
		)
	*/
      .pipe(
        webpcss({
          webpClass: "._webp",
          noWebpClass: "._no-webp",
        })
      )
      .pipe(browsersync.stream())

      .pipe(dest(path.build.css))
      .pipe(clean_css())
      .pipe(
        rename({
          extname: ".min.css",
        })
      )
      .pipe(dest(path.build.css))
      .pipe(browsersync.stream())
  );
}
function js() {
  return src(path.src.js, {})
    .pipe(plumber())
    .pipe(fileinclude())
    .pipe(gulp.dest(path.build.js))
    .pipe(uglify(/* options */))
    .pipe(
      rename({
        suffix: ".min",
        extname: ".js",
      })
    )
    .pipe(dest(path.build.js))
    .pipe(browsersync.stream());
}
function images() {
  return src(path.src.images)
    .pipe(newer(path.build.images))
    .pipe(
      imagemin([
        webp({
          quality: 75,
        }),
      ])
    )
    .pipe(
      rename({
        extname: ".webp",
      })
    )
    .pipe(dest(path.build.images))
    .pipe(src(path.src.images))
    .pipe(newer(path.build.images))
    .pipe(
      imagemin({
        progressive: true,
        svgoPlugins: [{ removeViewBox: false }],
        interlaced: true,
        optimizationLevel: 3, // 0 to 7
      })
    )
    .pipe(dest(path.build.images));
}
function favicon() {
  return src(path.src.favicon)
    .pipe(plumber())
    .pipe(
      rename({
        extname: ".ico",
      })
    )
    .pipe(dest(path.build.html));
}
function fonts_otf() {
  return src("./" + src_folder + "/fonts/*.otf")
    .pipe(plumber())
    .pipe(
      fonter({
        formats: ["ttf"],
      })
    )
    .pipe(gulp.dest("./" + src_folder + "/fonts/"));
}

function fonts() {
  src(path.src.fonts)
    .pipe(plumber())
    .pipe(ttf2woff())
    .pipe(dest(path.build.fonts));
  return src(path.src.fonts)
    .pipe(ttf2woff2())
    .pipe(dest(path.build.fonts))
    .pipe(browsersync.stream());
}
function fontstyle() {
  let file_content = fs.readFileSync(src_folder + "/scss/fonts.scss");
  if (file_content == "") {
    fs.writeFile(src_folder + "/scss/fonts.scss", "", cb);
    return fs.readdir(path.build.fonts, function (err, items) {
      if (items) {
        let c_fontname;
        for (var i = 0; i < items.length; i++) {
          let fontname = items[i].split(".");
          fontname = fontname[0];
          if (c_fontname != fontname) {
            fs.appendFile(
              src_folder + "/scss/fonts.scss",
              '@include font("' +
                fontname +
                '", "' +
                fontname +
                '", "400", "normal");\r\n',
              cb
            );
          }
          c_fontname = fontname;
        }
      }
    });
  }
}

function cb() {}
function clean() {
  return del (path.clean_template, path.clean);
  
}
function watchFiles() {
  // gulp.watch([path.watch.html]).on('change', () => {
  //   plumber();
  //   fileinclude();
  //   webphtml(); // вызывает ошибку при появлении в названи картинки - _, прбелов итд
  //   dest(path.build.html);
  //   browserSync.reload();
  //   // done();
  // });
  gulp.watch([path.watch.html]).on('change', gulp.series(html, browsersync.reload));
  gulp.watch([path.watch.css], css);
  gulp.watch([path.watch.js], js);
  gulp.watch([path.watch.images], images);
}
// function htmlReload() {
//     html();
//     gulp.watch([path.watch.html]).on("change", browsersync.reload);
// }
  // gulp.watch([path.watch.html].on('change', () =>{
  //   browserSync.reload();
  //   done();
  // }), html);



// let build = gulp.series(clean, fonts_otf, gulp.parallel(html, css, js, favicon, images), fonts, gulp.parallel(fontstyle));
// let watch = gulp.parallel(build, watchFiles, browserSync);

// let build = gulp.series(clean, gulp.parallel(html, css));
// let watch = gulp.parallel(build, watchFiles, browserSync);

//let build = gulp.series(clean, fonts_otf, gulp.parallel(html, css, images));

let build = gulp.series(
  clean,
  fonts_otf,
  gulp.parallel(html, css, js, images),
  fonts,
  gulp.parallel(fontstyle)
);
let watch = gulp.parallel(build, watchFiles, browserSync);

exports.html = html;
exports.css = css;
exports.js = js;
// exports.favicon = favicon;
exports.fonts_otf = fonts_otf;
exports.fontstyle = fontstyle;
exports.fonts = fonts;
exports.images = images;
exports.clean = clean;
exports.build = build;
exports.watch = watch;
exports.default = watch;
