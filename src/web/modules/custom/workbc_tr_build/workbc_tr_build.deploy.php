<?php

/**
 * Fix missing entity type.
 *
 * As per ticket WBCAMS-1215
 */
function workbc_tr_build_deploy_1215_mismatch_fix(&$sandbox = NULL) {

  \Drupal::service("meaofd.fixer")->fix("symfony_mailer_lite_transport");
  return t("[WBCAMS-1215] fix The Drupal Symfony Mailer Lite Transport missing entity type.");
}
