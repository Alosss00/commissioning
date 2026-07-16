<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
*/

// =========================
// DEFAULT
// =========================
$route['default_controller']   = 'auth';
$route['404_override']         = 'errors/missing';
$route['translate_uri_dashes'] = FALSE;


// =========================
// DASHBOARD
// =========================
$route['dashboard']                          = 'Dashboard/index';
$route['dashboard/rekap_commissioning']      = 'Dashboard/rekap_commissioning';

// =========================
// PERBAIKAN
// =========================
$route['perbaikan/form/(:num)'] = 'Perbaikan/form/$1';
$route['perbaikan/store']       = 'Perbaikan/store';
// Catatan: verifikasi fisik perbaikan sekarang ditangani oleh Approval/verif_perbaikan
// Method Perbaikan::verifikasi() dan Perbaikan::acc_verifikasi() sudah tidak dipakai


// =========================
// PENGAJUAN
// =========================
$route['pengajuan']                     = 'Pengajuan/index';
$route['pengajuan/create']              = 'Pengajuan/create';
$route['pengajuan/store']               = 'Pengajuan/store';
$route['pengajuan/resubmit']            = 'Pengajuan/resubmit';
$route['pengajuan/get_data']            = 'Pengajuan/get_data';
$route['pengajuan/detail/(:num)']       = 'Pengajuan/detail/$1';
$route['pengajuan/get_kendaraan_info']  = 'Pengajuan/get_kendaraan_info';


// =========================
// KENDARAAN
// =========================
$route['kendaraan']                     = 'Kendaraan/index';
$route['kendaraan/get_data']            = 'Kendaraan/get_data';
$route['kendaraan/get_by_id']           = 'Kendaraan/get_by_id';
$route['kendaraan/get_tipe_list']       = 'Kendaraan/get_tipe_list';
$route['kendaraan/get_dropdown']        = 'Kendaraan/get_dropdown';
$route['kendaraan/save']                = 'Kendaraan/save';
$route['kendaraan/delete']              = 'Kendaraan/delete';
$route['kendaraan/get_rekap']           = 'Kendaraan/get_rekap';
$route['kendaraan/get_all_for_export']  = 'Kendaraan/get_all_for_export';

// =========================
// APPROVAL
// =========================
$route['approval/manager']              = 'Approval/manager';
$route['approval/admin_ohs']            = 'Approval/admin_ohs';
$route['approval/ohs_supt']             = 'Approval/ohs_supt';
$route['approval/ktt']                  = 'Approval/ktt';
$route['approval/stiker']               = 'Approval/stiker';
$route['approval/proses']               = 'Approval/proses';
$route['approval/get_detail_stiker']    = 'Approval/get_detail_stiker';
$route['approval/cabut_stiker']         = 'Approval/cabut_stiker';
// Inspektor verifikasi fisik perbaikan (status masuk: siap_verifikasi)
// ACC → inspeksi_ulang | Tolak → tidak_lulus_inspeksi
$route['approval/verif_perbaikan']      = 'Approval/verif_perbaikan';
$route['approval/detail/(:any)/(:num)'] = 'Approval/detail/$1/$2';


// =========================
// USER MANAGEMENT
// =========================
$route['usermanagement']                = 'UserManagement/index';
$route['usermanagement/get_data']       = 'UserManagement/get_data';
$route['usermanagement/get_detail']     = 'UserManagement/get_detail';
$route['usermanagement/save']           = 'UserManagement/save';
$route['usermanagement/toggle_active']  = 'UserManagement/toggle_active';
$route['usermanagement/delete']         = 'UserManagement/delete';


// =========================
// PROFIL
// =========================
$route['profil']                = 'Profil/index';
$route['profil/update']         = 'Profil/update';
$route['profil/update_foto']    = 'Profil/update_foto';
$route['profil/ganti_password'] = 'Profil/ganti_password';


// =========================
// HAK AKSES
// =========================
$route['hakakses'] = 'HakAkses/index';


// =========================
// JADWAL
// =========================
$route['jadwal']                        = 'Jadwal/index';
$route['jadwal/create/(:num)']          = 'Jadwal/create/$1';
$route['jadwal/store']                  = 'Jadwal/store';
$route['jadwal/edit/(:num)']            = 'Jadwal/edit/$1';
$route['jadwal/cancel']                 = 'Jadwal/cancel';
$route['jadwal/detail']                 = 'Jadwal/detail';
$route['jadwal/cek_konflik_inspektor']  = 'Jadwal/cek_konflik_inspektor';
$route['jadwal/get_by_date']            = 'Jadwal/get_by_date';


// =========================
// MASTER MEKANIK
// =========================
$route['mekanik_master']                = 'Mekanik/index';
$route['mekanik_master/form']           = 'Mekanik/form';
$route['mekanik_master/form/(:num)']    = 'Mekanik/form/$1';
$route['mekanik_master/save']           = 'Mekanik/save';
$route['mekanik_master/toggle']         = 'Mekanik/toggle';
$route['mekanik_master/delete']         = 'Mekanik/delete';
$route['mekanik_master/get_available']  = 'Mekanik/get_available';


// =========================
// CHECKLIST
// =========================
$route['checklist/detail/(:num)'] = 'Checklist/detail/$1';
$route['checklist/form/(:num)']   = 'Checklist/form/$1';
$route['checklist/pdf/(:num)']    = 'Checklist/pdf/$1';


// =========================
// TIPE KENDARAAN
// =========================
$route['tipekendaraan']                 = 'TipeKendaraan/index';
$route['tipekendaraan/get_data']        = 'TipeKendaraan/get_data';
$route['tipekendaraan/save']            = 'TipeKendaraan/save';
$route['tipekendaraan/toggle']          = 'TipeKendaraan/toggle';
$route['tipekendaraan/delete']          = 'TipeKendaraan/delete';
$route['tipekendaraan/get_dropdown']    = 'TipeKendaraan/get_dropdown';


// =========================
// MEKANIK (alias)
// =========================
$route['mekanik'] = 'Mekanik/index';
