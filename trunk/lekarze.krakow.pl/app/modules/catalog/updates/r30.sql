ALTER TABLE `catalog_type` ADD COLUMN `subdomain` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `alias`;

