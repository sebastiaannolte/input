const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')
const svgToDataUri = require('mini-svg-data-uri')

module.exports = {
"purge":["./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php","./storage/framework/views/*.php","./resources/views/**/*.blade.php","./resources/js/**/*.vue"],"theme":{
"colors":{
"transparent":"transparent","current":"currentColor","black":colors.black,"white":colors.white,"gray":colors.gray,"red":colors.red,"yellow":colors.amber,"blue":colors.sky,"indigo":colors.sky,"green":colors.green},"extend":{
"fontFamily":{
"sans":["Nunito",...defaultTheme.fontFamily.sans],"inter":["\"Inter\"","sans-serif"]},"spacing":{
"120":"33rem"},"minHeight":{
"20":"5rem"},"backgroundImage":(theme) => ({
                            'multiselect-caret': `url("${
svgToDataUri(
                  `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http:
                )}")`,
                'multiselect-spinner': `url("${
svgToDataUri(
                  `<svg viewBox="0 0 512 512" fill="${
theme('colors.green.500')}" xmlns="http:
                )}")`,
                'multiselect-remove': `url("${
svgToDataUri(
                  `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http:
                )}")`,
              })}},"variants":{
"extend":{
"opacity":["disabled"],"fontWeight":["first"],"fontSize":["first"]}},"plugins":[require('@tailwindcss/forms')]}
