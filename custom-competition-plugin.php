<?php
/**
 * Plugin Name: Custom Competition Plugin
 * Description: Custom WordPress plugin for Competitions and Entries.
 * Version: 1.0
 * Author: Roshni Ahuja
 */

// Load Composer's autoloader
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Instantiate the class
$customCompetitionPlugin = new CustomCompetitionPlugin\CustomCompetitionPlugin(); //phpcs:ignore
