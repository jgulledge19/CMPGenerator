<?php
/**
 * @package doodle
 * @subpackage processors
 */
/* parse JSON */
if (empty($scriptProperties['data'])) return $modx->error->failure('Invalid data.');
$_DATA = $modx->fromJSON($scriptProperties['data']);
if (!is_array($_DATA)) return $modx->error->failure('Invalid data.');

/* get obj */
if (empty($_DATA['id'])) return $modx->error->failure($modx->lexicon('cmpgenerator.err_ns'));
$cmp = $modx->getObject('CmpGenerator',$_DATA['id']);
if (empty($cmp)) return $modx->error->failure($modx->lexicon('cmpgenerator.err_nf'));

/* set fields */
unset($_DATA['create_date']);
$cmp->fromArray($_DATA);

// return $modx->error->failure('build_scheme: '.$_DATA['build_scheme'].' - '.$cmp->get('build_scheme') );
/* save */
if ($cmp->save() == false) {
    return $modx->error->failure($modx->lexicon('cmpgenerator.err_save'));
}


return $modx->error->success('',$cmp);