/*global
    Drupal, jQuery
*/
'use strict';

(function ($) {
  // Modifies search result heading per: https://github.com/BarnardArchives/digitalcollections.barnard.edu/issues/75
  Drupal.behaviors.solrSearchResultHeading = {
    attach: function (context, settings) {

      const pageHeading = $('h1.page-header'),
          islandoraResultCount = $('div#islandora-solr-result-count');

      islandoraResultCount.hide(); // I could hook the appropriate code in an alter but no thanks.

      if (typeof settings.islandoraSolrContent !== 'undefined' &&
          settings.islandoraSolrContent.userSearch === false) return;

      if (!islandoraResultCount.length) return;

      pageHeading.once('solr-result-title',
          function () {
            const resultCount = islandoraResultCount.text().match(/.*of.((\d{1,3})(,\d{3}))/i)[1],
                spanResultCount = $('<span />', {
                  id: 'title-bound-solr-result-count',
                  text: resultCount,
                  style: 'font-weight: 900', // this should be removed and done in CSS. -BR
                }).append(" ");
            if (typeof settings.islandoraSolrContent !== 'undefined' &&
                settings.islandoraSolrContent.userSearch === true) {
              pageHeading.prepend("results in ");
            }
            pageHeading.prepend(spanResultCount);
          });
    }
  };

  Drupal.behaviors.solrSearchClearBlankSearch = {
    attach: function (context, settings) {

      const fieldZero = $('#edit-terms-0-search');

      if (!fieldZero.length) return;

      if (fieldZero.val() === ' ') fieldZero.val('');
    }
  };

  //
})( jQuery );
