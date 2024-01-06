<?php
/**
 * GW BBC
 *
 * @author    msikma https://michielsikma.com/
 * @license   MIT https://opensource.org/license/mit/
 * @mod       GW BBC
 */

if (file_exists(dirname(__FILE__).'/SSI.php') && !defined('SMF')) {
  require_once(dirname(__FILE__).'/SSI.php');
}
elseif (!defined('SMF')) {
  die('<b>GWBBC:</b> error: Installation failed. Please verify you put this file in the same place as SMF\'s SSI.php.');
}

if (SMF == 'SSI') {
  db_extend('packages');
}

$hook_functions = array(
  'integrate_theme_include' => '$sourcedir/gwbbc.subs.php',
  'integrate_bbc_codes' => 'GWBBC::addCodes',
  'integrate_bbc_buttons' => 'GWBBC::addButtons',
  'integrate_load_theme' => 'GWBBC::addStyles',
);

// Either install or uninstall.
$call = empty($context['uninstalling']) ? 'add_integration_function' : 'remove_integration_function';

foreach ($hook_functions as $hook => $function) {
  $call($hook, $function, true);
}

if (SMF == 'SSI') {
   echo '<b>GWBBC:</b> Installation successful.';
}
