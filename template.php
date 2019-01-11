<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

/**
 * Implements hook_preprocess_islandora_basic_collection_wrapper().
 */
function bootstrap_preprocess_islandora_basic_collection_wrapper(&$vars) {
  $object = $vars['islandora_object'];

  if (isset($object['MODS']) && $mods = simplexml_load_string($object['MODS']->getContent(NULL))) {
    $mods_local_identifier = (string) $mods->identifier;
    $is_student_pub = $object->id == variable_get('bc_islandora_student_pubs_pid', 'islandora:1022');

    if (empty($mods_local_identifier) && !$is_student_pub) {
      return;
    }

    $is_record_group12 = preg_match("/BC(12)-(\d{2})/", $mods_local_identifier);

    // If this is a BC12 object, $vars['student_pubs'] is TRUE.
    if ($is_record_group12 || $is_student_pub) {
      $vars['student_pubs'] = TRUE;
    }
  }
}
