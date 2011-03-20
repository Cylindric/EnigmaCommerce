-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: enigma
-- ------------------------------------------------------
-- Server version	5.1.49-1ubuntu8.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `store_category`
--

DROP TABLE IF EXISTS `store_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_category` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryTypeID` int(11) NOT NULL DEFAULT '1',
  `CategoryName` varchar(100) NOT NULL DEFAULT '',
  `Description` text NOT NULL,
  `ParentID` int(11) NOT NULL DEFAULT '0',
  `StockCodePrefix` int(11) NOT NULL DEFAULT '0',
  `WebView` tinyint(1) NOT NULL DEFAULT '-1',
  `ListType` int(11) NOT NULL DEFAULT '0',
  `CatalogueView` tinyint(1) NOT NULL DEFAULT '-1',
  `CatalogueSequence` int(11) NOT NULL DEFAULT '0',
  `WebSequence` int(10) unsigned NOT NULL DEFAULT '0',
  `ExternalID` int(11) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`CategoryID`),
  KEY `IDXParentID` (`ParentID`)
) ENGINE=MyISAM AUTO_INCREMENT=405 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_config`
--

DROP TABLE IF EXISTS `store_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_config` (
  `ConfigKey` varchar(255) NOT NULL DEFAULT '',
  `ConfigValue` varchar(255) NOT NULL DEFAULT '',
  `KeyType` varchar(45) NOT NULL DEFAULT 'string',
  PRIMARY KEY (`ConfigKey`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_customer`
--

DROP TABLE IF EXISTS `store_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_customer` (
  `CustomerID` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(100) NOT NULL DEFAULT '',
  `LastName` varchar(100) NOT NULL DEFAULT '',
  `InvAddress1` varchar(100) NOT NULL DEFAULT '',
  `InvAddress2` varchar(100) NOT NULL DEFAULT '',
  `InvAddress3` varchar(100) NOT NULL DEFAULT '',
  `InvTown` varchar(100) NOT NULL DEFAULT '',
  `InvCounty` varchar(100) NOT NULL DEFAULT '',
  `InvPostcode` varchar(10) NOT NULL DEFAULT '',
  `InvCountry` varchar(100) NOT NULL DEFAULT '',
  `DelAddress1` varchar(100) NOT NULL DEFAULT '',
  `DelAddress2` varchar(100) NOT NULL DEFAULT '',
  `DelAddress3` varchar(100) NOT NULL DEFAULT '',
  `DelTown` varchar(100) NOT NULL DEFAULT '',
  `DelCounty` varchar(100) NOT NULL DEFAULT '',
  `DelPostcode` varchar(10) NOT NULL DEFAULT '',
  `DelCountry` varchar(100) NOT NULL DEFAULT '',
  `Email` varchar(100) NOT NULL DEFAULT '',
  `DayPhone` varchar(100) NOT NULL DEFAULT '',
  `ccInfoAvailable` tinyint(1) NOT NULL DEFAULT '-1',
  `ccType` blob NOT NULL,
  `ccName` blob NOT NULL,
  `ccNo` blob NOT NULL,
  `ccExpMonth` blob NOT NULL,
  `ccExpYear` blob NOT NULL,
  `ccIssMonth` blob NOT NULL,
  `ccIssYear` blob NOT NULL,
  `ccIssue` blob NOT NULL,
  `ccCode` blob NOT NULL,
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`CustomerID`)
) ENGINE=MyISAM AUTO_INCREMENT=2879 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_detail`
--

DROP TABLE IF EXISTS `store_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_detail` (
  `DetailID` int(11) NOT NULL AUTO_INCREMENT,
  `DetailName` varchar(100) NOT NULL DEFAULT '',
  `ReportName` varchar(100) NOT NULL DEFAULT '',
  `ItemID` int(11) NOT NULL DEFAULT '0',
  `RetailPrice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `WebPrice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `RecommendedPrice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `OverridePrice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `OverrideStart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `OverrideEnd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UnitID` int(11) NOT NULL DEFAULT '0',
  `Size` double(10,2) NOT NULL DEFAULT '0.00',
  `WebView` tinyint(1) NOT NULL DEFAULT '-1',
  `WebBuy` tinyint(1) NOT NULL DEFAULT '-1',
  `CatalogueView` tinyint(1) NOT NULL DEFAULT '-1',
  `CatalogueBuy` tinyint(1) NOT NULL DEFAULT '-1',
  `CategoryView` tinyint(1) NOT NULL DEFAULT '0',
  `SoldOut` tinyint(1) NOT NULL DEFAULT '0',
  `StockCode` int(11) NOT NULL DEFAULT '0',
  `StockChecked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DimHeight` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `DimWidth` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `DimLength` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `DimWeight` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `DimVolume` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `UnitIDHeight` int(11) NOT NULL DEFAULT '0',
  `UnitIDWidth` int(11) NOT NULL DEFAULT '0',
  `UnitIDLength` int(11) NOT NULL DEFAULT '0',
  `UnitIDWeight` int(11) NOT NULL DEFAULT '0',
  `UnitIDVolume` int(11) NOT NULL DEFAULT '0',
  `ExternalID` int(11) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`DetailID`),
  KEY `IDXItemID` (`ItemID`)
) ENGINE=MyISAM AUTO_INCREMENT=3410 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_detailpostagecharge`
--

DROP TABLE IF EXISTS `store_detailpostagecharge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_detailpostagecharge` (
  `DetailID` int(11) NOT NULL DEFAULT '0',
  `PostageChargeID` int(11) NOT NULL DEFAULT '0',
  `PalletThreshold` int(11) NOT NULL DEFAULT '0',
  `PostageCharge` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`DetailID`,`PostageChargeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_detailsupplier`
--

DROP TABLE IF EXISTS `store_detailsupplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_detailsupplier` (
  `DetailID` int(11) NOT NULL DEFAULT '0',
  `SupplierID` int(11) NOT NULL DEFAULT '0',
  `Price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `BaseDiscount` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `ExtraDiscount` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `IsPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`SupplierID`,`DetailID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_history`
--

DROP TABLE IF EXISTS `store_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_history` (
  `HistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `EntityType` varchar(100) NOT NULL DEFAULT '',
  `EntityID` int(11) NOT NULL DEFAULT '0',
  `EntityName` varchar(100) NOT NULL DEFAULT '',
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SessionID` varchar(100) NOT NULL DEFAULT '',
  `UserAgent` varchar(100) NOT NULL DEFAULT '',
  `RemoteAddr` varchar(100) NOT NULL DEFAULT '',
  `ServerPort` varchar(100) NOT NULL DEFAULT '',
  `RequestURI` varchar(100) NOT NULL DEFAULT '',
  `UserReferer` varchar(100) NOT NULL DEFAULT '',
  `Description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`HistoryID`),
  KEY `IDX_EntityType` (`EntityType`),
  KEY `IDX_EntityID` (`EntityID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_item`
--

DROP TABLE IF EXISTS `store_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_item` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(100) NOT NULL DEFAULT '',
  `ReportName` varchar(100) DEFAULT '',
  `Description` text NOT NULL,
  `ReportDescription` text,
  `WebView` tinyint(1) NOT NULL DEFAULT '-1',
  `WebBuy` tinyint(1) NOT NULL DEFAULT '-1',
  `CatalogueView` tinyint(1) NOT NULL DEFAULT '-1',
  `CatalogueBuy` tinyint(1) NOT NULL DEFAULT '-1',
  `Recommended` tinyint(1) NOT NULL DEFAULT '0',
  `CategoryView` tinyint(1) NOT NULL DEFAULT '0',
  `ExternalID` int(11) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Article` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`ItemID`),
  FULLTEXT KEY `IDX_FULLTEXT` (`ItemName`,`Description`)
) ENGINE=MyISAM AUTO_INCREMENT=1488 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_itemcategory`
--

DROP TABLE IF EXISTS `store_itemcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_itemcategory` (
  `ItemID` int(11) NOT NULL DEFAULT '0',
  `CategoryID` int(11) NOT NULL DEFAULT '0',
  `IsPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ItemID`,`CategoryID`),
  KEY `PrimaryCategory` (`IsPrimary`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_itempicture`
--

DROP TABLE IF EXISTS `store_itempicture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_itempicture` (
  `ItemPictureID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemID` int(11) NOT NULL DEFAULT '0',
  `PictureID` int(11) NOT NULL DEFAULT '0',
  `DetailID` int(11) NOT NULL DEFAULT '0',
  `IsPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ItemPictureID`)
) ENGINE=MyISAM AUTO_INCREMENT=2551 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_itemrelation`
--

DROP TABLE IF EXISTS `store_itemrelation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_itemrelation` (
  `ParentID` int(11) NOT NULL DEFAULT '0',
  `ChildID` int(11) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ParentID`,`ChildID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_option`
--

DROP TABLE IF EXISTS `store_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_option` (
  `OptionID` int(11) NOT NULL AUTO_INCREMENT,
  `OptionCode` varchar(25) NOT NULL DEFAULT '',
  `OptionName` varchar(255) NOT NULL DEFAULT '',
  `OptionValue` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`OptionID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_order`
--

DROP TABLE IF EXISTS `store_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_order` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) NOT NULL DEFAULT '0',
  `OrderDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DeliveryDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `PostageZoneID` int(11) NOT NULL DEFAULT '0',
  `PostageCharged` decimal(10,2) NOT NULL DEFAULT '0.00',
  `OrderTotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IsNew` tinyint(1) NOT NULL DEFAULT '1',
  `CustomerNotified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`OrderID`)
) ENGINE=MyISAM AUTO_INCREMENT=3579 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_orderline`
--

DROP TABLE IF EXISTS `store_orderline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_orderline` (
  `OrderLineID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderLineTypeID` int(11) NOT NULL DEFAULT '0',
  `OrderID` int(11) NOT NULL DEFAULT '0',
  `DetailID` int(11) NOT NULL DEFAULT '0',
  `Quantity` int(11) NOT NULL DEFAULT '0',
  `PricePaid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Discount` tinyint(1) NOT NULL DEFAULT '0',
  `OrderLineText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`OrderLineID`)
) ENGINE=MyISAM AUTO_INCREMENT=10557 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_ordermessage`
--

DROP TABLE IF EXISTS `store_ordermessage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_ordermessage` (
  `MessageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `OrderID` int(10) unsigned NOT NULL DEFAULT '0',
  `SentTo` varchar(255) NOT NULL DEFAULT '',
  `Subject` varchar(255) NOT NULL DEFAULT '',
  `Headers` varchar(255) NOT NULL DEFAULT '',
  `Body` varchar(255) NOT NULL DEFAULT '',
  `CreateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`MessageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `store_paymenttype`
