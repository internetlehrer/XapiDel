<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/XapiCmi5/classes/class.ilXapiCmi5LrsType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/XapiCmi5/classes/XapiDelete/class.ilXapiCmi5StatementsDeleteRequest.php');
require_once __DIR__.'/class.ilXapiDelModel.php';

/**
 * Class ilXapiDelCron
 *
 * @author      Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @author      Stefan Schneider
 */
class ilXapiDelCron extends ilCronJob
{
	const JOB_ID = 'xapidel';
	
	/**
	 * @var ilXapiCmi5LrsType|null
	 */
    protected $lrsType;
    
    /**
	 * @var ilXapiDelModel
	 */
    protected $model;
	
	public function __construct()
	{
		$settings = new ilSetting(self::JOB_ID);
		$lrsTypeId = $settings->get('lrs_type_id', 0);
		
		if( $lrsTypeId )
		{
			$this->lrsType = new ilXapiCmi5LrsType($lrsTypeId);
		}
		else
		{
			$this->lrsType = null;
        }
        
        $this->model = ilXapiDelModel::init();
	}
	
	public function getId()
	{
		return self::JOB_ID;
	}
	
	/**
	 * @@inheritdoc
	 */
	public function hasAutoActivation()
	{
		return false;
	}
	
	/**
	 * @@inheritdoc
	 */
	public function hasFlexibleSchedule()
	{
		return true;
	}
	
	/**
	 * @@inheritdoc
	 */
	public function getDefaultScheduleType()
	{
		return self::SCHEDULE_TYPE_IN_MINUTES;
	}
	
	/**
	 * @@inheritdoc
	 */
	function getDefaultScheduleValue()
	{
		return 1;
	}
	
	protected function hasLrsType()
	{
		return $this->getLrsType() !== null;
	}
	
	protected function getLrsType()
	{
		return $this->lrsType;
	}
	
