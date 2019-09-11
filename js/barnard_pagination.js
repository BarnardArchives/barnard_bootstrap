/*global
    Drupal, jQuery
*/

(function ($) {
// Modifies pagination per: https://github.com/BarnardArchives/digitalcollections.barnard.edu/issues/74
// Assumes bootstrap classes are used ".pagination > {.active, .pager-first, pager-last}"
  Drupal.behaviors.barnardIslandoraPagination = {
    attach: function (context, settings) {
      const islandoraPager = $('ul.pagination'), // Grab the pagination item and hold it.
          activePage = islandoraPager.find('li.active'), // active position
          firstPage = islandoraPager.find('li.pager-first > a'), // first item
          lastPage = islandoraPager.find('li.pager-last > a'), // last item
          lastPageNumber = lastPage.length ? lastPage.attr('href').match(/.*page=(\d*)/i)[1] : 0; // greatest possible page number
      // lastPageNumber = $.urlParams(lastPage.attr('href')).page; // greatest possible page number - this requires a newer version of jQuery!


      if (activePage.text() > 3) {
        firstPage.text('1'); // set the first page text to the number 1.
        firstPage.parent().insertAfter(islandoraPager.find('li.prev')); // move the item forward
      } else {
        firstPage.hide(); // otherwise we don't need it
      }

      if (activePage.text() === '4') {
        firstPage.parent().next().hide();
      }

      if (activePage.text() < (lastPageNumber - 1)) {
        lastPage.text(Number(lastPageNumber) + 1); // set the last page to the last page number
        lastPage.parent().insertBefore(islandoraPager.find('li.next')); // move the item backward
      } else {
        lastPage.hide(); // otherwise we do not need it.
      }
    }
  };
})( jQuery );