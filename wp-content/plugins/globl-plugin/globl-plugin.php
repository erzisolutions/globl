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
    add_role('team_lead', 'Team Lead', [
        'read' => true,
        'edit_posts' => true,
        'manage_globl_projects' => true,
    ]);
    add_role('agent', 'Agent', [
        'read' => true,
    ]);
    add_role('project_manager', 'Project Manager', [
        'read' => true,
        'edit_posts' => true,
        'manage_globl_projects' => true,
        'manage_globl_workflows' => true,
    ]);
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
    add_submenu_page(
        'globl-workforce',
        'Workflow Builder',
        'Workflow Builder',
        'manage_options',
        'globl-workflow-builder',
        'globl_workflow_builder_page'
    );
    add_submenu_page(
        'globl-workforce',
        'User Settings',
        'User Settings',
        'manage_options',
        'globl-user-settings',
        'globl_user_settings_page'
    );
}
add_action('admin_menu', 'globl_admin_menu');

function globl_workforce_page() {
    if (isset($_POST['globl_create_project'])) {
        wp_insert_post([
            'post_title'   => sanitize_text_field($_POST['project_title']),
            'post_content' => sanitize_textarea_field($_POST['project_content']),
            'post_type'    => 'globl_project',
            'post_status'  => 'publish'
        ]);
        echo '<div class="updated notice"><p>Project created.</p></div>';
    }
    echo '<div class="wrap"><h1>Globl Workforce Management</h1>';
    echo '<form method="post"><h2>Create Project</h2>';
    echo '<input type="text" name="project_title" placeholder="Project Title" required><br><br>';
    echo '<textarea name="project_content" placeholder="Project Details" rows="4" cols="50"></textarea><br><br>';
    echo '<input type="submit" name="globl_create_project" class="button button-primary" value="Create Project">';
    echo '</form>';
    echo '<p>Use the Workflow Builder submenu to design complex workflows that leave Formidable in the dust.</p></div>';
}

// Simple shortcode for stats placeholder.
function globl_stats_shortcode() {
    return '<div id="globl-stats">Loading stats...</div><script>setTimeout(()=>{document.getElementById("globl-stats").innerText="Sample Stats"},1000);</script>';
}
add_shortcode('globl_stats', 'globl_stats_shortcode');
// Register custom post type for projects
function globl_register_project_cpt() {
    $args = [
        'public' => true,
        'label'  => 'Projects',
        'supports' => ['title', 'editor']
    ];
    register_post_type('globl_project', $args);
}
add_action('init', 'globl_register_project_cpt');

// Register custom post type for workflows
function globl_register_workflow_cpt() {
    $args = [
        'public' => true,
        'label'  => 'Workflows',
        'supports' => ['title']
    ];
    register_post_type('globl_workflow', $args);
}
add_action('init', 'globl_register_workflow_cpt');

// Workflow builder admin page
function globl_workflow_builder_page() {
    echo '<div class="wrap"><h1>Create Workflow</h1>';
    echo '<form method="post" action="" id="globl-workflow-form">';
    echo '<input type="text" name="workflow_title" placeholder="Workflow Title" required><br><br>';
    echo '<div id="steps"><input type="text" name="steps[]" placeholder="Step description"></div>';
    echo '<button id="add-step" class="button">Add Step</button><br><br>';
    echo '<input type="submit" name="globl_save_workflow" class="button button-primary" value="Save Workflow">';
    echo '</form></div>';
    echo '<script>document.getElementById("add-step").addEventListener("click", function(e){e.preventDefault();var div = document.createElement("div");div.innerHTML="<input type=\"text\" name=\"steps[]\" placeholder=\"Step description\">";document.getElementById("steps").appendChild(div);});</script>';
}

function globl_user_settings_page() {
    if (isset($_POST['globl_save_user_settings']) && check_admin_referer('globl_user_settings')) {
        foreach ((array)$_POST['user_role'] as $user_id => $role) {
            $user = get_user_by('ID', intval($user_id));
            if ($user && $role) {
                $user->set_role(sanitize_text_field($role));
            }
        }
        echo '<div class="updated notice"><p>User roles updated.</p></div>';
    }

    $roles = [
        'team_lead' => 'Team Lead',
        'agent' => 'Agent',
        'project_manager' => 'Project Manager',
        'administrator' => 'Administrator',
    ];
    $users = get_users();
    echo '<div class="wrap"><h1>User Settings</h1><form method="post">';
    wp_nonce_field('globl_user_settings');
    echo '<table class="widefat"><thead><tr><th>User</th><th>Role</th></tr></thead><tbody>';
    foreach ($users as $user) {
        $current = $user->roles ? $user->roles[0] : '';
        echo '<tr><td>' . esc_html($user->display_name) . '</td><td><select name="user_role[' . intval($user->ID) . ']">';
        foreach ($roles as $role_key => $role_name) {
            $selected = $current === $role_key ? 'selected' : '';
            echo '<option value="' . esc_attr($role_key) . '" ' . $selected . '>' . esc_html($role_name) . '</option>';
        }
        echo '</select></td></tr>';
    }
    echo '</tbody></table><p><input type="submit" name="globl_save_user_settings" class="button button-primary" value="Save Changes"></p></form></div>';
}

if (isset($_POST['globl_save_workflow'])) {
    $workflow_id = wp_insert_post([
        'post_title' => sanitize_text_field($_POST['workflow_title']),
        'post_type' => 'globl_workflow',
        'post_status' => 'publish'
    ]);
    if ($workflow_id && !empty($_POST['steps'])) {
        update_post_meta($workflow_id, 'steps', array_map('sanitize_text_field', $_POST['steps']));
    }
    echo '<div class="updated notice"><p>Workflow saved.</p></div>';
}
?>
