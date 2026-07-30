<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_pencabutan_schema extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('pencabutan_stiker')) {
            return;
        }

        $fields = [
            'id_pemohon'        => "INT NULL AFTER id_pengajuan",
            'role_pemohon'      => "INT NULL AFTER id_pemohon",
            'status_request'    => "VARCHAR(50) NOT NULL DEFAULT 'menunggu_ohs_supt' AFTER alasan",
            'ohs_supt_by'       => "INT NULL AFTER status_request",
            'ohs_supt_at'       => "DATETIME NULL AFTER ohs_supt_by",
            'ktt_1_by'          => "INT NULL AFTER ohs_supt_at",
            'ktt_1_at'          => "DATETIME NULL AFTER ktt_1_by",
            'ktt_2_by'          => "INT NULL AFTER ktt_1_at",
            'ktt_2_at'          => "DATETIME NULL AFTER ktt_2_by",
            'catatan_penolakan' => "TEXT NULL AFTER ktt_2_at",
        ];

        foreach ($fields as $field => $def) {
            if (!$this->db->field_exists($field, 'pencabutan_stiker')) {
                @$this->db->query("ALTER TABLE `pencabutan_stiker` ADD COLUMN `$field` $def");
            }
        }
    }

    public function down()
    {
        if (!$this->db->table_exists('pencabutan_stiker')) {
            return;
        }

        $fields = [
            'id_pemohon',
            'role_pemohon',
            'status_request',
            'ohs_supt_by',
            'ohs_supt_at',
            'ktt_1_by',
            'ktt_1_at',
            'ktt_2_by',
            'ktt_2_at',
            'catatan_penolakan'
        ];

        foreach ($fields as $field) {
            if ($this->db->field_exists($field, 'pencabutan_stiker')) {
                @$this->db->query("ALTER TABLE `pencabutan_stiker` DROP COLUMN `$field`");
            }
        }
    }
}
