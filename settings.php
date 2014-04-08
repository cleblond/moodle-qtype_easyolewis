<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('qtype_easyolewis_options/path', get_string('easyolewis_options', 'qtype_easyolewis'),
                   get_string('configeasyolewisoptions', 'qtype_easyolewis'), '/marvin', PARAM_TEXT));
}
