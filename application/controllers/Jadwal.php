<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Jadwal Controller
 * Roles: 1=Super Admin, 5=Admin OHS
 *
 * Status pengajuan yang relevan:
 *  - 'dijadwalkan'  → pengajuan sudah disetujui Admin OHS, siap dibuat jadwal
 *                     (juga dipakai saat jadwal sudah dibuat — pengajuan tetap 'dijadwalkan' sampai mekanik selesai)
 *  - 'selesai_inspeksi' → mekanik sudah isi form (dicatat oleh controller Checklist)
 *
 * Status jadwal_uji.status (tabel jadwal — TIDAK BERUBAH):
 *  - 'scheduled'  → jadwal aktif
 *  - 'done'       → inspeksi selesai
 *  - 'cancelled'  → dibatalkan
 */
class Jadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Jadwal_model' => 'jadwal_model', 'Pengajuan_model' => 'pengajuan_model', 'Mekanik_Model' => 'mekanik_model']);
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) redirect('auth/login');

        // Hanya Admin OHS (5) dan Super Admin (1)
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5, 8], $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }
    }

    // =============================================
    // INDEX — daftar & kalender jadwal
    // =============================================
    public function index()
    {
        $filter = [
            'status' => $this->input->get('status'),
            'bulan'  => $this->input->get('bulan'),
            'tahun'  => $this->input->get('tahun') ?: date('Y'),
        ];

        $jadwals  = $this->jadwal_model->get_all($filter);

        // Pengajuan berstatus 'dijadwalkan' yang BELUM punya jadwal aktif
        $menunggu_jadwal = $this->db
            ->select('pu.id_pengajuan, pu.tanggal_pengajuan, k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, u.nama AS nama_pemohon')
            ->from('pengajuan_uji pu')
            ->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('users u',          'u.id_user = pu.id_pemohon')
            ->where('pu.status', 'dijadwalkan')
            ->where('NOT EXISTS (SELECT 1 FROM jadwal_uji j WHERE j.id_pengajuan = pu.id_pengajuan AND j.status = "scheduled")', null, false)
            ->order_by('pu.tanggal_pengajuan', 'ASC')
            ->get()->result();
        // Data untuk kalender FullCalendar
        $events = [];
        foreach ($jadwals as $j) {
            $color = $j->status === 'scheduled' ? '#4154f1'
                : ($j->status === 'done'      ? '#2eca6a' : '#dc3545');
            $events[] = [
                'id'    => $j->id_jadwal,
                'title' => $j->no_polisi . ' — ' . $j->jenis_kendaraan,
                'start' => date('Y-m-d\TH:i:s', strtotime($j->tanggal_uji)),
                'color' => $color,
                'extendedProps' => [
                    'id_jadwal'  => $j->id_jadwal,
                    'no_polisi'  => $j->no_polisi,
                    'jenis'      => $j->jenis_kendaraan,
                    'pemohon'    => $j->nama_pemohon,
                    'lokasi'     => $j->lokasi,
                    'status'     => $j->status,
                    'keterangan' => $j->keterangan,
                ],
            ];
        }

        $data = [
            'title'           => 'Jadwal Inspeksi',
            'user'            => $this->session->userdata(),
            'jadwals'         => $jadwals,
            'filter'          => $filter,
            'menunggu_jadwal' => $menunggu_jadwal,
            'events_json'     => json_encode($events),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jadwal/index',      $data);
        $this->load->view('templates/footer',  $data);
    }

    // =============================================
    // CREATE — form buat jadwal dari id_pengajuan
    // =============================================
    public function create($id_pengajuan = null)
    {
        $id_pengajuan = (int) $id_pengajuan;
        $pengajuan    = $this->pengajuan_model->get_detail($id_pengajuan);

        // Status yang diperbolehkan masuk form jadwal
        // 'dijadwalkan' = sudah disetujui Admin OHS, belum/sudah punya jadwal (bisa reschedule)
        $status_boleh = ['dijadwalkan'];

        if (!$pengajuan || !in_array($pengajuan->status, $status_boleh)) {
            $this->session->set_flashdata(
                'error',
                'Pengajuan tidak ditemukan atau belum disetujui Admin OHS. '
                    . 'Status saat ini: <strong>' . ($pengajuan->status ?? 'tidak ada') . '</strong>'
            );
            redirect('jadwal');
        }

        // Cek apakah sudah ada jadwal aktif
        $existing = $this->jadwal_model->get_by_pengajuan_aktif($id_pengajuan);

        $data = [
            'title'      => 'Buat Jadwal Inspeksi',
            'user'       => $this->session->userdata(),
            'pengajuan'  => $pengajuan,
            'existing'   => $existing,
            // Mekanik lapangan dari master, difilter sesuai jenis kendaraan
            'mekaniks'   => $this->jadwal_model->get_mekanik_by_jenis($pengajuan->jenis_kendaraan),
            // Inspektor dari users (role 4)
            'inspektors' => $this->jadwal_model->get_inspektor(),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jadwal/form',       $data);
        $this->load->view('templates/footer',  $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id_pengajuan        = (int) $this->input->post('id_pengajuan');
        $tanggal_uji         = $this->input->post('tanggal_uji');
        $lokasi              = trim($this->input->post('lokasi'));
        $id_mekanik_master   = (int) $this->input->post('id_mekanik_master');
        $id_inspektor        = (int) $this->input->post('id_inspektor');
        $keterangan          = trim($this->input->post('keterangan'));
        $id_jadwal           = (int) $this->input->post('id_jadwal');

        // Validasi dasar
        if (!$tanggal_uji || !$lokasi) {
            echo json_encode(['status' => 'error', 'message' => 'Tanggal dan lokasi wajib diisi.']);
            return;
        }
        if (!$id_mekanik_master) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih mekanik lapangan.']);
            return;
        }
        if (!$id_inspektor) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih inspektor (user sistem).']);
            return;
        }

        if (strtotime($tanggal_uji) < strtotime('today')) {
            echo json_encode(['status' => 'error', 'message' => 'Tanggal tidak boleh di masa lalu.']);
            return;
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan || $pengajuan->status !== 'dijadwalkan') {
            echo json_encode(['status' => 'error', 'message' => 'Pengajuan tidak valid atau statusnya tidak sesuai.']);
            return;
        }

        // Cek konflik inspektor (user role 4) — selisih minimal 60 menit
        if ($this->jadwal_model->cek_konflik_inspektor($tanggal_uji, $id_inspektor, $id_jadwal ?: null)) {
            echo json_encode(['status' => 'error', 'message' => 'Inspektor sudah memiliki jadwal dalam rentang 1 jam di waktu yang sama.']);
            return;
        }

        // Cek konflik mekanik master — selisih minimal 60 menit
        if ($this->jadwal_model->cek_konflik_mekanik($tanggal_uji, $id_mekanik_master, $id_jadwal ?: null)) {
            echo json_encode(['status' => 'error', 'message' => 'Mekanik lapangan sudah memiliki jadwal dalam rentang 1 jam di waktu yang sama.']);
            return;
        }

        $payload = [
            'id_pengajuan'      => $id_pengajuan,
            'tanggal_uji'       => date('Y-m-d H:i:s', strtotime($tanggal_uji)),
            'lokasi'            => $lokasi,
            'id_mekanik'        => $id_inspektor,        // backward compat — id inspektor
            'id_mekanik_master' => $id_mekanik_master,   // mekanik lapangan baru
            'id_inspektor'      => $id_inspektor,        // eksplisit
            'keterangan'        => $keterangan,
            'status'            => 'scheduled',
            'dibuat_oleh'       => $this->session->userdata('id_user'),
        ];

        $this->db->trans_start();

        if ($id_jadwal) {
            $this->jadwal_model->update($id_jadwal, $payload);
        } else {
            $payload['created_at'] = date('Y-m-d H:i:s');
            $id_jadwal_baru = $this->jadwal_model->insert($payload);
        }

        // Kirim notif email ke inspektor (user role 4)
        $this->_notif_mekanik($id_inspektor, $id_pengajuan, $tanggal_uji, $lokasi);

        // Audit log
        $this->db->insert('audit_log', [
            'id_user'    => $this->session->userdata('id_user'),
            'aksi'       => $id_jadwal ? 'edit_jadwal' : 'buat_jadwal',
            'tabel'      => 'jadwal_uji',
            'id_ref'     => $id_jadwal ?: ($id_jadwal_baru ?? 0),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan jadwal.']);
            return;
        }

        echo json_encode([
            'status'  => 'success',
            'message' => 'Jadwal inspeksi berhasil ' . ($id_jadwal ? 'diperbarui' : 'disimpan') . '. Notifikasi dikirim ke inspektor.',
            'redirect' => site_url('jadwal'),
        ]);
    }

    // =============================================
    // EDIT — form edit jadwal
    // =============================================
    public function edit($id_jadwal = null)
    {
        $id_jadwal = (int) $id_jadwal;
        $jadwal    = $this->jadwal_model->get_by_id($id_jadwal);

        if (!$jadwal || $jadwal->status !== 'scheduled') {
            $this->session->set_flashdata('error', 'Jadwal tidak ditemukan atau tidak dapat diubah.');
            redirect('jadwal');
        }

        $pengajuan = $this->pengajuan_model->get_detail($jadwal->id_pengajuan);

        $data = [
            'title'      => 'Edit Jadwal Inspeksi',
            'user'       => $this->session->userdata(),
            'pengajuan'  => $pengajuan,
            'existing'   => $jadwal,
            'mekaniks'   => $this->jadwal_model->get_mekanik_by_jenis($pengajuan->jenis_kendaraan ?? null),
            'inspektors' => $this->jadwal_model->get_inspektor(),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jadwal/form',       $data);
        $this->load->view('templates/footer',  $data);
    }

    // =============================================
    // AJAX — cek konflik inspektor (user role 4)
    // Dipanggil dari views_jadwal_form.php per inspektor
    // =============================================
    public function cek_konflik_inspektor()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id_inspektor  = (int) $this->input->post('id_inspektor');
        $tanggal_uji   = $this->input->post('tanggal_uji');
        $exclude_id    = (int) $this->input->post('exclude_jadwal_id');

        if (!$id_inspektor || !$tanggal_uji) {
            echo json_encode(['konflik' => false]);
            return;
        }

        $konflik = $this->jadwal_model->cek_konflik_inspektor(
            $tanggal_uji,
            $id_inspektor,
            $exclude_id ?: null
        );
        echo json_encode(['konflik' => $konflik]);
    }

    // =============================================
    // AJAX — get jadwal di hari tertentu
    // Untuk ditampilkan di form jadwal sebagai referensi
    // =============================================
    public function get_by_date()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $tanggal = $this->input->post('tanggal');
        if (!$tanggal) {
            echo json_encode(['status' => 'success', 'data' => []]);
            return;
        }

        $rows = $this->jadwal_model->get_jadwal_on_date($tanggal);

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'waktu'           => date('H:i', strtotime($r->tanggal_uji)),
                'no_polisi'       => $r->no_polisi ?? '',
                'jenis_kendaraan' => $r->jenis_kendaraan ?? '',
                'nama_inspektor'  => $r->nama_inspektor ?? '—',
                'nama_mekanik'    => $r->nama_mekanik ?? '—',
            ];
        }
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    // =============================================
    // CANCEL — batalkan jadwal (AJAX)
    // Setelah dibatalkan, pengajuan kembali ke 'dijadwalkan'
    // supaya Admin OHS bisa buat jadwal baru
    // =============================================
    public function cancel()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id_jadwal = (int) $this->input->post('id_jadwal');
        $result    = $this->jadwal_model->cancel($id_jadwal);

        if ($result) {
            $this->db->insert('audit_log', [
                'id_user'    => $this->session->userdata('id_user'),
                'aksi'       => 'cancel_jadwal',
                'tabel'      => 'jadwal_uji',
                'id_ref'     => $id_jadwal,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dibatalkan. Pengajuan kembali ke antrian penjadwalan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membatalkan jadwal.']);
        }
    }

    // =============================================
    // DETAIL — AJAX popup kalender
    // =============================================
    public function detail()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id     = (int) $this->input->post('id_jadwal');
        $jadwal = $this->jadwal_model->get_by_id($id);
        echo json_encode(['status' => 'success', 'data' => $jadwal]);
    }

    // =============================================
    // PRIVATE — Notifikasi email ke mekanik
    // =============================================
    private function _notif_mekanik($id_inspektor, $id_pengajuan, $tanggal_uji, $lokasi)
    {
        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan) return;

        // Ambil info mekanik lapangan dari jadwal (jika sudah disimpan)
        $jadwal = $this->db
            ->select('j.*, mm.nama AS nama_mekanik_lap, mm.perusahaan AS perusahaan_mekanik')
            ->from('jadwal_uji j')
            ->join('mekanik_master mm', 'mm.id_mekanik = j.id_mekanik_master', 'left')
            ->where('j.id_pengajuan', $id_pengajuan)
            ->where('j.status', 'scheduled')
            ->get()->row();

        $nama_mekanik_lap  = $jadwal->nama_mekanik_lap ?? '—';
        $perusahaan_mekanik = $jadwal->perusahaan_mekanik ?? '—';

        $this->load->library('email');
        $from_email = $this->config->item('sikuk_email_from') ?: 'noreply@sikuk.app';
        $from_name  = $this->config->item('sikuk_email_name') ?: 'TACTIC System';

        $subject = '[TACTIC] Jadwal Inspeksi Baru — ' . $pengajuan->no_polisi;
        $body    = '<b>Jadwal Inspeksi Kelayakan Kendaraan</b><br><br>'
            . '<b>Kendaraan</b>&nbsp;&nbsp;&nbsp;: ' . htmlspecialchars($pengajuan->no_polisi) . ' — ' . htmlspecialchars($pengajuan->jenis_kendaraan) . '<br>'
            . '<b>Merk / Tipe</b> : ' . htmlspecialchars($pengajuan->merk) . ' ' . htmlspecialchars($pengajuan->tipe) . '<br>'
            . '<b>Tanggal</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . date('d M Y H:i', strtotime($tanggal_uji)) . ' WIB<br>'
            . '<b>Lokasi</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . htmlspecialchars($lokasi) . '<br>'
            . '<b>No. Pengajuan</b>: #PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT) . '<br>'
            . '<b>Mekanik</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . htmlspecialchars($nama_mekanik_lap) . ' (' . htmlspecialchars($perusahaan_mekanik) . ')<br><br>'
            . 'Harap memastikan mekanik lapangan siap pada jadwal yang telah ditentukan.<br><br>'
            . 'Terima kasih,<br><b>TACTIC System</b>';

        // Kirim ke semua Planner/Safety (role 8)
        $planners = $this->db
            ->select('u.id_user, u.nama, u.email')
            ->from('users u')
            ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
            ->group_start()
            ->where('ur.id_role', 8)
            ->or_where('u.id_role', 8)
            ->group_end()
            ->where('u.is_active', 1)
            ->where('u.email !=', '')
            ->group_by('u.id_user')
            ->get()->result();

        foreach ($planners as $p) {
            if (!$p->email) continue;
            $this->email->initialize(['mailtype' => 'html']);
            $this->email->from($from_email, $from_name);
            $this->email->to($p->email);
            $this->email->subject($subject);
            $this->email->message('Yth. ' . htmlspecialchars($p->nama) . ',<br><br>' . $body);
            @$this->email->send();
        }

        // Juga kirim ke inspektor sistem (role 4) yang login
        $inspektor = $this->db->where('id_user', $id_inspektor)->get('users')->row();
        if ($inspektor && $inspektor->email) {
            $this->email->initialize(['mailtype' => 'html']);
            $this->email->from($from_email, $from_name);
            $this->email->to($inspektor->email);
            $this->email->subject('[TACTIC] Tugas Inspeksi — ' . $pengajuan->no_polisi);
            $this->email->message(
                'Yth. ' . htmlspecialchars($inspektor->nama) . ',<br><br>'
                    . 'Anda dijadwalkan sebagai inspektor untuk:<br><br>' . $body
            );
            @$this->email->send();
        }
    }

    // =============================================
    // PRIVATE helpers
    // =============================================
    private function _user_roles()
    {
        $raw = $this->session->userdata('roles');
        if (is_array($raw) && !empty($raw)) return array_map('intval', $raw);
        $r = (int) $this->session->userdata('role');
        return $r > 0 ? [$r] : [];
    }

    private function _has_role(array $required, array $user_roles)
    {
        foreach ($required as $r) {
            if (in_array((int) $r, $user_roles)) return true;
        }
        return false;
    }
}
