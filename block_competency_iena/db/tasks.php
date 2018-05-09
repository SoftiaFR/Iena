<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 28/03/18
 * Time: 13:30
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'block_competency_iena\task\sync_task_iena_roles',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*/1',
        'month' => '*',
        'dayofweek' => '*'
    ),

    array(
        'classname' => 'block_competency_iena\task\sync_task_iena_roles_complete',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '1',
        'month' => '*/1',
        'dayofweek' => '*'
    ),

    array(
        'classname' => 'block_competency_iena\task\sync_task_iena_competency',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*/1',
        'month' => '*',
        'dayofweek' => '*'
    )
);