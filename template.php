<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

// for devel
// function barnard_bootstrap_block_view_alter(&$data, $block) {
//   kpr($block);
// }

// function barnard_bootstrap_form_alter(&$form, &$form_state, $form_id) {
//   kpr($form);
// }

// function barnard_bootstrap_menu_link(array $variables) {
//   kpr($variables);
// }

/**
 * Implements hook_preprocess_page().
 */
function barnard_bootstrap_preprocess_page(&$vars) {
  // Create new theme hook suggestion specific to nodes. This is used by Solr content type.
  if (isset($vars['node'])) {
    $node = $vars['node'];
    $vars['theme_hook_suggestions'][] = 'page__node__' . $node->type;
  }

  // Navigation Simple Search
  $simple_search_form = drupal_render(drupal_get_form('islandora_solr_simple_search_form'));
  $vars['simple_search_box'] = $simple_search_form;

  // If we have bc_islandora and this is the front page, invoke
  // _bc_islandora_featured() and set  $vars['page']['footer']['front_caption'].
  if (module_exists('bc_islandora') && $vars['is_front']) {
    module_load_include('inc', 'bc_islandora', 'includes/theme');
    $vars['page']['footer']['front_caption'] = [
      '#markup' => _barnard_islandora_featured(),
      '#prefix' => '<div id="block-views-featured-block">',
      '#suffix' => '</div>',
    ];
  }
  // If we have bc_islandora, this is NOT the front page, and this is not a
  // search result page, call bc_islandora's custom breadcrumb theming method
  // and set $vars['bc_breadcrumb'].
  if (isset($node) && $node->type == 'islandora_solr_content_type') {
    $vars['bc_breadcrumb'] = theme('bc_islandora_breadcrumb', ['breadcrumb' => menu_get_active_breadcrumb()]);
  }
  else {
    if (module_exists('bc_islandora') && !$vars['is_front'] && arg(1) != 'search') {
      $vars['bc_breadcrumb'] = theme('bc_islandora_breadcrumb', ['breadcrumb' => []]);
    }
  }
  // If we have service_links, set $vars['socialmedia'].
  //  if (module_exists('service_links') && _service_links_match_path()) {
  //    $vars['socialmedia'] = implode('', service_links_render(NULL));
  //  }
  // If this is an islandora object, add permalink js.
  if (arg(0) == 'islandora' && arg(1) == 'object') {
    drupal_add_js(['permalink_path' => $_GET['q']], 'setting');
    drupal_add_js(drupal_get_path('theme', 'barnard_bootstrap') . '/js/permalink.js');
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function barnard_bootstrap_form_islandora_solr_date_filter_form_alter(&$form, &$form_state, $form_id) {
  // Auto-epxand the date form.
  $form['date_range_expand']['#markup'] = '<span class="toggle-date-range-filter date-range-expanded"></span>';
  // Set the size.
  $size = 4;
  $form['date_filter']['date_filter_from']['#size'] = $size;
  $form['date_filter']['date_filter_to']['#size'] = $size;
  // Modify the suffix text.
  $form['date_filter']['date_filter_from']['#suffix'] = '<span id="between-dates"> to </span>';
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function barnard_bootstrap_form_islandora_solr_range_slider_form_alter(&$form, &$form_state, $form_id) {
  // Disable access to the slider and all related items.
  $disable_slider = TRUE;

  if ($disable_slider) {
    $form['markup']['#access'] = !$disable_slider;
    $form['range_slider_submit']['#access'] = !$disable_slider;
    drupal_add_js(drupal_get_path('theme', 'barnard_bootstrap') . '/js/date_slider.js', 
      array(
        'type' => 'file',
        'weight' => 5,
      ));
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function barnard_bootstrap_form_islandora_solr_simple_search_form_alter(&$form, &$form_state) {
  // Control form size attrib with CSS width.
  unset($form['simple']['islandora_simple_search_query']['#size']);
  // Add placeholder text.
  $form['simple']['islandora_simple_search_query']['#attributes']['placeholder'] = "Search everything by name, keyword, etc.";
}

/**
 * Implements hook_block_view_MODULE_DELTA_alter().
 */
function barnard_bootstrap_block_view_islandora_solr_display_switch_alter(&$data, $block) {
  $findArray = array('display-bcislandora-list', 'display-grid',
    'A simple output.', 'List', 'Grid', 'active');
  $replaceArray = array('btn glyphicon glyphicon-th-list display-bcislandora-list', 'btn glyphicon glyphicon-th-large display-grid',
    'Display search results as a list view', '', '', 'active disabled');
  $data = str_replace($findArray, $replaceArray, $data);
}

/**
 * Returns HTML for an islandora_solr_facet_wrapper.
 *
 * @param array $variables
 *   An associative array containing:
 *   - title: A string to use as the header/title.
 *   - content: A string containing the content being wrapped.
 *
 * @ingroup themeable
 */
function barnard_bootstrap_islandora_solr_facet_wrapper($variables) {
  $output = '<div class="islandora-solr-facet-wrapper">';
  if ($variables['pid'] === "mods_originInfo_dateCreated_sort") {
    $output .= '<h3 class="facet-heading"><a class="facet-toggle" data-toggle="collapse" href="#' . $variables['pid'] . '" aria-expanded="true">' . $variables['title'] . '</a></h3>';
    $output .= '<div class="collapse in" id="' . $variables['pid'] . '" aria-expanded="true">' . $variables['content'] . '</div>';
  } else {
    $output .= '<h3 class="facet-heading"><a class="facet-toggle collapsed" data-toggle="collapse" href="#'. $variables['pid'] . '" aria-expanded="false">' . $variables['title'] . '</a></h3>';
    $output .= '<div class="collapse" id="' . $variables['pid'] . '" aria-expanded="false">' . $variables['content'] . '</div>';
  }
  $output .= '</div>';
  return $output;
}

// ->>> Old Below:

/**
 * Exception.
 *
 * @param string $pid
 *   something to compare.
 *
 * @return string mode
 *   IAB mode.
 */
function _barnard_bootstrap_breadcrumb_view_exceptions($pid) {
  $thumbs = explode(', ', variable_get('bc_islandora_bookreader_initial_thumbs'));
  $one_ups = explode(', ', variable_get('bc_islandora_bookreader_initial_1up'));
  $ns = islandora_get_namespace($pid);
  if (in_array($pid, $thumbs) || in_array($ns, $thumbs)) {
    return 'thumb';
  }
  elseif (strpos($ns, 'BC15-02') !== FALSE || in_array($pid, $one_ups) || in_array($ns, $one_ups)) {
    return '1up';
  }
  return '2up';
}



/**
 * Implements hook_preprocess_islandora_basic_collection_wrapper().
 */
function barnard_bootstrap_preprocess_islandora_basic_collection_wrapper(&$vars) {
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

/**
 * Implements hook_preprocess_islandora_book_book().
 */
function barnard_bootstrap_preprocess_islandora_book_book(&$vars) {
  // Barnard Core Module.
  if (!module_exists('bc_islandora')) {
    return;
  }
  module_load_include('inc', 'bc_islandora', 'includes/theme');
  $object = $vars['object'];
  // Provide a link to this object's PDF datastream via $vars['dl_links'].
  $vars['dl_links'] = _barnard_islandora_dl_links($object, ['PDF']);
  drupal_add_js(libraries_get_path('openseadragon') . '/openseadragon.js');
  $vars['viewer'] = theme('bc_islandora_newspaper_issue_navigator', ['object' => $object]);
}

/**
 * Implements hook_preprocess_islandora_book_page().
 */
function barnard_bootstrap_preprocess_islandora_book_page(&$vars) {
  // Barnard Core Module.
  if (!module_exists('bc_islandora')) {
    return;
  }
  $object = $vars['object'];
  module_load_include('inc', 'bc_islandora', 'includes/theme');
  // Provide a link to this object's JPG datastream via $vars['dl_links'].
  $vars['dl_links'] = _barnard_islandora_dl_links($object, ['JPG']);
}

/**
 * Implements hook_preprocess_islandora_large_image().
 */
function barnard_bootstrap_preprocess_islandora_large_image(&$vars) {
  // Barnard Core Module.
  if (!module_exists('bc_islandora')) {
    return;
  }
  module_load_include('inc', 'bc_islandora', 'includes/theme');
  // Provide a link to this object's JPG datastream via $vars['dl_links'].
  $vars['dl_links'] = _barnard_islandora_dl_links($vars['islandora_object'], ['JPG']);
}

/**
 * Implements hook_preprocess_islandora_book_book().
 */
function barnard_bootstrap_preprocess_islandora_oralhistories(&$vars) {
  // Barnard Core Module.
  if (!module_exists('bc_islandora')) {
    return;
  }
  module_load_include('inc', 'bc_islandora', 'includes/theme');
  $object = $vars['object'];

  // Provide a link to this object's transcript datastream via $vars['dl_links'].
  $vars['dl_links'] = _barnard_islandora_dl_links($object, ['TRANSCRIPT']);
}

/**
 * Implements hook_preprocess_islandora_manuscript_manuscript().
 */
function barnard_bootstrap_preprocess_islandora_manuscript_manuscript(&$vars) {
  module_load_include('inc', 'islandora_paged_content', 'includes/utilities');
  module_load_include('inc', 'islandora', 'includes/metadata');
  drupal_add_js('misc/form.js');
  drupal_add_js('misc/collapse.js');
  drupal_add_js(drupal_get_path('theme', 'barnard_bootstrap') . '/js/manuscript.js');
  $object = $vars['object'];
  $vars['metadata'] = islandora_retrieve_metadata_markup($object);
  $vars['description'] = islandora_retrieve_description_markup($object);
  $pages_ocr = [];
  $pages_hocr = [];
  if ($pages = islandora_paged_content_get_pages($object)) {
    $i = 1;
    foreach ($pages as $pid => $page) {
      if ($page_obj = islandora_object_load($pid)) {
        if (isset($page_obj['OCR'])) {
          $page_ocr = $page_obj['OCR']->getContent(NULL);
          $page_grafs = explode("\n\n", $page_ocr);
          $new_grafs = [];
          foreach ($page_grafs as $i => $graf) {
            $new_grafs[$i] = preg_replace("/\n/", ' ', $graf);
          }
          $pages_ocr[] = implode("\n\n", $new_grafs);
        }
      }
    }
  }
  if (!empty($pages_ocr)) {
    $vars['ms_transcript'] = $pages_ocr;
  }
  // Barnard Core Module.
  if (!module_exists('bc_islandora')) {
    return;
  }
  module_load_include('inc', 'bc_islandora', 'includes/theme');
  $vars['dl_links'] = _barnard_islandora_dl_links($object, ['PDF', 'TRANSCRIPT']);
  if (count(islandora_paged_content_get_pages($object)) > 1) {
    $vars['ms_pager'] = _barnard_islandora_np_page_pager($object);
  }
}

/**
 * Implements hook_preprocess_islandora_manuscript_page().
 */
function barnard_bootstrap_preprocess_islandora_manuscript_page(&$vars) {
  module_load_include('inc', 'islandora_paged_content', 'includes/utilities');
  $object = $vars['object'];
  if (isset($object['OCR'])) {
    $vars['ms_transcript'] = $object['OCR']->getContent(NULL);
    drupal_add_js(drupal_get_path('theme', 'barnard_bootstrap') . '/js/manuscript.js');
  }
  // Barnard Core Module.
  if (!module_exists('bc_islandora')) {
    return;
  }
  module_load_include('inc', 'bc_islandora', 'includes/theme');
  $vars['dl_links'] = _barnard_islandora_dl_links($object, ['TRANSCRIPT']);
  $vars['ms_pager'] = _barnard_islandora_np_page_pager($object);
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 *
 * Replaces the url for the search result to be the book's url, not the page.
 * The page is added as a fragment at the end of the book url.
 */
function barnard_bootstrap_islandora_compoundcmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
  $parent_book_field_name = variable_get('islandora_book_parent_book_solr_field', 'RELS_EXT_isMemberOf_uri_ms');
  $book_pid = preg_replace('/info\:fedora\//', '', $search_results['solr_doc'][$parent_book_field_name][0], 1);
  $field_match = [
    'catch_all_fields_mt',
    'OCR_t',
    'text_nodes_HOCR_hlt',
  ];
  $field_term = '';
  $fields = preg_split('/OR|AND|NOT/', $query_processor->solrQuery);
  foreach ($fields as $field) {
    if (preg_match('/^(.*):\((.*)\)/', $field, $matches)) {
      if (isset($matches[1]) && in_array($matches[1], $field_match)) {
        $field_term = ((isset($matches[2]) && $matches[2]) ? $matches[2] : '');
        break;
      }
    }
  }
  if ($field_term) {
    $search_term = trim($field_term);
  }
  elseif ($query_processor->solrDefType == 'dismax' || $query_processor->solrDefType == 'edismax') {
    $search_term = trim($query_processor->solrQuery);
  }
  $search_results['object_url_fragment'] = "page/1/mode/1up";
  if (!empty($search_term)) {
    $search_results['object_url_fragment'] .= "/search/" . rawurlencode($search_term);
  }
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 */
function barnard_bootstrap_islandora_manuscriptpagecmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
  $search_results['object_url_params']['solr'] = [
    'query' => $query_processor->solrQuery,
    'params' => $query_processor->solrParams,
  ];
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 */
function barnard_bootstrap_islandora_newspaperpagecmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
  $field_match = [
    'catch_all_fields_mt',
    'OCR_t',
    'text_nodes_HOCR_hlt',
  ];
  $field_term = '';
  $fields = preg_split('/OR|AND|NOT/', $query_processor->solrQuery);
  foreach ($fields as $field) {
    if (preg_match('/^(.*):\((.*)\)/', $field, $matches)) {
      if (isset($matches[1]) && in_array($matches[1], $field_match)) {
        $field_term = ((isset($matches[2]) && $matches[2]) ? $matches[2] : '');
        break;
      }
    }
  }
  if ($field_term) {
    $search_term = trim($field_term);
    $search_results['object_url_params']['solr'] = [
      'query' => $search_term,
      'params' => ['defType' => 'dismax'],
    ];
  }
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 *
 * Add page viewing fragment and search term to show all search results within
 * book on page load.
 */
function barnard_bootstrap_islandora_bookcmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
  $parent_book_field_name = variable_get('islandora_book_parent_book_solr_field', 'RELS_EXT_isMemberOf_uri_ms');
  $book_pid = preg_replace('/info\:fedora\//', '', $search_results['solr_doc'][$parent_book_field_name][0], 1);
  $field_match = [
    'catch_all_fields_mt',
    'OCR_t',
    'text_nodes_HOCR_hlt',
  ];
  $field_term = '';
  $fields = preg_split('/OR|AND|NOT/', $query_processor->solrQuery);
  foreach ($fields as $field) {
    if (preg_match('/^(.*):\((.*)\)/', $field, $matches)) {
      if (isset($matches[1]) && in_array($matches[1], $field_match)) {
        $field_term = ((isset($matches[2]) && $matches[2]) ? $matches[2] : '');
        break;
      }
    }
  }
  if ($field_term) {
    $search_term = trim($field_term);
  }
  elseif ($query_processor->solrDefType == 'dismax' || $query_processor->solrDefType == 'edismax') {
    $search_term = trim($query_processor->solrQuery);
  }
  $mode = _barnard_bootstrap_breadcrumb_view_exceptions($book_pid);
  $search_results['object_url_fragment'] = "page/1/mode/$mode";
  if (!empty($search_term)) {
    $search_results['object_url_fragment'] .= "/search/" . rawurlencode($search_term);
  }
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 *
 * Replaces the url for the search result to be the book's url, not the page.
 * The page is added as a fragment at the end of the book url.
 */
function barnard_bootstrap_islandora_pagecmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
  $parent_book_field_name = variable_get('islandora_book_parent_book_solr_field', 'RELS_EXT_isMemberOf_uri_ms');
  $page_number_field_name = variable_get('islandora_paged_content_page_number_solr_field', 'RELS_EXT_isSequenceNumber_literal_ms');
  // @TODO: what is wrong here, as well?  dev is acting odd using the literal.  What.
  if (!isset($search_results['solr_doc'][$page_number_field_name])) {
    // Fall back to ms if you cannot get the page number from literal.
    $page_number_field_name = 'RELS_EXT_isSequenceNumber_uri_ms';
  }
  if (empty($search_results['object_url']) || empty($search_results['solr_doc'])) {
    drupal_set_message('Received an invalid or broken solr_search_results.', 'warning', FALSE);
    return;
  }
  // If:
  // the solr doc contains the parent book AND
  // the solr doc contains the page number...
  if (isset($search_results['solr_doc'][$parent_book_field_name]) &&
    count($search_results['solr_doc'][$parent_book_field_name]) &&
    isset($search_results['solr_doc'][$page_number_field_name]) &&
    count($search_results['solr_doc'][$page_number_field_name])) {
    // Replace the result url with that of the parent book and add the page
    // number as a fragment.
    $book_pid = preg_replace('/info\:fedora\//', '', $search_results['solr_doc'][$parent_book_field_name][0], 1);
    // Waste of time (depending on where the page number comes from).
    $page_number = preg_replace('/info\:fedora\//', '', $search_results['solr_doc'][$page_number_field_name][0], 1);
    if (islandora_object_access(ISLANDORA_VIEW_OBJECTS, islandora_object_load($book_pid))) {
      $search_results['object_url'] = "islandora/object/$book_pid";
      $mode = _barnard_bootstrap_breadcrumb_view_exceptions($book_pid);
      $search_results['object_url_fragment'] = "page/$page_number/mode/$mode";
      $field_match = [
        'catch_all_fields_mt',
        'OCR_t',
        'text_nodes_HOCR_hlt',
      ];
      $field_term = '';
      $fields = preg_split('/OR|AND|NOT/', $query_processor->solrQuery);
      foreach ($fields as $field) {
        if (preg_match('/^(.*):\((.*)\)/', $field, $matches)) {
          if (isset($matches[1]) && in_array($matches[1], $field_match)) {
            $field_term = ((isset($matches[2]) && $matches[2]) ? $matches[2] : '');
            break;
          }
        }
      }
      if ($field_term) {
        $search_term = trim($field_term);
      }
      elseif ($query_processor->solrDefType == 'dismax' || $query_processor->solrDefType == 'edismax') {
        $search_term = trim($query_processor->solrQuery);
      }
      if (!empty($search_term)) {
        $search_results['object_url_fragment'] .= "/search/" . rawurlencode($search_term);
      }
    }
  }
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 */
function barnard_bootstrap_islandora_sp_large_image_cmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
  $search_results['object_url_params']['solr'] = [
    'query' => $query_processor->solrQuery,
    'params' => $query_processor->solrParams,
  ];
}

/**
 * Implements hook_CMODEL_PID_islandora_solr_object_result_alter().
 *
 * Replaces the url for the search result to be the book's url, not the page.
 * The page is added as a fragment at the end of the book url.
 */
// function barnard_bootstrap_islandora_pagecmodel_islandora_solr_object_result_alter(&$search_results, $query_processor) {
//  // Grab the names of the appropriate solr fields from the db.
//  $parent_book_field_name = variable_get('islandora_book_parent_book_solr_field', 'RELS_EXT_isMemberOf_uri_ms');
//  $page_number_field_name = variable_get('islandora_paged_content_page_number_solr_field', 'RELS_EXT_isSequenceNumber_literal_ms');
//  // @TODO: what is wrong here, as well?  dev is acting odd using the literal.  What.
//  if (!isset($search_results['solr_doc'][$page_number_field_name])) {
//    // Fall back to ms if you cannot get the page number from literal.
//    $page_number_field_name = 'RELS_EXT_isSequenceNumber_uri_ms';
//  }
//  if (isset($search_results['solr_doc'][$parent_book_field_name]) &&
//    count($search_results['solr_doc'][$parent_book_field_name]) &&
//    isset($search_results['solr_doc'][$page_number_field_name]) &&
//    count($search_results['solr_doc'][$page_number_field_name])) {

//    // Replace the result url with that of the parent book and add the page
//    // number as a fragment.
//    $book_pid = preg_replace('/info\:fedora\//', '', $search_results['solr_doc'][$parent_book_field_name][0], 1);
//    // Waste of time (depending on where the page number comes from).
//    $page_number = preg_replace('/info\:fedora\//', '', $search_results['solr_doc'][$page_number_field_name][0], 1);
//    $search_term = trim($query_processor->solrQuery);
//    // @TODO COME BACK HERE!
//    if (islandora_object_access(ISLANDORA_VIEW_OBJECTS, islandora_object_load($book_pid))) {
//      $extra_life = strpos($book_pid, 'BC15') !== FALSE;
//      $mode = $extra_life ? '1up' : '2up';
//      $search_results['object_url'] = "islandora/object/$book_pid";
//      $search_results['object_url_fragment'] = "page/$page_number/mode/$mode";

//      // XXX: Won't handle fielded searches nicely... then again, if our
//      // highlighting field is not the one being search on, this makes sense?
//      if ($query_processor->solrDefType == 'dismax' || $query_processor->solrDefType == 'edismax') {
//        $search_results['object_url_fragment'] .= "/search/" . rawurlencode($query_processor->solrQuery);
//      }
//    }
//  }
// }
