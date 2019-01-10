<?php

/**
 * @file
 * islandora-basic-collection-wrapper.tpl.php
 *
 * @TODO: needs documentation about file and variables
 */
?>

<div class="islandora-basic-collection-wrapper">
  <?php if (!empty($dc_array['dc:description']['value'])): ?>
    <p><?php print $dc_array['dc:description']['value']; ?></p>
  <?php endif; ?>
  <div class="islandora-basic-collection clearfix<?php if (isset($student_pubs) && $student_pubs) print ' student-pubs'; ?>">
    <?php if (!isset($student_pubs) || !$student_pubs): ?>
      <span class="islandora-basic-collection-display-switch">
        <ul class="links inline">
          <?php foreach ($view_links as $link): ?>
            <li>
              <a <?php print drupal_attributes($link['attributes']) ?>><?php print filter_xss($link['title']) ?></a>
            </li>
          <?php endforeach ?>
        </ul>
      </span>
    <?php endif; ?>
    <?php print $collection_pager; ?>
    <?php print $collection_content; ?>
    <?php print $collection_pager; ?>
  </div>
</div>
