<?php
/** @var xPDO\Transport\xPDOTransport $transport */
/** @var array $options */

if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    switch ($options[xPDO\Transport\xPDOTransport::PACKAGE_ACTION]) {
        case xPDO\Transport\xPDOTransport::ACTION_INSTALL:
        case xPDO\Transport\xPDOTransport::ACTION_UPGRADE:
            $corePath = $modx->getOption('customform.core_path', null,
                $modx->getOption('core_path') . 'components/customform/');
            $modx->addPackage('customform', $corePath . 'model/');

            $manager = $modx->getManager();
            $manager->createObjectContainer('customform\\CustomFormSubmission');
            break;
    }
}

return true;
