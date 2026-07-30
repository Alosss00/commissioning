-- Adds LDAP support to the existing `users` table.
-- Migration generated on 2026-07-30

ALTER TABLE `users`
	ADD COLUMN `auth_source` ENUM('local','ldap') NOT NULL DEFAULT 'local' AFTER `password`,
	ADD COLUMN `ldap_dn` VARCHAR(255) NULL DEFAULT NULL AFTER `auth_source`;
