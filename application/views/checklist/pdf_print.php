<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Inspeksi — <?= html_escape($uji->no_polisi) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            color: #111;
            background: #fff;
            padding: 16px 20px;
        }

        /* ── HEADER ─────────────────────────────────────────────── */
        .doc-header {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-header td {
            border: 1.5px solid #333;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .logo-cell {
            width: 88px;
            text-align: center;
            padding: 5px !important;
        }

        .logo-cell img {
            max-width: 76px;
            max-height: 52px;
            object-fit: contain;
        }

        .title-cell {
            text-align: center;
        }

        .title-cell .proj {
            font-size: 10pt;
            font-weight: bold;
        }

        .title-cell .main {
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.4;
        }

        .title-cell .sub {
            font-size: 8.5pt;
            font-style: italic;
        }

        .docno-row td {
            border: 1.5px solid #333;
            border-top: none;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            padding: 3px 8px;
            background: #f9f9f9;
        }

        /* ── SECTION TITLE ──────────────────────────────────────── */
        .sec-title {
            background: #d9d9d9;
            font-weight: bold;
            font-size: 9pt;
            padding: 4px 8px;
            border: 1.5px solid #333;
            border-top: none;
        }

        /* ── SECTION 1 ──────────────────────────────────────────── */
        .s1 {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .s1 td {
            border: 1px solid #333;
            padding: 3px 7px;
            vertical-align: middle;
        }

        .s1 .lbl {
            background: #f2f2f2;
            font-weight: bold;
            white-space: nowrap;
        }

        .s1 .colon {
            width: 10px;
            text-align: center;
        }

        /* ── SECTION 2 ──────────────────────────────────────────── */
        .s2 {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .s2 td,
        .s2 th {
            border: 1px solid #333;
            padding: 3px 6px;
            vertical-align: middle;
        }

        .s2 th {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
        }

        .s2 .cat-row td {
            background: #bfbfbf;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        .s2 .ref-col {
            width: 40px;
            text-align: center;
        }

        .s2 .chk-col {
            width: 34px;
            text-align: center;
        }

        .s2 .rmk-col {
            width: 130px;
        }

        .row-no {
            background: #fff0f0;
        }

        .row-na {
            background: #f9f9f9;
        }

        .b-yes {
            background: #28a745;
            color: #fff;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }

        .b-no {
            background: #dc3545;
            color: #fff;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }

        .b-na {
            background: #6c757d;
            color: #fff;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }

        /* ── FAULT TABLE ────────────────────────────────────────── */
        .fault {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .fault td,
        .fault th {
            border: 1px solid #333;
            padding: 3px 6px;
        }

        .fault th {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
        }

        /* ── PASS / FAIL ────────────────────────────────────────── */
        .pf {
            width: 100%;
            border-collapse: collapse;
        }

        .pf td {
            border: 1px solid #333;
            padding: 4px 10px;
            font-weight: bold;
            font-size: 9.5pt;
            width: 50%;
        }

        /* ── SIGN TABLE ─────────────────────────────────────────── */
        .sign {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .sign td {
            border: 1px solid #333;
            padding: 4px 8px;
            vertical-align: top;
        }

        .sign .lbl {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }

        .sign-area {
            height: 46px;
        }

        /* ── SECTION 3 & 4 ──────────────────────────────────────── */
        .s34 {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .s34 td {
            border: 1px solid #333;
            padding: 3px 8px;
            vertical-align: top;
        }

        .s34 .lbl {
            background: #f2f2f2;
            font-weight: bold;
            white-space: nowrap;
            width: 90px;
        }

        .s34 th {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            border: 1px solid #333;
            padding: 3px 8px;
        }

        .sign-area-sm {
            height: 36px;
        }

        /* ── FOOTER ─────────────────────────────────────────────── */
        .doc-footer {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .doc-footer td {
            border: 1.5px solid #333;
            padding: 3px 7px;
            vertical-align: middle;
        }

        .doc-footer .lbl {
            background: #f2f2f2;
            font-weight: bold;
        }

        .gap {
            margin-top: 5px;
        }

        /* ── PRINT ──────────────────────────────────────────────── */
        .no-print {
            margin-bottom: 14px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 10mm 12mm;
                size: A4 portrait;
            }
        }
    </style>
</head>

<body>

    <?php
    /*
     * ── $doc sudah dikirim dari controller Checklist::pdf() ──────────────
     * Tidak ada lagi $doc_map hardcoded di sini.
     * Semua data dokumen (doc_no, title_id, title_en, dst.) diambil dari
     * tabel tipe_kendaraan melalui JOIN di controller, lalu di-pass sebagai
     * variabel $doc ke view ini.
     *
     * Jika tipe kendaraan baru ditambahkan di Master Tipe Kendaraan, PDF
     * otomatis memakai data yang baru — tanpa perlu edit file ini.
     *
     * Fallback sudah diset di controller:
     *   title_id  → 'DAFTAR PERIKSA UJI KELAYAKAN ' + nama_tipe (uppercase)
     *   title_en  → nama_tipe + ' Commissioning Checklist'
     *   doc_no    → 'TT-OHS-FRO-002'
     *   dll.
     * ─────────────────────────────────────────────────────────────────── */

    /* ── Helper vars ─────────────────────────────────────────────────── */
    $tipe_peng   = strtolower($uji->tipe_pengajuan ?? '');
    $is_new      = in_array($tipe_peng, ['baru', 'new', 'commissioning']);
    $is_6monthly = in_array($tipe_peng, ['6 bulanan', '6monthly', 'periodic', 'berkala']);

    $tipe_akses = strtolower($uji->tipe_akses ?? '');
    $is_pit     = in_array($tipe_akses, ['pit', 'pit access']);
    $is_nonpit  = in_array($tipe_akses, ['non pit', 'non pit access', 'non-pit']);

    $tgl_inspeksi = !empty($uji->updated_at)
        ? date('d M Y', strtotime($uji->updated_at))
        : (!empty($uji->created_at) ? date('d M Y', strtotime($uji->created_at)) : '—');

    $nm_mek     = $uji->nama_mekanik ?? '';
    $items_no   = $summary['items_no'] ?? [];
    ?>


    <!-- ── Print / Close ──────────────────────────────────────────────── -->
    <div class="no-print" style="display:flex;gap:8px;">
        <button onclick="window.print()"
            style="background:#dc3545;color:#fff;border:none;padding:7px 18px;
               border-radius:4px;cursor:pointer;font-size:11pt;">
            🖨 Print / Save PDF
        </button>
        <button onclick="window.close()"
            style="background:#6c757d;color:#fff;border:none;padding:7px 14px;
               border-radius:4px;cursor:pointer;">
            Tutup
        </button>
    </div>


    <!-- ════════════════════════════════════════════════════════════════════
     HEADER
     ════════════════════════════════════════════════════════════════════ -->
    <table class="doc-header">
        <tr>
            <!-- Logo Kiri: Archi Indonesia -->
            <td class="logo-cell">
                <img src="<?= base_url('assets/img/logo-archi.png') ?>"
                    alt="Archi Indonesia"
                    onerror="this.style.display='none'">
            </td>

            <!-- Judul Tengah — dari tipe_kendaraan.title_id / title_en -->
            <td class="title-cell">
                <div class="proj">TOKA TINDUNG PROJECT</div>
                <div class="main"><?= html_escape($doc['title_id']) ?></div>
                <div class="sub"><?= html_escape($doc['title_en']) ?></div>
            </td>

            <!-- Logo Kanan: MSM TTN -->
            <td class="logo-cell">
                <img src="<?= base_url('assets/img/logo-msm.png') ?>"
                    alt="MSM TTN"
                    onerror="this.style.display='none'">
            </td>
        </tr>
    </table>
    <table class="doc-header" style="border-top:none;">
        <tr class="docno-row">
            <!-- No. Dokumen — dari tipe_kendaraan.doc_no -->
            <td><?= html_escape($doc['doc_no']) ?></td>
        </tr>
    </table>


    <!-- ════════════════════════════════════════════════════════════════════
     SECTION 1 – VEHICLE DETAILS
     ════════════════════════════════════════════════════════════════════ -->
    <div class="sec-title gap">
        SECTION 1 – VEHICLE DETAILS
        <em>(To be completed prior to inspection)</em>
    </div>

    <table class="s1">
        <tr>
            <td class="lbl" style="width:22%;">Type of Inspection</td>
            <td class="colon">:</td>
            <td style="width:28%;">
                <?= $is_new      ? '☑' : '☐' ?> <em>New</em>
                &nbsp;&nbsp;&nbsp;
                <?= $is_6monthly ? '☑' : '☐' ?> <em>6 Monthly</em>
            </td>
            <td class="lbl" style="width:18%;">Unit Number</td>
            <td class="colon">:</td>
            <td><?= html_escape($uji->nomor_unit ?: '—') ?></td>
        </tr>
        <tr>
            <td class="lbl">Owner / Responsible Company</td>
            <td class="colon">:</td>
            <td><?= html_escape($uji->perusahaan ?? '—') ?></td>
            <td class="lbl">Police Number</td>
            <td class="colon">:</td>
            <td><?= html_escape($uji->no_polisi) ?></td>
        </tr>
        <tr>
            <td class="lbl">Vehicle Make, Model and Description</td>
            <td class="colon">:</td>
            <td colspan="4">
                <?= html_escape(trim(($uji->merk ?? '') . ' ' . ($uji->tipe_kendaraan ?? ''))) ?: '—' ?>
            </td>
        </tr>
        <tr>
            <td class="lbl">Manufactured Year <em>(max. 10 years)</em> and Kilometer</td>
            <td class="colon">:</td>
            <td><?= html_escape($uji->tahun ?? '—') ?></td>
            <td class="lbl">Location of Use</td>
            <td class="colon">:</td>
            <td>
                <?= $is_pit    ? '☑' : '☐' ?> <em>Pit Access</em>
                &nbsp;&nbsp;&nbsp;
                <?= $is_nonpit ? '☑' : '☐' ?> <em>Non Pit Access</em>
            </td>
        </tr>
        <tr>
            <td class="lbl">Duration On Site</td>
            <td class="colon">:</td>
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>


    <!-- ════════════════════════════════════════════════════════════════════
     SECTION 2 – INSPECTION
     ════════════════════════════════════════════════════════════════════ -->
    <div class="sec-title gap">
        SECTION 2 – INSPECTION
        <em>(to be completed by a PT MSM/TTN nominated Inspector)</em>
        &nbsp;&nbsp;
        <strong>Check for item inspected</strong>
    </div>

    <table class="s2">
        <thead>
            <tr>
                <th class="ref-col">REF.</th>
                <th>CRITERIA</th>
                <th class="chk-col">YES</th>
                <th class="chk-col">NO</th>
                <th class="rmk-col">Remark</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $kat_label = [
                'GENERAL'  => 'GENERAL REQUIREMENTS',
                'CRITICAL' => 'CRITICAL ITEMS (***)',
            ];
            foreach (['GENERAL', 'CRITICAL'] as $kat):
                if (empty($grouped[$kat])) continue;
            ?>
                <tr class="cat-row">
                    <td colspan="5"><?= $kat_label[$kat] ?></td>
                </tr>
                <?php foreach ($grouped[$kat] as $item):
                    $row_cls = $item->hasil === 'no' ? 'row-no' : ($item->hasil === 'na' ? 'row-na' : '');
                ?>
                    <tr class="<?= $row_cls ?>">
                        <td class="ref-col"><?= html_escape($item->no_urut) ?></td>
                        <td>
                            <?= html_escape($item->kriteria) ?>
                            <?php if ($kat === 'CRITICAL'): ?><strong>***</strong><?php endif; ?>
                        </td>
                        <td class="chk-col">
                            <?= $item->hasil === 'yes' ? '<span class="b-yes">YES</span>' : '' ?>
                        </td>
                        <td class="chk-col">
                            <?= $item->hasil === 'no'  ? '<span class="b-no">NO</span>'  : '' ?>
                        </td>
                        <td class="rmk-col">
                            <?php if ($item->hasil === 'na'): ?>
                                <span class="b-na">N/A</span>&nbsp;
                            <?php endif; ?>
                            <span style="font-size:8pt;color:#444;">
                                <?= html_escape($item->keterangan ?? '') ?>
                            </span>
                        </td>
                    </tr>
            <?php endforeach;
            endforeach; ?>

        </tbody>
    </table>

    <p style="font-size:8pt;margin:2px 0 5px 1px;">
        <em>Catatan: *** = Critical Item</em>
    </p>


    <!-- ── Fault Table ───────────────────────────────────────────────── -->
    <table class="fault">
        <thead>
            <tr>
                <th style="width:40px;">Ref.</th>
                <th>Fault Description</th>
                <th style="width:110px;">Complete by</th>
                <th style="width:88px;">Date Complete</th>
                <th style="width:88px;">Verified by</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items_no)): ?>
                <?php foreach ($items_no as $fn): ?>
                    <tr>
                        <td style="text-align:center;"><?= html_escape($fn->no_urut) ?></td>
                        <td><?= html_escape($fn->kriteria) ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Notes -->
    <table class="fault" style="border-top:none;">
        <tr>
            <td>
                <strong><em>Notes:</em></strong>&nbsp;
                <?= html_escape($uji->catatan_temuan ?? $uji->catatan_umum ?? '') ?>
            </td>
        </tr>
    </table>

    <!-- Pass / Fail -->
    <table class="pf gap">
        <tr>
            <td style="color:<?= $summary['lulus'] ? '#155724' : '#aaa' ?>;">
                <?= $summary['lulus'] ? '☑' : '☐' ?>&nbsp;<strong>Pass Commissioning</strong>
            </td>
            <td style="color:<?= !$summary['lulus'] ? '#721c24' : '#aaa' ?>;">
                <?= !$summary['lulus'] ? '☑' : '☐' ?>&nbsp;<strong>Fail Commissioning</strong>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan Section 2 -->
    <table class="sign gap">
        <tr>
            <td class="lbl" style="width:33.33%;">INSPECTOR</td>
            <td class="lbl" style="width:33.33%;">Mechanic</td>
            <td class="lbl" style="width:33.33%;">OHS Comm. Coordinator</td>
        </tr>
        <tr>
            <td>
                <div class="sign-area"></div>
                <strong>Name:</strong> <?= html_escape($uji->nama_inspektor ?? '—') ?>
            </td>
            <td>
                <div class="sign-area"></div>
                <strong>Name:</strong> <?= html_escape($nm_mek ?: '—') ?>
            </td>
            <td>
                <div class="sign-area"></div>
                <strong>Name:</strong>
            </td>
        </tr>
        <tr>
            <td><strong>I/D:</strong></td>
            <td><strong>I/D:</strong></td>
            <td><strong>I/D:</strong></td>
        </tr>
        <tr>
            <td><strong>Signature:</strong></td>
            <td><strong>Signature:</strong></td>
            <td><strong>Signature:</strong></td>
        </tr>
        <tr>
            <td><strong>Date:</strong> <?= $tgl_inspeksi ?></td>
            <td><strong>Date:</strong></td>
            <td><strong>Date:</strong></td>
        </tr>
    </table>


    <!-- ════════════════════════════════════════════════════════════════════
     SECTION 3 – ACKNOWLEDGE BY OHS Superintendent / Manager
     ════════════════════════════════════════════════════════════════════ -->
    <div class="sec-title gap">
        SECTION 3 – Acknowledge By
        <em>(OHS Superintendent / Manager)</em>
    </div>

    <table class="s34">
        <tr>
            <td class="lbl">Catatan / <em>Notes:</em></td>
            <td colspan="3" style="height:22px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Name:</td>
            <td style="width:38%;">
                <div class="sign-area-sm"></div>
            </td>
            <td style="width:12%;">&nbsp;</td>
            <td style="width:38%;">&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Signature:</td>
            <td style="height:36px;">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Date:</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl"><em>Notes:</em></td>
            <td colspan="3" style="height:18px;">&nbsp;</td>
        </tr>
    </table>


    <!-- ════════════════════════════════════════════════════════════════════
     SECTION 4 – APPROVAL (Kepala Teknik Tambang)
     ════════════════════════════════════════════════════════════════════ -->
    <div class="sec-title gap">
        SECTION 4 – APPROVAL
        <em>(to be completed by the Kepala Teknik Tambang or delegate as appropriate)</em>
    </div>

    <table class="s34">
        <thead>
            <tr>
                <td style="width:15%;border:1px solid #333;">&nbsp;</td>
                <th style="width:42.5%;">MSM</th>
                <th style="width:42.5%;">TTN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="lbl">Name:</td>
                <td>
                    <div class="sign-area-sm"></div>
                </td>
                <td>
                    <div class="sign-area-sm"></div>
                </td>
            </tr>
            <tr>
                <td class="lbl">Signature:</td>
                <td style="height:36px;">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="lbl">Date:</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="lbl"><em>Notes:</em></td>
                <td colspan="2" style="height:18px;">&nbsp;</td>
            </tr>
        </tbody>
    </table>


    <!-- ════════════════════════════════════════════════════════════════════
     FOOTER — semua data dari $doc (tipe_kendaraan)
     ════════════════════════════════════════════════════════════════════ -->
    <table class="doc-footer gap">
        <tr>
            <td class="lbl" style="width:22%;">
                Nama Dokumen / <em>Document Name</em>
            </td>
            <td colspan="3">
                <?= html_escape($doc['doc_name_id']) ?> /
                <em><?= html_escape($doc['doc_name_en']) ?></em>
            </td>
        </tr>
        <tr>
            <td class="lbl">Ditetapkan Oleh / <em>Determined By</em></td>
            <td style="width:28%;">
                Kepala Teknik Tambang / <em>Mining Technical Head</em>
            </td>
            <td class="lbl" style="width:18%;">
                Tanggal Terbit / <em>Date of Issue</em>
            </td>
            <td style="width:18%;"><?= html_escape($doc['tgl_terbit']) ?></td>
        </tr>
        <tr>
            <td class="lbl">No Dokumen / <em>Document No</em></td>
            <td><?= html_escape($doc['doc_no']) ?></td>
            <td class="lbl">Tanggal Tinjau Ulang / <em>Review Date</em></td>
            <td><?= html_escape($doc['tgl_review']) ?></td>
        </tr>
        <tr>
            <td class="lbl">No Revisi</td>
            <td><?= html_escape($doc['no_revisi']) ?></td>
            <td class="lbl" style="font-size:7pt;font-style:italic;">
                Dokumen terkendali dan valid hanya ada di sharepoint Archi Indonesia
            </td>
            <td>Halaman 1 dari 2</td>
        </tr>
    </table>

</body>

</html>