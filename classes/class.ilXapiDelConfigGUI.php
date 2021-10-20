<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

use ILIAS\DI\Container;

require_once __DIR__ . '/class.ilXapiDelCron.php';
/**
 * Class ilXapiDelConfigGUI
 *
 * @author      Uwe Kohnle <support@internetlehrer-gmbh.de>
 *
 * @package     Services/AssessmentQuestion
 */
class ilXapiDelConfigGUI extends ilPluginConfigGUI
{
	/** @var ilXapiDelPlugin $plugin_object */
	protected $plugin_object;

    /** @var Container $dic */
    private $dic;

    public function __construct()
    {
        global $DIC /** @var Container $DIC */;
        $this->dic = $DIC;
    }

    public function performCommand($cmd)
	{
        $this->initTabs();
		$this->{$cmd}();
	}


    /**
     * Init Tabs
     *
     * @param string	mode ('edit_type' or '')
     */
    function initTabs($a_mode = "")
    {
        global $ilCtrl, $ilTabs, $lng;

        $ilTabs->addTab("list",
            $this->plugin_object->txt('content_types'),
            $ilCtrl->getLinkTarget($this, 'configure')
        );
    }


    /**
     * @param ilPropertyFormGUI|null $form
     * @throws ilPluginException
     */
    protected function configure(ilPropertyFormGUI $form = null)
	{
        if( !ilPluginAdmin::isPluginActive('xxcf') ) {
            ilUtil::sendFailure($this->plugin_object->txt('not_active_xxcf'));
            return;
        }
		global $DIC; /* @var Container $DIC */

        require_once __DIR__ . '/class.ilXapiDelTableGUI.php';
        $tableGui = new ilXapiDelTableGUI($this);
        $tableGui->init($this);
        $DIC->ui()->mainTemplate()->setContent($tableGui->getHTML());
        /*
		if( $form === null )
		{
			$form = $this->buildForm();
		}


		$DIC->ui()->mainTemplate()->setContent($form->getHTML());
        */

	}
	
	protected function save()
	{
		global $DIC; /* @var Container $DIC */
		
		$form = $this->buildForm();
		
		if( !$form->checkInput() )
		{
			return $this->configure($form);
		}
		
		#$this->writeLrsTypeId($form->getInput('lrs_type_id'));
		
		$DIC->ctrl()->redirect($this, 'configure');
	}
	
	protected function buildForm()
	{
		global $DIC; /* @var Container $DIC */
		
		$form = new ilPropertyFormGUI();
		
		$form->setFormAction($DIC->ctrl()->getFormAction($this));
		$form->addCommandButton('save', $DIC->language()->txt('save'));
		
		$form->setTitle('Configuration');
		
		$item = new ilRadioGroupInputGUI('LRS-Type', 'lrs_type_id');
		$item->setRequired(true);
		
		$types = ilCmiXapiLrsTypeList::getTypesData(false);
		
		foreach ($types as $type)
		{
			$option = new ilRadioOption($type['title'], $type['type_id'], $type['description']);
			$item->addOption($option);
		}
		
		$item->setValue($this->readLrsTypeId());
		
		$form->addItem($item);
		
		return $form;
	}
	
	protected function readLrsTypeId()
	{
		$settings = new ilSetting(ilXapiDelCron::JOB_ID);
		return $settings->get('lrs_type_id', 0);
	}
	
	protected function writeLrsTypeId($lrsTypeId)
	{
		$settings = new ilSetting(ilXapiDelCron::JOB_ID);
		$settings->set('lrs_type_id', $lrsTypeId);
	}
}
