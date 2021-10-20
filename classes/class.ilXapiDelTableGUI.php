<?php
/**
 * Copyright (c) 2018 internetlehrer-gmbh.de
 * GPLv2, see LICENSE 
 */

use ILIAS\DI\Container;

/**
 * xapidel plugin: content types table GUI
 *
 * @author Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @version $Id$
 */ 
class ilXapiDelTableGUI extends ilTable2GUI
{
    /** @var ilXapiDelPlugin $plugin_object */
    protected $plugin_object;

    /** @var Container $dic */
    private $dic;
    /**
     * @var ilDBInterface
     */
    private $db;


    /**
     * Constructor
     *
     * @param object        parent object
     * @param string $a_parent_cmd
     * @param string $a_template_context
     * @throws ilPluginException
     */
    function __construct($a_parent_obj, $a_parent_cmd = '', $a_template_context = '') 
    {
    	// this uses the cached plugin object
		$this->plugin_object = ilPlugin::getPluginObject(IL_COMP_SERVICE, 'Cron', 'crnhk', 'XapiDel');
		parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);

		global $DIC; /** @var Container $DIC */

		$this->dic = $DIC;
		$this->db = $this->dic->database();

    }

    /**
     * Init the table with some configuration
     *
     *
     * @access public
     * @param $a_parent_obj
     */
    public function init($a_parent_obj) 
    {
        global $ilCtrl, $lng;

        $this->addColumn($lng->txt('object_id'), 'obj_id', '10%');
        $this->addColumn($this->plugin_object->txt('type_id'), 'type_id', '10%');
        $this->addColumn($this->plugin_object->txt('act_id'), 'act_id');
        $this->addColumn($this->plugin_object->txt('user_ident'), 'user_ident');
        $this->addColumn($this->plugin_object->txt('date_added'), 'added', '20%');
#$this->addColumn($this->plugin_object->txt('date_job_exec'), 'exec', '20%');

        $this->setDefaultOrderField('type_id');
        $this->setDefaultOrderDirection('asc');

        $this->setEnableHeader(true);
        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj));

        $this->setRowTemplate('tpl.xapidel_row.html', $this->plugin_object->getDirectory());
        $this->getMyDataFromDb();
    }

    /**
     * Get data and put it into an array
     */
    function getMyDataFromDb() 
    {
    	$this->plugin_object->includeClass('class.ilXapiDelModel.php');
    	$model = ilXapiDelModel::init();
        $data = array_merge($model->getAllXapiDelObjectData(), $model->getXapiObjectsByDeletedUsers() );
        $this->setData($data);
    }

    /**
     * Fill a single data row.
     */
    protected function fillRow($a_set) 
    {
        global $lng, $ilCtrl;

        $ilCtrl->setParameter($this->parent_obj, 'type_id', $a_set['type_id']);

        $this->tpl->setVariable('OBJ_ID', $a_set['obj_id']);
        $this->tpl->setVariable('TYPE_ID', $a_set['type_id']);
        $this->tpl->setVariable('ACT_ID', $a_set['activity_id']);
        $this->tpl->setVariable('USR_IDENT', $a_set['usr_ident'] ? $a_set['usr_ident'] . ' (#' . $a_set['usr_id'] . ')' : '-');
        $this->tpl->setVariable('DATE_ADDED', $a_set['added']);
        #$this->tpl->setVariable('DATE_JOB_EXEC', $a_set['updated']); // currently cron job deletes the job related entry
    }

}

?>