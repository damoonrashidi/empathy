coffee = require 'gulp-coffee'
sass   = require 'gulp-ruby-sass'
chmod  = require 'gulp-chmod'
prefix = require 'gulp-autoprefixer'
concat = require 'gulp-concat'
uglify = require 'gulp-uglify'
browsersync = require('browser-sync').create()
minifyHTML  = require 'gulp-minify-html'
minifyCSS   = require 'gulp-minify-css'
minifyJS    = require 'gulp-uglify'
gulp        = require 'gulp'

#pretty straight forward, edit as necessary
DIR = 
  JS : 
    APP : 'src/coffee/*.coffee'
    OUT : 'public/js/',
  CSS :
    IN : 'src/sass/'
    OUT : 'public/css/'

gulp.task 'sass', ->
  sass DIR.CSS.IN+"app.sass", style: "compressed"
    .pipe prefix browsers: ['last 2 versions']
    .pipe minifyCSS compatibility: 'ie8'
    .pipe chmod 644
    .pipe gulp.dest DIR.CSS.OUT
    .pipe browsersync.stream()

gulp.task 'coffee', ->
  gulp.src DIR.JS.APP
    .pipe coffee bare: true
    .pipe concat "app.js"
    .pipe chmod 644
    .pipe gulp.dest DIR.JS.OUT
    .pipe browsersync.stream()


gulp.task 'serve', ->
  browsersync.init server: 
    proxy: 'localhost:1338'
  gulp.watch [DIR.CSS.IN+"app.sass", DIR.CSS.IN+"**/*.sass"], ['sass']
  gulp.watch [DIR.JS.APP, DIR.JS.DIRECTIVES], ['coffee']
  gulp.watch("*.html").on('change', browsersync.reload)
  return


gulp.task 'default', ['sass', 'coffee', 'serve']