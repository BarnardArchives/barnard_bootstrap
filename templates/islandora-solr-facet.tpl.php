<?php

/**
 * @file
 * Template file for default facets
 *
 * @TODO document available variables
 */
?>

<ul id="facets" class=" <?php print $classes; ?>">
  <?php foreach($buckets as $key => $bucket): ?>
    <li>
      <?php print $bucket['link']; ?>
      <span class="count">(<?php print $bucket['count']; ?>)</span>
    </li>
  <?php endforeach; ?>
</ul>
