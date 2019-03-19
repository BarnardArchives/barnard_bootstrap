  /**
 * @file
 * Javascript file to disable the date range slider faceting
 */

(function ( $ ) {

  Drupal.behaviors.islandoraSolrRangeSlider = {
    attach: function(context, settings) {
      var rangeSliderVals = '';
      return false;
    }
  }

})( jQuery );
