<?php
/**
 * GW BBC
 *
 * @author    msikma https://michielsikma.com/
 * @license   MIT https://opensource.org/license/mit/
 * @mod       GW BBC
 */

require_once("$sourcedir/gwbbc.vendor/autoload.php");

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;

// Set up a standard HTML sanitizer.
// See <https://wicg.github.io/sanitizer-api/> for more information.
global $sanitizer;
$sanitizer = new HtmlSanitizer((new HtmlSanitizerConfig())
  // Allow all elements that can't be abused (i.e. no <script>, no <style>, etc.)
  ->allowSafeElements()
  // Enforces all links to be untrusted.
  ->forceAttribute('a', 'rel', 'noopener noreferrer')
);

/**
 * Insert a new BBC button after another specific button.
 */
function insert_bbc_button_after(&$tags, $new_tag, $target, $add_separator_before = false, $add_separator_after = false) {
  foreach ($tags as &$tags_row) {
    $new_tags = array();

    foreach ($tags_row as $tag) {
      $new_tags[] = $tag;

      if ($tag['code'] == $target) {
        if ($add_separator_before) {
          $new_tags[] = array();
        }
        $new_tags[] = $new_tag;
        if ($add_separator_after) {
          $new_tags[] = array();
        }
      }
    }

    $tags_row = $new_tags;
  }
}

/**
 * Sanitizes a given HTML string and returns the sanitized version.
 */
function sanitize_html($untrusted_html) {
  global $sanitizer;
  return $sanitizer->sanitizeFor('div', $untrusted_html);
}
