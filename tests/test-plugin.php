<?php
/**
 * Test plugin is installed
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://bplus.mx
 * @since      1.0.0
 *
 * @package    Bplus_Framework
 */

/**
 * Test plugin installation.
 *
 * We only test if plugin is installed.
 *
 * @package    Bplus_Framework
 * @subpackage Bplus_Framework/tests
 * @author     Luis Abarca <luis@bplus.mx>
 */
class PluginTest extends WP_UnitTestCase {

  // Check that that activation doesn't break
  function test_plugin_activated() {
    $this->assertTrue( is_plugin_active( PLUGIN_PATH ) );
  }
}
