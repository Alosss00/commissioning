<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| LDAP Authentication Configuration
| -------------------------------------------------------------------
| Environment: ARCHIMINING (Active Directory)
| Host: archimining.local (Port 636 / LDAPS)
| Base DN: DC=archimining,DC=local
| -------------------------------------------------------------------
*/

$config['ldap'] = array(

	// Enable/disable LDAP auth globally without removing code
	'enabled'          => true,

	// Use ldaps:// (port 636) whenever possible
	'host'             => 'ldaps://archimining.local',
	'port'             => 636,
	'use_starttls'     => false,

	// Network/protocol hardening
	'protocol_version' => 3,
	'network_timeout'  => 5,   // seconds
	'time_limit'       => 5,   // seconds

	// Active Directory Domain
	'domain'           => 'ARCHIMINING',

	// Base DN to search under
	'base_dn'          => 'DC=archimining,DC=local',

	// Service (bind) account used ONLY to search for the user's DN.
	// bind_password is read from environment variable LDAP_BIND_PASSWORD
	'bind_dn'          => getenv('LDAP_BIND_DN') ?: 'CN=svc-ujikelayakan,OU=Service Accounts,DC=archimining,DC=local',
	'bind_password'    => getenv('LDAP_BIND_PASSWORD'),

	// Attribute used as the login username (sAMAccountName for Active Directory)
	'user_attribute'   => 'sAMAccountName',

	// Search filter template for Active Directory
	'search_filter'    => '(sAMAccountName=%s)',

	// Attributes to pull back and map into the local session/user record
	'attr_map' => array(
		'email'      => 'mail',
		'full_name'  => 'displayName',
		'employee_id'=> 'employeeNumber',
	),

	// Optional: group DN whose members are allowed to log in at all.
	'required_group_dn' => null,

	// JIT provisioning: false (users must be pre-created by admin)
	'jit_provisioning' => false,
	'default_role'     => 'pemohon',
);
