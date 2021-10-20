<#1>
<?php
//
?>
<#2>
<?php
$fields_data = array(
    'usr_id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ),
    'added' => array(
        'type' => 'timestamp',
        'notnull' => true
    ),
    'updated' => array(
        'type' => 'timestamp',
        'notnull' => false,
        'default' => null
    ),
);
$ilDB->createTable("crnhk_xapidel_user", $fields_data);
$ilDB->addPrimaryKey("crnhk_xapidel_user", array("usr_id"));
?>
<#3>
<?php
$fields_data = array(
    'obj_id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ),
    'type_id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ),
    'activity_id' => array(
        'type' => 'text',
        'length' => 128,
        'notnull' => true,
    ),
    'added' => array(
        'type' => 'timestamp',
        'notnull' => true
    ),
    'updated' => array(
        'type' => 'timestamp',
        'notnull' => false,
        'default' => null
    ),
);
$ilDB->createTable("crnhk_xapidel_object", $fields_data);
$ilDB->addPrimaryKey("crnhk_xapidel_object", array("obj_id", "type_id", "activity_id"));
?>


