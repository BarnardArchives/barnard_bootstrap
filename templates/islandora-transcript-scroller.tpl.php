<?php
  unset($transcript_controls['content']['transcript_search']);
  print render($transcript_controls);
?>
<div class="transcript-container">
	<h3>Transcript</h3>
  <div class="transcript-content" id="transcript">
    <?php if (empty($transcript['contents']['tcu_list'])): ?>
      This transcript has not been corrected; you may download an automated, uncorrected transcript by clicking on the link above.
    <?php else: ?>
      <?php print render($transcript); ?>
    <?php endif; ?>
  </div>
</div>