	/**
	 * @@inheritdoc
	 */
	public function run()
	{
        global $DIC;
        $cronResult = new ilCronJobResult();
		ilLoggerFactory::getRootLogger()->alert('run');
		
		// LRS - Ist Client gelöscht?
		// LRS - Wenn Client gelöscht dann nix machen
		// LRS - Wenn Client gelöscht wirklich alle Daten weg?
		// Wenn Objekt gelöscht warum wird es nochmal bei Nutzer aufgeführt (Tabelle gucken)
		// xxx Löschen wenn nut Lernerfahrung anzeigen dann nur anzeigen nicht löschen = kein Datenadmin

        /*
        Fall 1: 
            * Objekt deleted (in Tabelle xapidel_object eingetragen mit Feld updated=null)
        => xapidel_object aktualisieren mit updated
        => hole alle Daten zu Users aus xxcf_users und die Daten zum lrs und activity_id aus xapidel_object inkl. xxcf_data_types
        => Löschvorgang an LRS-typ schicken
        => wenn's geklappt hat: Zeile aus xxcf_users löschen
        => wenn's für alle user geklappt hat: Zeile aus xxcf_data_settings löschen
        => wenn ggf. auch user gelöscht wurde und der user nur dieses objekt bearbeitet hat, dann lösche auch Zeile in xpidel_user


        Fall 2: 
            * User deleted (in Tabelle xapidel_user eingetragen mit Feld updated=null)
            * Objekt noch vorhanden (kein Eintrag in Tabelle xapidel_object)
        => xapidel_user aktualisieren mit updated
        => hole alle Daten zum User aus xxcf_users und die Daten zum lrs und activity_id aus xxcf_settings inkl. xxcf_data_types
        => Löschvorgang an LRS-typ schicken
        => wenn's geklappt hat: Zeile aus xxcf_users löschen
        => wenn's für alle Objekte, die der User genutzt hat, gelöscht wurde: Zeile in xapidel_user löschen
        
        Fall 3:
            * User deleted (in Tabelle xapidel_user eingetragen mit Feld updated=null)
            * Objekt auch deleted (Eintrag in Tabelle xapidel_object mit updated=null)
            => xapidel_user aktualisieren mit updated
            => xapidel_object aktualisieren mit updated
            => hole alle Daten zum User aus xxcf_users und die Daten zum lrs und activity_id aus xapidel_object inkl. xxcf_data_types
            => Löschvorgang an LRS-typ schicken
            => wenn's geklappt hat: Zeile aus xxcf_users löschen
            => wenn's für alle Objekte, die der User genutzt hat, gelöscht wurde: Zeile in xapidel_user löschen
            => Zeile in Tabelle xapidel_object löschen


        
        */
        
        
        //user deleted
        //SELECT distinct LRS credentials for all objs  - ACHTUNG Plugin-Version beachten!
        //SELECT usr_id FROM xapidel_user
        //usr_ids=..
        //SELECT obj_id, user_cred WHERE user_id in (usr_ids)
        //delete in lrs - wenn nicht in separate log-tabelle schreiben
        //delete xxcf_users WHERE obj_id and user_cred 
        //if numrows for usr_id =0 DELETE FROM xapidel_user WHERE usr_id=%s
        
        //Hinweis auf negative Auswirkungen von lrs-typ-Änderungen für Lösch vorgänge
		
        //object deleted
        //SELECT activity_id, lrs_cred FROM xapidel_obj, xxcf_data_types WHERE xxcf_data_types.type_id = xapidel_obj.type_d //ACHTUNG: endpoint_use egal, lrs_type_id genutzt?
        
        
        //lrs_type_id deleted???
        
        /*
		if( !$this->hasLrsType() )
		{
			ilLoggerFactory::getRootLogger()->alert('No lrs type configured!');
			$cronResult->setStatus(ilCronJobResult::STATUS_INVALID_CONFIGURATION);
			return $cronResult;
		}
		*/
		// $lpChangesQueue = new ilxapidelChangesQueue();
		// $lpChangesQueue->load();
		
		// $statementListBuilder = new ilxapidelXapiStatementListBuilder(ilLoggerFactory::getRootLogger(), $this->getLrsType());
		// $statementList = $statementListBuilder->buildStatementsList($lpChangesQueue);
		/*
		$lrsRequest = new ilxapidelXapiRequest(
			ilLoggerFactory::getRootLogger(),
			$this->getLrsType()->getLrsEndpointStatementsLink(),
			$this->getLrsType()->getLrsKey(),
			$this->getLrsType()->getLrsSecret()
		);
		
		if( $lrsRequest->send($statementList) )
		{
			if( $lpChangesQueue->hasEntries() )
			{
				$lpChangesQueue->delete();
				$cronResult->setStatus(ilCronJobResult::STATUS_OK);
			}
			else
			{
				$cronResult->setStatus(ilCronJobResult::STATUS_NO_ACTION);
			}
		}
		else
		{
			$cronResult->setStatus(ilCronJobResult::STATUS_FAIL);
		}
        */

        // Fall 1:
        // check deleted objects where updated = NULL

        $newDeletedObjects = $this->model->getNewDeletedXapiObjects();
        //ilLoggerFactory::getRootLogger()->alert(var_export($newDeletedObjects,TRUE));

		$deletedObjectData = array();
        $allDone = true;
        foreach ($newDeletedObjects as $deletedObject) {
            ilLoggerFactory::getRootLogger()->alert($deletedObject['obj_id']);
            // set object to updated
            $this->model->setXapiObjAsUpdated($deletedObject['obj_id']);
            // delete data
            $deleteRequest = new ilXapiCmi5StatementsDeleteRequest(
                (int) $deletedObject['obj_id'],
                (int) $deletedObject['type_id'],
                (string) $deletedObject['activity_id'],
                NULL,
                ilXapiCmi5StatementsDeleteRequest::DELETE_SCOPE_ALL
            );
            $done = $deleteRequest->delete();
            // entry in xxcf_users is already deleted from ilXapiCmi5StatementsDeleteRequest
            // delete in obj_id from xxcf_data_settings
            if ($done) {
				ilLoggerFactory::getRootLogger()->alert("deleted object {$deletedObject['obj_id']} data");
				$deletedObjectData[] = $deletedObject['obj_id'];
                $this->model->deleteXapiObjectEntry($deletedObject['obj_id']);
            }
            else {
				ilLoggerFactory::getRootLogger()->alert("error: object {$deletedObject['obj_id']} data deletion");
                $this->model->resetUpdatedXapiObj($deletedObject['obj_id']);
                $allDone = false;
            }
		}

		// Fall 2:
		// check deleted users where updated = NULL
		$newDeletedUsers = $this->model->getNewDeletedUsers();
		foreach ($newDeletedUsers as $deletedUser) {
			$usrId = $deletedUser['usr_id'];
			// set user to updated
			$this->model->setUserAsUpdated($usrId);
			// get all objects of deleted user
			$xapiObjects = $this->model->getXapiObjectsByUser($usrId);
			$usrObjectsDone = true;
			foreach ($xapiObjects as $xapiObject) {
				$objId = $xapiObject['obj_id'];
				// check if all object data already successfully deleted in previous step within this run, because object was also deleted
				if (in_array($objId, $deletedObjectData)) {
					ilLoggerFactory::getRootLogger()->alert("nothing to do, because of complete object data deletion in previous step");
					continue;
				}
				$deleteRequest = new ilXapiCmi5StatementsDeleteRequest(
					(int) $xapiObject['obj_id'],
					(int) $xapiObject['lrs_type_id'],
					(string) $xapiObject['activity_id'],
					$usrId,
					ilXapiCmi5StatementsDeleteRequest::DELETE_SCOPE_OWN
				);
				$done = $deleteRequest->delete();
				// entry in xxcf_users is already deleted from ilXapiCmi5StatementsDeleteRequest
				if ($done) {
					ilLoggerFactory::getRootLogger()->alert("deleted object {$objId} data for user {$usrId}");
				}
				else {
					ilLoggerFactory::getRootLogger()->alert("error: object {$objId} data deletion for user {$usrId}");
					$usrObjectsDone = false;
				}
			} // EOF foreach ($xapiObjects as $xapiObject)

			if ($usrObjectsDone) {
				$this->model->deleteUserEntry($usrId);
			}
			else {
                $this->model->resetUpdatedXapiUser($usrId);
				$allDone = false;
			}
		}

		// Fall 3 wird noch gebraucht?

		// maybe more detailled success/fail messages?

		if ($allDone) {
			$cronResult->setStatus(ilCronJobResult::STATUS_OK);
		}
		else {
			$cronResult->setStatus(ilCronJobResult::STATUS_FAIL);
		}
		return $cronResult;
    }
}
