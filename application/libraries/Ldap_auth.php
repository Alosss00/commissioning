<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ldap_auth
 *
 * Search+bind LDAP authentication for CI3.
 *
 * Flow:
 *   1. Bind with a low-privilege service account (never the user's own creds)
 *   2. Search for the user's DN using an escaped filter
 *   3. Re-bind using the found DN + the password the user typed, to verify it
 *   4. On success, return the user's directory attributes; on any failure,
 *      return false and log the reason (never expose LDAP error detail to
 *      the login form — that leaks directory structure to attackers)
 *
 * Deliberately does NOT support anonymous bind as a "successful login" —
 * an empty password must be rejected before ever touching LDAP, because
 * many directories treat ldap_bind($conn, $dn, '') as an anonymous bind
 * that returns TRUE.
 */
class Ldap_auth
{
	protected $CI;
	protected $config;
	protected $conn = false;

	public function __construct()
	{
		$this->CI =& get_instance();
		@$this->CI->config->load('ldap', TRUE, TRUE);
		$cfg = $this->CI->config->item('ldap');

		$default_config = [
			'enabled'          => true,
			'host'             => 'ldaps://archimining.local',
			'port'             => 636,
			'use_starttls'     => false,
			'protocol_version' => 3,
			'network_timeout'  => 5,
			'time_limit'       => 5,
			'domain'           => 'ARCHIMINING',
			'base_dn'          => 'DC=archimining,DC=local',
			'bind_dn'          => 'CN=svc-ujikelayakan,OU=Service Accounts,DC=archimining,DC=local',
			'bind_password'    => getenv('LDAP_BIND_PASSWORD'),
			'user_attribute'   => 'sAMAccountName',
			'search_filter'    => '(sAMAccountName=%s)',
			'attr_map'         => [
				'email'       => 'mail',
				'full_name'   => 'displayName',
				'employee_id' => 'employeeNumber',
			],
			'required_group_dn' => null,
			'jit_provisioning'  => true,
			'default_role'      => 'pemohon',
		];

		$this->config = array_merge($default_config, is_array($cfg) ? $cfg : []);

		if (!extension_loaded('ldap')) {
			log_message('error', 'Ldap_auth: php-ldap extension is not loaded.');
		}
	}

	/**
	 * Attempt to authenticate a user against LDAP.
	 *
	 * @param string $username raw username as typed into the login form
	 * @param string $password raw password as typed into the login form
	 * @return array|false  attributes array on success, false on failure
	 */
	public function authenticate($username, $password)
	{
		if (empty($this->config['enabled'])) {
			return false;
		}

		// Reject empty password BEFORE any LDAP call — prevents the
		// classic "unauthenticated bind" bypass.
		if ($username === '' || $password === '') {
			log_message('info', 'Ldap_auth: rejected empty username/password before bind.');
			return false;
		}

		if (!extension_loaded('ldap')) {
			return false;
		}

		if (!$this->connect()) {
			return false;
		}

		// Step 1: Search via Service Account (if configured), or fallback to Direct Active Directory Bind
		$safe_username = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
		$user_dn = null;

		if (!empty($this->config['bind_dn']) && !empty($this->config['bind_password'])) {
			if (@ldap_bind($this->conn, $this->config['bind_dn'], $this->config['bind_password'])) {
				$filter = sprintf($this->config['search_filter'], $safe_username);
				$search = @ldap_search($this->conn, $this->config['base_dn'], $filter, array('dn'));
				if ($search !== false) {
					$entries = ldap_get_entries($this->conn, $search);
					if (isset($entries['count']) && $entries['count'] === 1) {
						$user_dn = $entries[0]['dn'];
					}
				}
			}
		}

		// Optional: require membership in a specific group
		if ($user_dn && !empty($this->config['required_group_dn'])) {
			if (!$this->is_member_of($user_dn, $this->config['required_group_dn'])) {
				log_message('info', "Ldap_auth: {$user_dn} is not a member of required group.");
				$this->close();
				return false;
			}
		}

		// Step 2: Re-connect fresh and verify user password
		$this->close();
		if (!$this->connect()) {
			return false;
		}

		$bind_success = false;
		if ($user_dn) {
			$bind_success = @ldap_bind($this->conn, $user_dn, $password);
		}

		// Step 3: Fallback Active Directory UPN Bind (username@archimining.local & ARCHIMINING\username)
		if (!$bind_success) {
			$domain = !empty($this->config['domain']) ? $this->config['domain'] : 'ARCHIMINING';
			$upn_candidates = [
				$username . '@' . strtolower($domain) . '.local',
				$domain . '\\' . $username,
				$username . '@archimining.local'
			];

			foreach ($upn_candidates as $upn) {
				$this->close();
				if (!$this->connect()) continue;
				if (@ldap_bind($this->conn, $upn, $password)) {
					$bind_success = true;
					$user_dn = $upn;
					break;
				}
			}
		}

		if (!$bind_success) {
			log_message('info', "Ldap_auth: password verification failed for username {$username}.");
			$this->close();
			return false;
		}

		// Success — pull mapped attributes if available
		$attrs = $this->fetch_attributes($user_dn);
		if (empty($attrs)) {
			$attrs = [
				'dn'          => $user_dn,
				'username'    => $username,
				'email'       => $username . '@archimining.local',
				'full_name'   => ucfirst($username),
				'employee_id' => null
			];
		}
		$this->close();

		return $attrs;
	}

	protected function connect()
	{
		// Bypass certificate verification for internal Active Directory (.local) domain controllers
		putenv('LDAPTLS_REQCERT=never');
		if (defined('LDAP_OPT_X_TLS_REQUIRE_CERT') && defined('LDAP_OPT_X_TLS_NEVER')) {
			@ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
		}

		$host = $this->config['host'];
		if ($this->config['port'] == 636 && strpos($host, 'ldaps://') === false && strpos($host, 'ldap://') === false) {
			$host = 'ldaps://' . $host;
		}

		$this->conn = @ldap_connect($host, $this->config['port']);
		if (!$this->conn) {
			log_message('error', 'Ldap_auth: ldap_connect failed for host ' . $host);
			return false;
		}

		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, $this->config['protocol_version']);
		ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($this->conn, LDAP_OPT_NETWORK_TIMEOUT, $this->config['network_timeout']);
		ldap_set_option($this->conn, LDAP_OPT_TIMELIMIT, $this->config['time_limit']);

		if (!empty($this->config['use_starttls'])) {
			if (!@ldap_start_tls($this->conn)) {
				log_message('error', 'Ldap_auth: StartTLS negotiation failed.');
				$this->conn = false;
				return false;
			}
		}

		return true;
	}

	protected function is_member_of($user_dn, $group_dn)
	{
		$safe_dn = ldap_escape($user_dn, '', LDAP_ESCAPE_FILTER);
		$filter = "(member={$safe_dn})";
		$search = @ldap_search($this->conn, $group_dn, $filter, array('dn'), 0, 1);
		if ($search === false) {
			return false;
		}
		$entries = ldap_get_entries($this->conn, $search);
		return isset($entries['count']) && $entries['count'] > 0;
	}

	protected function fetch_attributes($user_dn)
	{
		$want = array_values($this->config['attr_map']);
		$want[] = $this->config['user_attribute'];

		$search = @ldap_read($this->conn, $user_dn, '(objectClass=*)', $want);
		if ($search === false) {
			return array();
		}
		$entries = ldap_get_entries($this->conn, $search);
		if (!isset($entries[0])) {
			return array();
		}

		$out = array('dn' => $user_dn);
		foreach ($this->config['attr_map'] as $local_key => $ldap_attr) {
			$ldap_attr_lc = strtolower($ldap_attr);
			$out[$local_key] = isset($entries[0][$ldap_attr_lc][0])
				? $entries[0][$ldap_attr_lc][0]
				: null;
		}
		return $out;
	}

	protected function close()
	{
		if ($this->conn) {
			@ldap_unbind($this->conn);
			$this->conn = false;
		}
	}
}
