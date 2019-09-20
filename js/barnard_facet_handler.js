/*global
    Drupal, jQuery
*/
'use strict';

(function ($) {
  Drupal.behaviors.barnardIslandoraFacets = {
    attach: function (context, settings) {
      let crumbs = $('ol.breadcrumb');

      if (!crumbs.length) return;

      let facets = crumbs.find('li');

      if (facets.length <= 1) {
        crumbs.hide();
      } else {
        $(facets[0]).remove();
      }

    }
  };
})( jQuery );
