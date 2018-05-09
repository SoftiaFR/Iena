<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 29/03/18
 * Time: 13:58
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('base_mahara', get_string('base_mahara','block_mahara_iena' ),
        null, "http://192.168.10.65/"));

    $settings->add(new admin_setting_configtext('wstoken', get_string('token_mahara','block_mahara_iena' ),
        null, "4b166d2e71428228cb610d50c442c0d6"));

    $settings->add(new admin_setting_configtext('instution_mahara', get_string('instituion_mahara','block_mahara_iena' ),
        null, "maharamoodle"));
}