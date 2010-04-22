ALTER TABLE `catalog_service` ADD COLUMN `subdomain` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `alias`;

