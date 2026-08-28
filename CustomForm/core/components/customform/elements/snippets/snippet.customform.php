<?php
$corePath  = $modx->getOption('customform.core_path', $scriptProperties,
    $modx->getOption('core_path') . 'components/customform/');
$assetsUrl = $modx->getOption('customform.assets_url', $scriptProperties,
    $modx->getOption('assets_url') . 'components/customform/');

$modx->addPackage('customform', $corePath . 'model/');

$modx->regClientCSS($assetsUrl . 'css/customform.css');
$modx->regClientStartupScript($assetsUrl . 'js/customform.js');

$tplForm    = $modx->getOption('tplForm', $scriptProperties, 'customform.form');
$tplSuccess = $modx->getOption('tplSuccess', $scriptProperties, 'customform.success');
$rateLimit  = (int) $modx->getOption('rateLimit', $scriptProperties, 3);
$rateWindow = (int) $modx->getOption('rateWindow', $scriptProperties, 3600);

if (!function_exists('customform_render')) {
    function customform_render(modX $modx, $tpl, array $values = [], array $errors = []) {
        return $modx->getChunk($tpl, [
            'name'         => $values['name'] ?? '',
            'phone'        => $values['phone'] ?? '',
            'email'        => $values['email'] ?? '',
            'message'      => $values['message'] ?? '',
            'error_name'   => $errors['name'] ?? '',
            'error_phone'  => $errors['phone'] ?? '',
            'error_email'  => $errors['email'] ?? '',
            'error_global' => $errors['_global'] ?? '',
        ]);
    }
}

if (!function_exists('customform_send_to_crm')) {
    function customform_send_to_crm(modX $modx, array $data) {
        return null;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return customform_render($modx, $tplForm);
}

if (!empty($_POST['website'])) {
    return customform_render($modx, $tplSuccess);
}

$ip       = $_SERVER['REMOTE_ADDR'] ?? '';
$cacheKey = 'customform/ratelimit/' . md5($ip);
// JSON-формат вместо PHP-файла: значение читается на каждый запрос заново,
// без риска, что opcache отдаст устаревший байткод часто перезаписываемого файла
$cacheOptions = [\xPDO\xPDO::OPT_CACHE_FORMAT => \xPDO\Cache\xPDOCacheManager::CACHE_JSON];
$attempts = (int) $modx->cacheManager->get($cacheKey, $cacheOptions);

if ($attempts >= $rateLimit) {
    $modx->log(modX::LOG_LEVEL_WARN, "[CustomForm] Rate limit exceeded for {$ip}");
    return customform_render($modx, $tplForm, $_POST, ['_global' => 'Забагато спроб. Спробуйте пізніше.']);
}

$errors  = [];
$name    = trim($modx->sanitizeString($_POST['name'] ?? ''));
$phone   = preg_replace('/\D/', '', $_POST['phone'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($modx->sanitizeString($_POST['message'] ?? ''));

if (!preg_match("/^[\p{L}'’\-\s]{2,50}$/u", $name)) {
    $errors['name'] = "Ім'я має містити тільки літери (2-50 символів)";
}
if (strlen($phone) < 12 || strpos($phone, '380') !== 0) {
    $errors['phone'] = 'Введіть номер у форматі +380XXXXXXXXX';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Некоректний email';
}

if (!empty($errors)) {
    return customform_render($modx, $tplForm,
        ['name' => $name, 'phone' => $phone, 'email' => $email, 'message' => $message],
        $errors
    );
}

$nextAttempts = $attempts + 1;
$modx->cacheManager->set($cacheKey, $nextAttempts, $rateWindow, $cacheOptions);

$submission = $modx->newObject('customform\\CustomFormSubmission');
$submission->fromArray([
    'name'      => $name,
    'phone'     => '+' . $phone,
    'email'     => $email,
    'message'   => $message,
    'status'    => 'new',
    'createdon' => date('Y-m-d H:i:s'),
]);

if (!$submission->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[CustomForm] Failed to save submission, phone=' . $phone);
    return customform_render($modx, $tplForm, $_POST, ['_global' => 'Помилка збереження, спробуйте пізніше.']);
}

$crmId = customform_send_to_crm($modx, $submission->toArray());
if ($crmId) {
    $submission->set('crm_id', $crmId);
    $submission->set('status', 'sent');
    $submission->save();
} else {
    $modx->log(modX::LOG_LEVEL_WARN, '[CustomForm] CRM push skipped/failed for submission #' . $submission->get('id'));
}

return customform_render($modx, $tplSuccess);