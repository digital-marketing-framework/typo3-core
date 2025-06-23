
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

CREATE TABLE tx_dmfcore_domain_model_test_case (
	label varchar(256) DEFAULT '',
	name varchar(256) DEFAULT '',
	description text DEFAULT '',
	type varchar(64) DEFAULT '',
	hash varchar(64) DEFAULT '',
 	serialized_input mediumtext DEFAULT '',
	serialized_expected_output mediumtext DEFAULT '',

	changed int(11) unsigned DEFAULT '0' NOT NULL,
	created int(11) unsigned DEFAULT '0' NOT NULL,

	UNIQUE unique_name_type (name, type)
);
