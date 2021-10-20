<?php
require_once(__DIR__ . '/class.ilXapiDelCron.php');

use \ILIAS\DI\Container;
/**
 * Class ilxapidelPlugin
 *
 * @author      Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @author      Stefan Schneider
 */
class ilXapiDelPlugin extends ilCronHookPlugin {

	const PLUGIN_ID = "xapidel";
	const PLUGIN_NAME = "XapiDel";
	const PLUGIN_CLASS_NAME = ilXapiDelPlugin::class;

	CONST DB_XXCF_OBJ = 'xxcf_data_settings';
    CONST DB_XXCF_USERS = 'xxcf_users';

    CONST DB_DEL_OBJ = 'crnhk_xapidel_object';
	CONST DB_DEL_USERS = 'crnhk_xapidel_user';
	
	private $deleteToTrash; // ToDo: config param

    /** @var Container $dic */
    private $dic;

    /** @var ilDBInterface $db */
    private $db;
	
	/**
	 * @inheritdoc
	 */
	public function __construct() {
		parent::__construct();
		global $DIC; /** @var Container $DIC */

		$this->dic = $DIC;
		$this->db = $this->dic->database();
		$this->deleteToTrash = false;
	}
	
	
	/**
	 * @inheritdoc
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}
	
	
	/**
	 * @inheritdoc
	 */
	public function getCronJobInstances() {
		return [
			new ilXapiDelCron()
		];
	}
	
	
	/**
	 * @inheritdoc
	 */
	public function getCronJobInstance($a_job_id)
	{
		switch ($a_job_id)
		{
			case ilXapiDelCron::JOB_ID:
				return new ilxapidelCron();

			default:
				return null;
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()
	{
		// Nothing to delete
	}
    
    
        /**
     * @param string $component
     * @param string $event
     * @param array  $parameters
     */
    public function handleEvent($component, $event, $parameters) {
		global $DIC; /** @var Container $DIC */
		ilLoggerFactory::getRootLogger()->alert("Event: " . $event);
		#echo '<pre>'; var_dump([$component, $event, $parameters]); exit;
        # 1 toTrash
        # 2 beforeDeletion
		//if( $component === 'Services/Object' && $event === 'toTrash' ) { //delete
        if( $component === 'Services/Object') {
			$delete = (($this->deleteToTrash && $event === 'toTrash') || (!$this->deleteToTrash && $event == 'beforeDeletion')) ? true : false;
			ilLoggerFactory::getRootLogger()->alert("Delete: " . $delete);
			if ($delete == true) {
				require_once(__DIR__ . '/class.ilXapiDelModel.php');
				$model = ilXapiDelModel::init();
				$objId = (!$this->deleteToTrash) ? (int) $parameters['object']->getId() : (int) $parameters['obj_id']; 
				$xapiObject = $model->getXapiObjectData($objId);
				if( !is_null($xapiObject) ) {
					// add obj as deleted
					$model->setXapiObjAsDeleted($objId, $xapiObject['type_id'], $xapiObject['activity_id']);
				}
			}
        }

        # USER DELETION
        if( $component === 'Services/User' && $event === 'deleteUser' ) {
            $usr_id = $parameters['usr_id'];
            require_once(__DIR__ . '/class.ilXapiDelModel.php');
            $model = ilXapiDelModel::init();

            // null or array with objIds, if are going to need more
            $xapiObjUser = $model->getXapiObjIdForUser($usr_id);
            if( !is_null($xapiObjUser) ) {
                // add user as deleted
                $model->setXapiUserAsDeleted($usr_id);
            }
        }
    }

    protected function afterUninstall()
    {
        global $DIC;
        $ilDB = $DIC->database();

        if( $ilDB->tableExists('crnhk_xapidel_user') ) {
            $ilDB->dropTable('crnhk_xapidel_user');
        }
        if( $ilDB->tableExists('crnhk_xapidel_object') ) {
            $ilDB->dropTable('crnhk_xapidel_object');
        }
    }
}
