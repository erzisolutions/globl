<?php
/**
 * Plugin Name: Globl Admin Tools
 * Description: Adds workforce management features, custom roles, and shortcodes for the Globl call center theme.
 * Version: 0.1.0
 * Author: Globl
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register custom roles.
function globl_add_roles() {
    add_role('team_lead', 'Team Lead', ['read' => true]);
    add_role('agent', 'Agent', ['read' => true]);
    add_role('project_manager', 'Project Manager', ['read' => true]);
}
register_activation_hook(__FILE__, 'globl_add_roles');

// Admin page placeholder.
function globl_admin_menu() {
    add_menu_page(
        'Globl Workforce',
        'Globl Workforce',
        'manage_options',
        'globl-workforce',
        'globl_workforce_page'
    );
}
add_action('admin_menu', 'globl_admin_menu');

function globl_workforce_page() {
    echo '<div class="wrap"><h1>Globl Workforce Management</h1><p>Coming soon: manage teams, agents, and projects.</p></div>';
}

// Simple shortcode for stats placeholder.
function globl_stats_shortcode() {
    return '<div id="globl-stats">Loading stats...</div><script>setTimeout(()=>{document.getElementById("globl-stats").innerText="Sample Stats"},1000);</script>';
}
add_shortcode('globl_stats', 'globl_stats_shortcode');
?>
