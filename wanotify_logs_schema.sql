-- MariaDB dump 10.19  Distrib 10.5.19-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: wanotify_logs
-- ------------------------------------------------------
-- Server version	10.5.19-MariaDB-0+deb11u2-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `android_cloud_data_daily`
--

DROP TABLE IF EXISTS `android_cloud_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `android_cloud_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `android_data_daily`
--

DROP TABLE IF EXISTS `android_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `android_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `android_data_hourly`
--

DROP TABLE IF EXISTS `android_data_hourly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `android_data_hourly` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_data_daily`
--

DROP TABLE IF EXISTS `case_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `en_data_hourly`
--

DROP TABLE IF EXISTS `en_data_hourly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `en_data_hourly` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encv_api_key_data_daily`
--

DROP TABLE IF EXISTS `encv_api_key_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encv_api_key_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encv_bulk_data_daily`
--

DROP TABLE IF EXISTS `encv_bulk_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encv_bulk_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encv_code_data_daily`
--

DROP TABLE IF EXISTS `encv_code_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encv_code_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encv_error_data_daily`
--

DROP TABLE IF EXISTS `encv_error_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encv_error_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `encv_issuance_method_data_daily`
--

DROP TABLE IF EXISTS `encv_issuance_method_data_daily`;
/*!50001 DROP VIEW IF EXISTS `encv_issuance_method_data_daily`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `encv_issuance_method_data_daily` AS SELECT
 1 AS `date`,
  1 AS `data` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `encv_key_data_daily`
--

DROP TABLE IF EXISTS `encv_key_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encv_key_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encv_user_data_daily`
--

DROP TABLE IF EXISTS `encv_user_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encv_user_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enpa_1d_data_daily`
--

DROP TABLE IF EXISTS `enpa_1d_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enpa_1d_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enpa_7d_data_daily`
--

DROP TABLE IF EXISTS `enpa_7d_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enpa_7d_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ios_data_daily`
--

DROP TABLE IF EXISTS `ios_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ios_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ios_data_hourly`
--

DROP TABLE IF EXISTS `ios_data_hourly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ios_data_hourly` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `landing_page_data_daily`
--

DROP TABLE IF EXISTS `landing_page_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_page_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `landing_page_data_hourly`
--

DROP TABLE IF EXISTS `landing_page_data_hourly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_page_data_hourly` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matomo_action_data_daily`
--

DROP TABLE IF EXISTS `matomo_action_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matomo_action_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matomo_event_data_daily`
--

DROP TABLE IF EXISTS `matomo_event_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matomo_event_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `text_info_data_hourly`
--

DROP TABLE IF EXISTS `text_info_data_hourly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `text_info_data_hourly` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_logs`
--

DROP TABLE IF EXISTS `update_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(28) NOT NULL,
  `date` datetime NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `old_diff` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_diff` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7435748 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web_counter_data_daily`
--

DROP TABLE IF EXISTS `web_counter_data_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_counter_data_daily` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web_counter_data_hourly`
--

DROP TABLE IF EXISTS `web_counter_data_hourly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_counter_data_hourly` (
  `date` datetime NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci `PAGE_COMPRESSED`='ON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `encv_issuance_method_data_daily`
--

/*!50001 DROP VIEW IF EXISTS `encv_issuance_method_data_daily`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`wanotify_logs`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `encv_issuance_method_data_daily` AS select `encv_code_data_daily`.`date` AS `date`,json_insert(`encv_api_key_data_daily`.`data`,'$.codes_issued',json_value(`encv_code_data_daily`.`data`,'$.codes_issued'),'$.user_reports_issued',json_value(`encv_code_data_daily`.`data`,'$.user_reports_issued'),'$.Total_User_Issuance',json_value(`encv_user_data_daily`.`data`,'$.Total_User_Issuance'),'$.Max_User_Issuance',json_value(`encv_user_data_daily`.`data`,'$.Max_User_Issuance')) AS `data` from ((`encv_code_data_daily` join `encv_api_key_data_daily` on(`encv_code_data_daily`.`date` = `encv_api_key_data_daily`.`date`)) join `encv_user_data_daily` on(`encv_code_data_daily`.`date` = `encv_user_data_daily`.`date`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-07-10 12:35:08
