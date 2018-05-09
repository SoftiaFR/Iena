<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 28/03/18
 * Time: 12:08
 */

namespace block_competency_iena\task;


class sync_task_iena_roles_complete extends \core\task\scheduled_task
{
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */

    public function get_name()
    {
        return "task_iena_roles_complete";
    }


    public function execute()
    {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/competency_iena/entity/block_competency_iena_cron_roles.php');
        $cron_test = new \block_competency_iena_cron_roles();
        $cron_test->attribute_roles_iena_complete();
    }
}