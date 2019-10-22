/*global
    Drupal, jQuery
*/

(function ($) {
// Modifies pagination per: https://github.com/BarnardArchives/digitalcollections.barnard.edu/issues/74
// Assumes bootstrap classes are used ".pagination > {.active, .pager-first, pager-last}"
  Drupal.behaviors.barnardIslandoraPagination = {
    attach: function (context, settings) {
      const islandoraPager = $('ul.pagination'), // Grab the pagination item and hold it.
          pagesArray = islandoraPager.find('li').toArray().map( (i) => { return i.innerText }),
          firstPage = islandoraPager.find('li.pager-first > a'), // first item
          lastPage = islandoraPager.find('li.pager-last > a'), // last item
          lastPageNumber = lastPage.length ? Number(lastPage.attr('href').match(/.*page=(\d*)/i)[1]) + 1 : 0, // greatest possible page number
          ellips = $('.pager-ellipsis.disabled');
      // lastPageNumber = $.urlParams(lastPage.attr('href')).page; // greatest possible page number - this requires a newer version of jQuery!

      console.log(pagesArray);

      if (pagesArray.includes("1")) {
        firstPage.hide();
      } else {
        firstPage.text('1'); // set the first page text to the number 1.
        firstPage.parent().insertAfter(islandoraPager.find('li.prev')); // move the item forward
      }

      if (pagesArray.includes(String(lastPageNumber))) {
        lastPage.hide(); // otherwise we do not need it.
      } else {
        lastPage.text(lastPageNumber); // set the last page to the last page number
        lastPage.parent().insertBefore(islandoraPager.find('li.next')); // move the item backward
      }

      ellips.each( (e,t) => {
        let ellipsis = $(t);
        if (Number(ellipsis.prev().text()) + 1 === Number(ellipsis.next().text())) {
          ellipsis.hide();
        }
      });
      
    }
  };
})( jQuery );
