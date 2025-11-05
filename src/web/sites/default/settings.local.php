<?php

$databases['default']['default'] = array (
  'database' => 'workbc-tr',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'postgres',
  'port' => '5432',
  'driver' => 'pgsql',
);

$config['search_api.server.career_resources_solr'] = [
  'id' => 'career_resources_solr',
  'name' => 'Solr',
  'description' => '',
  'backend' => 'search_api_solr',
  'backend_config' => [
    'retrieve_data' => false,
    'highlight_data' => false,
    'site_hash' => false,
    'server_prefix' => '',
    'domain' => 'generic',
    'environment' => 'default',
    'connector' => 'standard',
    'connector_config' => [
      'scheme' => 'http',
      'host' => 'solr',
      'port' => 8983,
      'path' => '/',
      'core' => 'workbc-tr_dev',
      'timeout' => 5,
      'index_timeout' => 5,
      'optimize_timeout' => 10,
      'finalize_timeout' => 30,
      'skip_schema_check' => false,
      'solr_version' => '',
      'http_method' => 'AUTO',
      'commit_within' => 1000,
      'jmx' => false,
      'jts' => false,
      'solr_install_dir' => '',
    ],
    'optimize' => false,
    'fallback_multiple' => false,
    'disabled_field_types' => [],
    'disabled_caches' => [],
    'disabled_request_handlers' => [
      'request_handler_elevate_default_7_0_0',
      'request_handler_replicationmaster_default_7_0_0',
      'request_handler_replicationslave_default_7_0_0',
    ],
    'disabled_request_dispatchers' => [
      'request_dispatcher_httpcaching_default_7_0_0',
    ],
    'rows' => 10,
    'index_single_documents_fallback_count' => 10,
    'index_empty_text_fields' => false,
    'suppress_missing_languages' => false
  ],
];
