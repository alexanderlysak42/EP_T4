<?php

require_once dirname(__DIR__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'vendor/autoload.php';

use MODX\Revolution\modX;
use MODX\Revolution\modCategory;
use MODX\Revolution\modSnippet;
use MODX\Revolution\modChunk;
use MODX\Revolution\Transport\modPackageBuilder;
use xPDO\Transport\xPDOTransport;

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$root = dirname(__DIR__) . '/';
$sources = [
    'core'      => $root . 'core/components/customform/',
    'assets'    => $root . 'assets/components/customform/',
    'resolvers' => $root . '_build/resolvers/',
];

$builder = new modPackageBuilder($modx);
$builder->createPackage('customform', '1.0.0', '');
$builder->registerNamespace(
    'customform', false, true,
    '{core_path}components/customform/', '{assets_path}components/customform/'
);

$category = $modx->newObject(modCategory::class);
$category->set('category', 'CustomForm');

$snippet = $modx->newObject(modSnippet::class);
$snippet->set('name', 'CustomForm');
$snippet->set('description', 'Форма заявки з валідацією, rate limit і збереженням через xPDO.');
$snippet->set('snippet', '');
$snippet->set('static', true);
$snippet->set('static_file', 'core/components/customform/elements/snippets/snippet.customform.php');

$chunkForm = $modx->newObject(modChunk::class);
$chunkForm->set('name', 'customform.form');
$chunkForm->set('description', 'Шаблон форми заявки CustomForm.');
$chunkForm->set('snippet', '');
$chunkForm->set('static', true);
$chunkForm->set('static_file', 'core/components/customform/elements/chunks/customform.form.tpl');

$chunkSuccess = $modx->newObject(modChunk::class);
$chunkSuccess->set('name', 'customform.success');
$chunkSuccess->set('description', 'Шаблон успішної відправки CustomForm.');
$chunkSuccess->set('snippet', '');
$chunkSuccess->set('static', true);
$chunkSuccess->set('static_file', 'core/components/customform/elements/chunks/customform.success.tpl');

$chunks = [$chunkForm, $chunkSuccess];

$category->addMany($snippet, 'Snippets');
$category->addMany($chunks, 'Chunks');

$vehicle = $builder->createVehicle($category, [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
        'Snippets' => [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ],
        'Chunks' => [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ],
    ],
]);

$vehicle->resolve('file', [
    'source' => $sources['core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
]);
$vehicle->resolve('file', [
    'source' => $sources['assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
]);
$vehicle->resolve('php', [
    'source' => $sources['resolvers'] . 'resolve.schema.php',
]);

$builder->putVehicle($vehicle);

$builder->setPackageAttributes([
    'readme' => file_exists($root . 'README.md') ? file_get_contents($root . 'README.md') : '',
    'license' => '',
]);

$builder->pack();

echo "Package customform-1.0.0.transport.zip built successfully.\n";
