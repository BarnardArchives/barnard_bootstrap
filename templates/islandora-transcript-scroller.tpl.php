<?php
  unset($transcript_controls['content']['transcript_search']);
  print render($transcript_controls);
?>
<div class="transcript-container">
	<h3>Transcript</h3>
  <div class="transcript-content" id="transcript">
    <?php print render($transcript); ?>
  </div>
</div>