--

DROP TABLE IF EXISTS `store_paymenttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_paymenttype` (
  `PaymentTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `PaymentTypeName` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`PaymentTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_picture`
--

DROP TABLE IF EXISTS `store_picture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_picture` (
  `PictureID` int(11) NOT NULL AUTO_INCREMENT,
  `PictureName` varchar(100) NOT NULL DEFAULT '',
  `FileName` varchar(100) NOT NULL DEFAULT '',
  `Width` int(11) NOT NULL DEFAULT '0',
  `Height` int(11) NOT NULL DEFAULT '0',
  `Description` text NOT NULL,
  `Aspect` int(11) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`PictureID`)
) ENGINE=MyISAM AUTO_INCREMENT=2028 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_postageband`
--

DROP TABLE IF EXISTS `store_postageband`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_postageband` (
  `PostageBandID` int(11) NOT NULL AUTO_INCREMENT,
  `PostageBandName` varchar(100) NOT NULL DEFAULT '',
  `StartValue` decimal(10,2) NOT NULL DEFAULT '0.00',
  `EndValue` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`PostageBandID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_postagebandzone`
--

DROP TABLE IF EXISTS `store_postagebandzone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_postagebandzone` (
  `PostageBandID` int(11) NOT NULL DEFAULT '0',
  `PostageZoneID` int(11) NOT NULL DEFAULT '0',
  `Charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`PostageBandID`,`PostageZoneID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_postagecharge`
--

DROP TABLE IF EXISTS `store_postagecharge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_postagecharge` (
  `PostageChargeID` int(11) NOT NULL AUTO_INCREMENT,
  `PostageChargeName` varchar(100) NOT NULL DEFAULT '',
  `Description` varchar(100) NOT NULL DEFAULT '',
  `Charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Icon` varchar(100) NOT NULL DEFAULT '',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`PostageChargeID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_postagezone`
--

DROP TABLE IF EXISTS `store_postagezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_postagezone` (
  `PostageZoneID` int(11) NOT NULL AUTO_INCREMENT,
  `PostageZoneName` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`PostageZoneID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_stock`
--

DROP TABLE IF EXISTS `store_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_stock` (
  `StockID` int(11) NOT NULL AUTO_INCREMENT,
  `DetailID` int(11) NOT NULL DEFAULT '0',
  `SupplierID` int(11) NOT NULL DEFAULT '0',
  `PricePaid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `QuantityPurchased` int(11) NOT NULL DEFAULT '0',
  `QuantityRemaining` int(11) NOT NULL DEFAULT '0',
  `DeliveryDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`StockID`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_supplier`
--

DROP TABLE IF EXISTS `store_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_supplier` (
  `SupplierID` int(11) NOT NULL AUTO_INCREMENT,
  `SupplierName` varchar(100) NOT NULL DEFAULT '',
  `Description` varchar(100) NOT NULL DEFAULT '',
  `IsPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `ExternalID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`SupplierID`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_unit`
--

DROP TABLE IF EXISTS `store_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_unit` (
  `UnitID` int(11) NOT NULL AUTO_INCREMENT,
  `UnitName` varchar(100) NOT NULL DEFAULT '',
  `Code` varchar(10) NOT NULL DEFAULT '',
  `BaseMultiple` varchar(100) NOT NULL DEFAULT '',
  `BaseUnitID` int(11) NOT NULL DEFAULT '0',
  `PluralName` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`UnitID`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_user`
--

DROP TABLE IF EXISTS `store_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_user` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(100) NOT NULL DEFAULT '',
  `Firstname` varchar(100) NOT NULL DEFAULT '',
  `Lastname` varchar(100) NOT NULL DEFAULT '',
  `Password` varchar(100) NOT NULL DEFAULT '',
  `AccessLevel` int(11) NOT NULL DEFAULT '0',
  `CreateDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ModifyDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DeleteDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
