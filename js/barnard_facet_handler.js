/*global
    Drupal, jQuery
*/
'use strict';

// (function ($) { }

// THIS IS A STUB.
$ = jQuery;
var crumbs = $('ol.breadcrumb')

// ditch if can't find
if (!crumbs.length) {
  return;
}
// parse parts
var facets = crumbs.find('li')

// handle hiding of facets.
if (facets.length <= 1)
  crumbs.hide();
else {
  $(facets[0]).hide();
}
