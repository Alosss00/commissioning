-- Set akun 'tms.hendrik' agar login menggunakan otentikasi LDAP Active Directory (ARCHIMINING)
UPDATE `users` 
SET `auth_source` = 'ldap' 
WHERE `username` = 'tms.hendrik' OR `email` LIKE 'tms.hendrik%';

-- Jika user belum ada di tabel users, gunakan query berikut untuk menambahkan akun baru (email dikosongkan/NULL):
INSERT INTO `users` (`id_role`, `nama`, `username`, `email`, `auth_source`, `is_active`)
VALUES (1, 'Hendrik', 'tms.hendrik', NULL, 'ldap', 1);
