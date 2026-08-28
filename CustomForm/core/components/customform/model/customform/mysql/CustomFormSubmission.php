<?php
namespace customform\mysql;

use xPDO\xPDO;

class CustomFormSubmission extends \customform\CustomFormSubmission
{

    public static $metaMap = array (
        'package' => 'customform',
        'version' => '3.0',
        'table' => 'customform_submissions',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'name' => NULL,
            'phone' => NULL,
            'email' => NULL,
            'message' => NULL,
            'crm_id' => NULL,
            'status' => 'new',
            'createdon' => NULL,
        ),
        'fieldMeta' => 
        array (
            'name' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '100',
                'phptype' => 'string',
                'null' => false,
            ),
            'phone' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '20',
                'phptype' => 'string',
                'null' => false,
            ),
            'email' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '150',
                'phptype' => 'string',
                'null' => true,
            ),
            'message' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
                'null' => true,
            ),
            'crm_id' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '50',
                'phptype' => 'string',
                'null' => true,
            ),
            'status' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '20',
                'phptype' => 'string',
                'default' => 'new',
            ),
            'createdon' => 
            array (
                'dbtype' => 'datetime',
                'phptype' => 'datetime',
                'null' => true,
            ),
        ),
        'indexes' => 
        array (
            'phone' => 
            array (
                'alias' => 'phone',
                'primary' => false,
                'unique' => false,
                'columns' => 
                array (
                    'phone' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
    );

}
