/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('../../node_modules/bootstrap/dist/css/bootstrap.min.css')
require('../../node_modules/@fortawesome/fontawesome-free/css/all.css')

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
require('popper.js')
require('bootstrap')
require('holderjs')

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
