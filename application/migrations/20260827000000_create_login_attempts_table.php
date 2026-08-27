<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration_Create_login_attempts_table
 * 
 * Membuat tabel login_attempts untuk pencatatan percobaan login berbasis database
 * guna mencegah bypass brute-force lockout melalui penghapusan session / cookie.
 */
class Migration_Create_login_attempts_table extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('login_attempts')) {
            $this->dbforge->add_field([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE
                ],
                'identity' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '45',
                ],
                'attempt' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'last_attempt_time' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['identity', 'ip_address'], FALSE, TRUE); // UNIQUE KEY(identity, ip_address)
            $this->dbforge->create_table('login_attempts', TRUE);
        }
    }

    public function down()
    {
        if ($this->db->table_exists('login_attempts')) {
            $this->dbforge->drop_table('login_attempts', TRUE);
        }
    }
}
