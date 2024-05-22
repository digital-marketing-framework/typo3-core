
CREATE TABLE tx_dmfcore_domain_model_api_endpoint (
	name varchar(64) DEFAULT '',
	enabled tinyint(4) unsigned DEFAULT '0' NOT NULL,
	push_enabled tinyint(4) unsigned DEFAULT '0' NOT NULL,
	pull_enabled tinyint(4) unsigned DEFAULT '0' NOT NULL,
	disable_context tinyint(4) unsigned DEFAULT '0' NOT NULL,
	allow_context_override tinyint(4) unsigned DEFAULT '0' NOT NULL,
	expose_to_frontend tinyint(4) unsigned DEFAULT '0' NOT NULL,
	configuration_document text DEFAULT '',
);
