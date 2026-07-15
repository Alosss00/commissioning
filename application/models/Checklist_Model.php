<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Checklist_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEMPLATE
    // ─────────────────────────────────────────────────────────────────────────

    public function get_all_templates()
    {
        return $this->db
            ->select('ct.*, t.nama_tipe, t.kode_tipe,
                  t.doc_no, t.title_id, t.title_en,
                  t.doc_name_id, t.doc_name_en,
                  t.tgl_terbit, t.tgl_review, t.no_revisi')
            ->from('checklist_template ct')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = ct.id_tipe_kendaraan', 'left')
            ->where('ct.is_active', 1)
            ->order_by('ct.kode', 'ASC')
            ->get()->result();
    }

    public function get_template($id)
    {
        return $this->db
            ->select('ct.*, t.nama_tipe, t.kode_tipe,
                  t.doc_no, t.title_id, t.title_en,
                  t.doc_name_id, t.doc_name_en,
                  t.tgl_terbit, t.tgl_review, t.no_revisi')
            ->from('checklist_template ct')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = ct.id_tipe_kendaraan', 'left')
            ->where('ct.id_template', (int) $id)
            ->get()->row();
    }

    public function get_template_by_jenis($nama_tipe)
    {
        return $this->db
            ->select('ct.*, t.nama_tipe, t.kode_tipe')
            ->from('checklist_template ct')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = ct.id_tipe_kendaraan')
            ->where('t.nama_tipe', $nama_tipe)
            ->where('ct.is_active', 1)
            ->get()->row();
    }

    public function get_template_by_tipe_id($id_tipe_kendaraan)
    {
        return $this->db
            ->select('ct.*, t.nama_tipe, t.kode_tipe,
                  t.doc_no, t.title_id, t.title_en,
                  t.doc_name_id, t.doc_name_en,
                  t.tgl_terbit, t.tgl_review, t.no_revisi')
            ->from('checklist_template ct')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = ct.id_tipe_kendaraan')
            ->where('ct.id_tipe_kendaraan', (int) $id_tipe_kendaraan)
            ->where('ct.is_active', 1)
            ->get()->row();
    }

    public function get_tipe_tersedia()
    {
        return $this->db
            ->select('t.id_tipe_kendaraan, t.nama_tipe, t.kode_tipe')
            ->from('tipe_kendaraan t')
            ->join(
                'checklist_template ct',
                'ct.id_tipe_kendaraan = t.id_tipe_kendaraan AND ct.is_active = 1',
                'left'
            )
            ->where('t.is_active', 1)
            ->where('ct.id_template IS NULL', null, false)
            ->order_by('t.nama_tipe', 'ASC')
            ->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHECKLIST ITEM
    // ─────────────────────────────────────────────────────────────────────────

    public function get_items($id_template)
    {
        return $this->db
            ->where('id_template', $id_template)
            ->order_by('kategori DESC')
            ->order_by('CAST(no_urut AS UNSIGNED)', 'ASC', false)
            ->get('checklist_item')
            ->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SAVE CHECKLIST — dengan history snapshot sebelum overwrite
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Simpan jawaban checklist.
     *
     * Alur baru (Opsi A — history):
     *   1. Hitung versi berikutnya untuk id_uji ini di history.
     *   2. Snapshot seluruh uji_checklist yang ada ke uji_checklist_history
     *      SEBELUM dihapus — hanya jika sudah ada data (bukan submit pertama).
     *   3. DELETE uji_checklist lama, INSERT batch baru.
     *
     * Dengan ini:
     *   - Submit pertama   : tidak ada snapshot (tidak ada data lama)
     *   - Inspeksi ulang 1 : snapshot versi 1 tersimpan, versi aktif = 2
     *   - Inspeksi ulang 2 : snapshot versi 2 tersimpan, versi aktif = 3
     *   - dst.
     *
     * @param int   $id_uji       FK ke uji_kelayakan
     * @param array $items        array [id_item => ['hasil'=>..., 'keterangan'=>...]]
     * @param array $uji_meta     opsional — ['id_pengajuan', 'hasil_uji',
     *                            'nama_inspektor', 'perusahaan_inspektor',
     *                            'catatan_temuan']
     *                            Jika kosong, diambil dari DB secara otomatis.
     */
    public function save_checklist($id_uji, $items, $uji_meta = [])
    {
        // ── Cek apakah sudah ada data lama yang perlu di-snapshot ────────────
        $existing = $this->db
            ->where('id_uji', $id_uji)
            ->get('uji_checklist')
            ->result();

        if (!empty($existing)) {
            // ── Ambil meta dari DB jika tidak disuplai ───────────────────────
            if (empty($uji_meta)) {
                $uk = $this->db
                    ->select('id_pengajuan, hasil AS hasil_uji,
                              nama_inspektor, perusahaan_inspektor, catatan_temuan,
                              updated_at, created_at')
                    ->where('id_uji', $id_uji)
                    ->get('uji_kelayakan')->row();

                if ($uk) {
                    $uji_meta = [
                        'id_pengajuan'         => $uk->id_pengajuan,
                        'hasil_uji'            => $uk->hasil_uji,
                        'nama_inspektor'       => $uk->nama_inspektor,
                        'perusahaan_inspektor' => $uk->perusahaan_inspektor,
                        'catatan_temuan'       => $uk->catatan_temuan,
                        'snapshot_at'          => $uk->updated_at ?: $uk->created_at,
                    ];
                }
            }

            // ── Hitung versi berikutnya ──────────────────────────────────────
            // Versi = (max versi yang sudah ada di history untuk id_uji ini) + 1
            // Jika belum ada history sama sekali → versi 1 (snapshot pertama)
            $max_versi = $this->db
                ->select_max('versi')
                ->where('id_uji', $id_uji)
                ->get('uji_checklist_history')
                ->row();

            $versi_baru = ($max_versi && $max_versi->versi !== null)
                ? (int) $max_versi->versi + 1
                : 1;

            // ── Snapshot ke history ──────────────────────────────────────────
            $snapshot_at = $uji_meta['snapshot_at']
                ?? date('Y-m-d H:i:s');

            $history_batch = [];
            foreach ($existing as $row) {
                $history_batch[] = [
                    'id_uji'               => $id_uji,
                    'id_pengajuan'         => $uji_meta['id_pengajuan']         ?? 0,
                    'versi'                => $versi_baru,
                    'id_item'              => $row->id_item,
                    'hasil'                => $row->hasil,
                    'keterangan'           => $row->keterangan,
                    'snapshot_at'          => $snapshot_at,
                    'hasil_uji'            => $uji_meta['hasil_uji']            ?? null,
                    'nama_inspektor'       => $uji_meta['nama_inspektor']       ?? null,
                    'perusahaan_inspektor' => $uji_meta['perusahaan_inspektor'] ?? null,
                    'catatan_temuan'       => $uji_meta['catatan_temuan']       ?? null,
                ];
            }

            if (!empty($history_batch)) {
                $this->db->insert_batch('uji_checklist_history', $history_batch);
            }
        }

        // ── Delete lama + Insert baru (alur yang sudah ada, tidak berubah) ──
        $this->db->where('id_uji', $id_uji)->delete('uji_checklist');
        if (empty($items)) return true;

        $batch = [];
        foreach ($items as $id_item => $val) {
            $batch[] = [
                'id_uji'     => $id_uji,
                'id_item'    => (int) $id_item,
                'hasil'      => in_array($val['hasil'], ['yes', 'no', 'na'])
                    ? $val['hasil'] : 'na',
                'keterangan' => isset($val['keterangan'])
                    ? trim($val['keterangan']) : '',
            ];
        }
        return $this->db->insert_batch('uji_checklist', $batch);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET HISTORY — untuk ditampilkan di detail / PDF inspeksi ulang
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Semua versi history untuk satu id_uji, dikelompokkan per versi.
     * Return: array [ versi => [ rows ] ]
     */
    public function get_checklist_history($id_uji)
    {
        $rows = $this->db
            ->select('h.*, ci.kriteria, ci.kategori, ci.no_urut')
            ->from('uji_checklist_history h')
            ->join('checklist_item ci', 'ci.id_item = h.id_item', 'left')
            ->where('h.id_uji', (int) $id_uji)
            ->order_by('h.versi', 'ASC')
            ->order_by('ci.kategori DESC')
            ->order_by('CAST(ci.no_urut AS UNSIGNED)', 'ASC', false)
            ->get()->result();

        // Kelompokkan per versi
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r->versi][] = $r;
        }
        return $grouped;
    }

    /**
     * Daftar ringkasan versi untuk satu id_uji.
     * Berguna untuk header timeline (versi, tanggal, hasil, inspektor).
     */
    public function get_history_versions($id_uji)
    {
        return $this->db
            ->select('versi,
                  MAX(snapshot_at)          AS snapshot_at,
                  MAX(hasil_uji)            AS hasil_uji,
                  MAX(nama_inspektor)       AS nama_inspektor,
                  MAX(perusahaan_inspektor) AS perusahaan_inspektor,
                  MAX(catatan_temuan)       AS catatan_temuan,
                  COUNT(*)                  AS total_item,
                  SUM(hasil = "no")         AS total_no,
                  SUM(hasil = "yes")        AS total_yes,
                  SUM(hasil = "na")         AS total_na', false)
            ->where('id_uji', (int) $id_uji)
            ->group_by('versi')
            ->order_by('versi', 'ASC')
            ->get('uji_checklist_history')
            ->result();
    }

    /**
     * History berdasarkan id_pengajuan — untuk tampilkan semua riwayat
     * seluruh siklus inspeksi di halaman detail pengajuan.
     */
    public function get_history_by_pengajuan($id_pengajuan)
    {
        return $this->db
            ->select('h.id_uji, h.versi, h.snapshot_at, h.hasil_uji,
                      h.nama_inspektor, h.perusahaan_inspektor, h.catatan_temuan,
                      COUNT(*) AS total_item,
                      SUM(h.hasil = "no")  AS total_no,
                      SUM(h.hasil = "yes") AS total_yes,
                      SUM(h.hasil = "na")  AS total_na')
            ->where('h.id_pengajuan', (int) $id_pengajuan)
            ->group_by('h.id_uji, h.versi')
            ->order_by('h.id_uji', 'ASC')
            ->order_by('h.versi', 'ASC')
            ->get('uji_checklist_history')
            ->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // JAWABAN & SUMMARY (tidak berubah)
    // ─────────────────────────────────────────────────────────────────────────

    public function get_checklist_answers($id_uji)
    {
        $this->db->select('uc.*, ci.kriteria, ci.kategori, ci.no_urut, ct.nama_template');
        $this->db->from('uji_checklist uc');
        $this->db->join('checklist_item ci',     'ci.id_item = uc.id_item');
        $this->db->join('checklist_template ct', 'ct.id_template = ci.id_template');
        $this->db->where('uc.id_uji', $id_uji);
        $this->db->order_by('ci.kategori DESC');
        $this->db->order_by('CAST(ci.no_urut AS UNSIGNED)', 'ASC', false);
        return $this->db->get()->result();
    }

    /**
     * Summary hasil checklist.
     * Lulus = tidak ada satupun item (CRITICAL maupun GENERAL) yang NO.
     */
    public function get_summary($id_uji)
    {
        $rows    = $this->get_checklist_answers($id_uji);
        $summary = [
            'total'       => count($rows),
            'yes'         => 0,
            'no'          => 0,
            'na'          => 0,
            'critical_no' => 0,
            'general_no'  => 0,
            'items_no'    => [],
        ];
        foreach ($rows as $r) {
            if (isset($summary[$r->hasil])) $summary[$r->hasil]++;
            if ($r->hasil === 'no') {
                if ($r->kategori === 'CRITICAL') $summary['critical_no']++;
                else                              $summary['general_no']++;
                $summary['items_no'][] = $r;
            }
        }
        $summary['lulus']    = $summary['no'] === 0;
        $summary['total_no'] = $summary['critical_no'] + $summary['general_no'];
        return $summary;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD TEMPLATE
    // ─────────────────────────────────────────────────────────────────────────

    public function insert_template($data)
    {
        $this->db->insert('checklist_template', $data);
        return $this->db->insert_id();
    }

    public function update_template($id, $data)
    {
        return $this->db->where('id_template', $id)->update('checklist_template', $data);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD ITEM
    // ─────────────────────────────────────────────────────────────────────────

    public function insert_item($data)
    {
        $this->db->insert('checklist_item', $data);
        return $this->db->insert_id();
    }

    public function update_item($id, $data)
    {
        return $this->db->where('id_item', $id)->update('checklist_item', $data);
    }

    public function delete_item($id)
    {
        return $this->db->where('id_item', $id)->delete('checklist_item');
    }

    public function get_item($id)
    {
        return $this->db->where('id_item', $id)->get('checklist_item')->row();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COMPAT
    // ─────────────────────────────────────────────────────────────────────────

    /** @deprecated Gunakan get_template_by_tipe_id() */
    public static function map_jenis($nama_tipe)
    {
        $no_template = ['Water Truck'];
        if (in_array($nama_tipe, $no_template)) return null;
        return $nama_tipe;
    }

    public function get_tipe_doc_info($id_tipe_kendaraan)
    {
        return $this->db
            ->select('id_tipe_kendaraan, nama_tipe, kode_tipe,
                  doc_no, title_id, title_en,
                  doc_name_id, doc_name_en,
                  tgl_terbit, tgl_review, no_revisi')
            ->where('id_tipe_kendaraan', (int) $id_tipe_kendaraan)
            ->get('tipe_kendaraan')
            ->row();
    }
}
