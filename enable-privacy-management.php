<?php
/**
 * Plugin Name: Enable Privacy Management for Admins
 * Description: Allows regular administrators in a multisite to edit privacy policy pages
 * Version: 1.1
 * Author: Macbay Digital
 */

// Fügt die manage_privacy_options Capability zu Administrator-Rollen hinzu
function mb_add_privacy_cap_to_admin() {
    // Nur in Multisite ausführen
    if (is_multisite()) {
        // Administrator-Rolle abrufen
        $role = get_role('administrator');
        
        // Capability hinzufügen, wenn die Rolle existiert
        if ($role && !$role->has_cap('manage_privacy_options')) {
            $role->add_cap('manage_privacy_options', true);
        }
    }
}

// Funktion ausführen, sobald Plugins geladen sind
add_action('plugins_loaded', 'mb_add_privacy_cap_to_admin');

// Zusätzlich die Capability bei der Initialisierung des Admin-Bereichs prüfen und hinzufügen
add_action('admin_init', 'mb_add_privacy_cap_to_admin');

// Filter hinzufügen, um sicherzustellen, dass die Capability-Prüfung korrekt funktioniert
function mb_map_meta_cap_for_privacy($caps, $cap, $user_id, $args) {
    // Wenn es sich um die Überprüfung der Privacy-Capability handelt
    if ('manage_privacy_options' === $cap) {
        // Prüfen, ob der Benutzer ein Administrator ist
        if (is_super_admin($user_id) || user_can($user_id, 'administrator')) {
            // Ersetzen Sie die erforderlichen Capabilities durch eine, die Administratoren haben
            return array('manage_options');
        }
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'mb_map_meta_cap_for_privacy', 10, 4);
