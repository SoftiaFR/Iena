<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 29/03/18
 * Time: 13:58
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('role_iena', get_string('name_role','block_competency_iena' ),
        null, "gestionnairepf"));

    $settings->add(new admin_setting_configtext('parcour_iena', get_string('name_parcour','block_competency_iena' ),
        null, "Plan dynamique"));

    $settings->add(new admin_setting_configtext('mail_iena', get_string('name_mail','block_competency_iena' ),
        null, "admin@mail.com"));

    $settings->add(new admin_setting_configtextarea('apc_iena',get_string('apc_iena','block_competency_iena' ),
        null,"APC"));

    $settings->add(new admin_setting_configtextarea('info_iena',get_string('info_iena','block_competency_iena' ),
        null,"info iena"));
}