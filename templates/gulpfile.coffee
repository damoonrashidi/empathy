sass   = require 'gulp-ruby-sass'
chmod  = require 'gulp-chmod'
prefix = require 'gulp-autoprefixer'
concat = require 'gulp-concat'
cleancss = require 'gulp-clean-css'
phpserver = require 'node-php-server'
browsersync = require('browser-sync').create()
gulp        = require 'gulp'

#pretty straight forward, edit as necessary
DIR = 
  JS : 
    APP : ['src/js/*.js', 'src/js/**/*.js']
    OUT : 'public/js/',
  CSS :
    IN : 'src/sass/'
    OUT : 'public/css/'

gulp.task 'sass', ->
  sass DIR.CSS.IN+"*.sass", style: "compressed"
    .pipe prefix browsers: ['last 2 versions']
    .pipe cleancss compatibility: 'ie8'
    .pipe chmod 644
    .pipe gulp.dest DIR.CSS.OUT
    .pipe browsersync.stream()

gulp.task 'js', ->
  gulp.src DIR.JS.APP
    .pipe gulp.dest DIR.JS.OUT
    .pipe browsersync.stream()

gulp.task 'serve', ->
    phpserver.createServer
      port: 1338,
      hostname: '127.0.0.1',
      base: '.',
      keepalive: false,
      open: false,
      bin: 'php',
      router: __dirname + '/app.php' 
  browsersync.init 
    proxy: 'localhost:1338'
  gulp.watch [DIR.CSS.IN+"*.sass", DIR.CSS.IN+"**/*.sass"], ['sass']
  gulp.watch [DIR.JS.APP], ['js']
  gulp.watch("*.html").on('change', browsersync.reload)
  return


gulp.task 'default', ['sass', 'js', 'serve']