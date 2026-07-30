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
		$this->CI->config->load('ldap', TRUE);
		$this->config = $this->CI->config->item('ldap');

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
		if (!$this->config['enabled']) {
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

		// Step 1: bind as service account
		if (!@ldap_bind($this->conn, $this->config['bind_dn'], $this->config['bind_password'])) {
			log_message('error', 'Ldap_auth: service account bind failed. Check bind_dn/bind_password.');
			$this->close();
			return false;
		}

		// Step 2: search for the user's DN, escaping the filter value
		// against LDAP filter injection (RFC 4515 escaping)
		$safe_username = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
		$filter = sprintf($this->config['search_filter'], $safe_username);

		$search = @ldap_search($this->conn, $this->config['base_dn'], $filter, array('dn'));
		if ($search === false) {
			log_message('error', 'Ldap_auth: search failed for filter ' . $filter);
			$this->close();
			return false;
		}

		$entries = ldap_get_entries($this->conn, $search);
		if (!isset($entries['count']) || $entries['count'] !== 1) {
			// 0 matches = no such user; >1 matches = ambiguous filter/config.
			// Both are treated as auth failure without distinguishing which,
			// so the login form can't be used to enumerate valid usernames.
			log_message('info', "Ldap_auth: user lookup returned {$entries['count']} results for {$safe_username}.");
			$this->close();
			return false;
		}

		$user_dn = $entries[0]['dn'];

		// Optional: require membership in a specific group before allowing
		// the bind-as-user step at all.
		if (!empty($this->config['required_group_dn'])) {
			if (!$this->is_member_of($user_dn, $this->config['required_group_dn'])) {
				log_message('info', "Ldap_auth: {$user_dn} is not a member of required group.");
				$this->close();
				return false;
			}
		}

		// Step 3: re-bind as the found DN using the password the user
		// supplied. This is the step that actually verifies the password —
		// the service account bind above never validates the user's secret.
		// Re-connect fresh so a bound service-account connection is never
		// reused to attempt a user bind (avoids state leakage across binds).
		$this->close();
		if (!$this->connect()) {
			return false;
		}

		if (!@ldap_bind($this->conn, $user_dn, $password)) {
			log_message('info', "Ldap_auth: password verification failed for {$user_dn}.");
			$this->close();
			return false;
		}

		// Success — pull mapped attributes for the caller to use when
		// creating/updating the local shadow user record.
		$attrs = $this->fetch_attributes($user_dn);
		$this->close();

		return $attrs;
	}

	protected function connect()
	{
		$this->conn = @ldap_connect($this->config['host'], $this->config['port']);
		if (!$this->conn) {
			log_message('error', 'Ldap_auth: ldap_connect failed for host ' . $this->config['host']);
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
