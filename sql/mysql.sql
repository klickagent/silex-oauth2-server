# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.31-0ubuntu0.14.04.2)
# Database: locopoly
# Generation Time: 2016-08-13 19:55:27 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table oauth_access_token
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_access_token`;

CREATE TABLE `oauth_access_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` varchar(1000) NOT NULL DEFAULT '',
  `access_token_expires` int(10) unsigned NOT NULL,
  `client_id` char(40) NOT NULL DEFAULT '',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table oauth_authcode
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_authcode`;

CREATE TABLE `oauth_authcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code` varchar(1000) NOT NULL DEFAULT '',
  `auth_code_expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table oauth_client
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_client`;

CREATE TABLE `oauth_client` (
  `id` char(40) NOT NULL,
  `secret` char(40) NOT NULL,
  `name` varchar(255) NOT NULL,
  `auto_approve` tinyint(1) NOT NULL DEFAULT '0',
  `redirect_uri` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_oacl_clse_clid` (`secret`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table oauth_refresh_token
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_refresh_token`;

CREATE TABLE `oauth_refresh_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(1000) NOT NULL DEFAULT '',
  `refresh_token_expires` int(10) unsigned NOT NULL,
  `access_token_id` char(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table oauth_scope
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_scope`;

CREATE TABLE `oauth_scope` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_oasc_sc` (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
