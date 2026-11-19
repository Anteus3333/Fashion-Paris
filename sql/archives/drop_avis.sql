-- Retire la fonction avis (Fashion n’utilise plus les avis clients)
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `avis`;
SET FOREIGN_KEY_CHECKS = 1;
