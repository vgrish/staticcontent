<?php
/** @var array $scriptProperties */
/** @var staticcontent $staticcontent */
$staticcontent = $modx->getService('staticcontent');
$staticcontent->initialize($modx->context->key);

echo "<pre>";
print_r($_REQUEST);