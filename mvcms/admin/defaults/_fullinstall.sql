-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2009 at 10:06 AM
-- Server version: 5.1.33
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `eninet20090917`
--
-- --------------------------------------------------------

--
-- Table structure for table `_admin`
--

DROP TABLE IF EXISTS `_admin`;
CREATE TABLE IF NOT EXISTS `_admin` (
  `adminid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminstatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `admindate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `adminutil` text NOT NULL,
  `adminpass` varchar(32) NOT NULL DEFAULT '',
  `adminpriv` text NOT NULL,
  `adminemail` text NOT NULL,
  PRIMARY KEY (`adminid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `_admin`
--

INSERT INTO `_admin` (`adminid`, `adminstatut`, `admindate`, `adminutil`, `adminpass`, `adminpriv`, `adminemail`) VALUES
(1, 'Y', '2009-09-24 16:45:48', 'admin', '21232f297a57a5a743894a0e4a801fc3', '0', 'developer&#064;mediavince.com');

-- --------------------------------------------------------

--
-- Table structure for table `_bannedips`
--

DROP TABLE IF EXISTS `_bannedips`;
CREATE TABLE IF NOT EXISTS `_bannedips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_comment`
--

DROP TABLE IF EXISTS `_comment`;
CREATE TABLE `_comment` (
  `commentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commentdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `commentstatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `commentlang` text NOT NULL,
  `commentrid` int(10) unsigned NOT NULL,
  `commentforum` int(10) unsigned NOT NULL DEFAULT '0',
  `commentmembre` int(11) unsigned NOT NULL,
  `commententry` mediumtext NOT NULL,
  `commentresponse` mediumtext NOT NULL,
  `commentip` text NOT NULL,
  PRIMARY KEY (`commentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `_cont`
--

DROP TABLE IF EXISTS `_cont`;
CREATE TABLE IF NOT EXISTS `_cont` (
  `contid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contstatut` enum('N','Y') NOT NULL DEFAULT 'Y',
  `contdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contdateby` text NOT NULL,
  `contupdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contupdateby` text NOT NULL,
  `contpg` int(10) unsigned NOT NULL DEFAULT '0',
  `contlang` text NOT NULL,
  `conttitle` text NOT NULL,
  `contentry` longtext NOT NULL,
  `conturl` text NOT NULL,
  `contlogo` text NOT NULL,
  `contmenu` text NOT NULL,
  `conttype` text NOT NULL,
  `contorient` enum('left','center','right') NOT NULL DEFAULT 'center',
  `contmetadesc` mediumtext NOT NULL,
  `contmetakeyw` mediumtext NOT NULL,
  PRIMARY KEY (`contid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_contdoc`
--

DROP TABLE IF EXISTS `_contdoc`;
CREATE TABLE IF NOT EXISTS `_contdoc` (
  `contdocid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contdocstatut` enum('N','Y') NOT NULL DEFAULT 'Y',
  `contdocdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contdoclang` text NOT NULL,
  `contdocrid` int(10) unsigned NOT NULL,
  `contdocutil` text NOT NULL,
  `contdoccontid` int(10) unsigned NOT NULL DEFAULT '0',
  `contdocdesc` text NOT NULL,
  `contdoc` text NOT NULL,
  PRIMARY KEY (`contdocid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_contphoto`
--

DROP TABLE IF EXISTS `_contphoto`;
CREATE TABLE IF NOT EXISTS `_contphoto` (
  `contphotoid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contphotostatut` enum('N','Y') NOT NULL DEFAULT 'Y',
  `contphotodate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contphotolang` text NOT NULL,
  `contphotorid` int(10) unsigned NOT NULL,
  `contphotoutil` text NOT NULL,
  `contphotocontid` int(10) unsigned NOT NULL DEFAULT '0',
  `contphotodesc` text NOT NULL,
  `contphotoimg` text NOT NULL,
  `contphotosort` int(6) unsigned zerofill NOT NULL default '999999',
  PRIMARY KEY (`contphotoid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_enum`
--

DROP TABLE IF EXISTS `_enum`;
CREATE TABLE IF NOT EXISTS `_enum` (
  `enumid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enumstatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `enumwhat` text NOT NULL,
  `enumtype` text NOT NULL,
  `enumtitre` text NOT NULL,
  PRIMARY KEY (`enumid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `_enum`
--

INSERT INTO `_enum` (`enumid`, `enumstatut`, `enumwhat`, `enumtype`, `enumtitre`) VALUES
(NULL, 'Y', 'gendre', 'mlle', '1'),
(NULL, 'Y', 'gendre', 'mme', '2'),
(NULL, 'Y', 'gendre', 'mrmme', '3'),
(NULL, 'Y', 'gendre', 'mr', '4'),
(NULL, 'Y', 'jour', 'lun', '1'),
(NULL, 'Y', 'jour', 'mar', '2'),
(NULL, 'Y', 'jour', 'mer', '3'),
(NULL, 'Y', 'jour', 'jeu', '4'),
(NULL, 'Y', 'jour', 'ven', '5'),
(NULL, 'Y', 'jour', 'sam', '6'),
(NULL, 'Y', 'jour', 'dim', '7'),
(NULL, 'N', 'lang', 'ar', '1'),
(NULL, 'N', 'lang', 'de', '2'),
(NULL, 'Y', 'lang', 'en', '3'),
(NULL, 'N', 'lang', 'es', '4'),
(NULL, 'N', 'lang', 'fr', '5'),
(NULL, 'N', 'lang', 'it', '6'),
(NULL, 'N', 'lang', 'jp', '7'),
(NULL, 'N', 'lang', 'ru', '8'),
(NULL, 'N', 'lang', 'zh', '9'),
(NULL, 'Y', 'mois', 'jan', '1'),
(NULL, 'Y', 'mois', 'feb', '2'),
(NULL, 'Y', 'mois', 'mar', '3'),
(NULL, 'Y', 'mois', 'apr', '4'),
(NULL, 'Y', 'mois', 'may', '5'),
(NULL, 'Y', 'mois', 'jun', '6'),
(NULL, 'Y', 'mois', 'jul', '7'),
(NULL, 'Y', 'mois', 'aug', '8'),
(NULL, 'Y', 'mois', 'sep', '9'),
(NULL, 'Y', 'mois', 'oct', '10'),
(NULL, 'Y', 'mois', 'nov', '11'),
(NULL, 'Y', 'mois', 'dec', '12'),
(NULL, 'Y', 'privilege', '1', '1'),
(NULL, 'Y', 'privilege', '2', '2'),
(NULL, 'Y', 'privilege', '3', '3'),
(NULL, 'Y', 'privilege', '4', '4'),
(NULL, 'Y', 'statut', '1', 'N'),
(NULL, 'Y', 'statut', '2', 'Y'),
(NULL, 'Y', 'sujet', '1', '1'),
(NULL, 'Y', 'sujet', '2', '2'),
(NULL, 'Y', 'sujet', '3', '3'),
(NULL, 'Y', 'sujet', '4', '4'),
(NULL, 'Y', 'sujet', '5', '5');

-- --------------------------------------------------------

--
-- Table structure for table `_event`
--

DROP TABLE IF EXISTS `_event`;
CREATE TABLE `_event` (
  `eventid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventstatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `eventdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventlang` text NOT NULL,
  `eventrid` int(10) unsigned NOT NULL,
  `eventtype` int(11) unsigned NOT NULL,
  `eventfrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventtitle` text NOT NULL,
  `evententry` longtext NOT NULL,
  `eventimg` text NOT NULL,
  `eventdoc` int(10) NOT NULL,
  `eventmembre` int(3) unsigned NOT NULL,
  PRIMARY KEY (`eventid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `_forum`
--

DROP TABLE IF EXISTS `_forum`;
CREATE TABLE `_forum` (
  `forumid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forumstatut` enum('N','Y') NOT NULL DEFAULT 'Y',
  `forumdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `forumlang` text NOT NULL,
  `forumrid` int(10) unsigned NOT NULL,
  `forumtitle` text NOT NULL,
  `forumentry` longtext NOT NULL,
  `forumtype` int(11) unsigned NOT NULL,
  `forummembre` text NOT NULL,
  `forumpublish` enum('N','Y') NOT NULL DEFAULT 'Y',
  `forumcomment` int(3) unsigned NOT NULL,
  PRIMARY KEY (`forumid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `_gallery`
--

DROP TABLE IF EXISTS `_gallery`;
CREATE TABLE IF NOT EXISTS `_gallery` (
  `galleryid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallerystatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `gallerydate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `gallerylang` text NOT NULL,
  `galleryrid` int(10) unsigned NOT NULL,
  `gallerytitle` text NOT NULL,
  `galleryentry` longtext NOT NULL,
  `galleryimg` int(10) unsigned NOT NULL,
  PRIMARY KEY (`galleryid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_htaccess`
--

DROP TABLE IF EXISTS `_htaccess`;
CREATE TABLE IF NOT EXISTS `_htaccess` (
  `htaccessid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `htaccessdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `htaccessstatut` enum('N','Y') NOT NULL DEFAULT 'Y',
  `htaccesslang` text NOT NULL,
  `htaccessitem` int(4) unsigned NOT NULL,
  `htaccesstitle` text NOT NULL,
  `htaccessentry` text NOT NULL,
  `htaccessurl` text NOT NULL,
  `htaccesstype` text NOT NULL,
  `htaccessmetadesc` mediumtext NOT NULL,
  `htaccessmetakeyw` mediumtext NOT NULL,
  PRIMARY KEY (`htaccessid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_membre`
--

DROP TABLE IF EXISTS `_membre`;
CREATE TABLE `_membre` (
  `membreid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `membredate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `membrestatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `membrelang` text NOT NULL,
  `membrerid` int(10) unsigned NOT NULL,
  `membregendre` int(1) unsigned NOT NULL DEFAULT '0',
  `membreprenom` text NOT NULL,
  `membrenom` text NOT NULL,
  `membreimg` text NOT NULL,
  `membreprofession` text NOT NULL,
  `membreadresse` text NOT NULL,
  `membreville` text NOT NULL,
  `membrecodpost` text NOT NULL,
  `membrepays` text NOT NULL,
  `membrenumtel` text NOT NULL,
  `membreskype` text NOT NULL,
  `membrenumfax` text NOT NULL,
  `membremarketing1` longtext NOT NULL,
  `membreevent` int(3) unsigned NOT NULL,
  `membreforum` int(3) unsigned NOT NULL,
  PRIMARY KEY (`membreid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `_newsletter`
--

DROP TABLE IF EXISTS `_newsletter`;
CREATE TABLE IF NOT EXISTS `_newsletter` (
  `newsletterid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletterstatut` enum('N','Y') NOT NULL DEFAULT 'N',
  `newsletterdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `newslettersujet` text NOT NULL,
  `newslettercontent` text NOT NULL,
  `newslettersent` text NOT NULL,
  `newslettererror` text NOT NULL,
  `newsletterread` text NOT NULL,
  PRIMARY KEY (`newsletterid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_string`
--

DROP TABLE IF EXISTS `_string`;
CREATE TABLE IF NOT EXISTS `_string` (
  `stringid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stringpg` text NOT NULL,
  `stringlang` text NOT NULL,
  `stringtype` text NOT NULL,
  `stringtitle` text NOT NULL,
  `stringentry` text NOT NULL,
  PRIMARY KEY (`stringid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `_user`
--

DROP TABLE IF EXISTS `_user`;
CREATE TABLE `_user` (
  `userid` int(10) unsigned NOT NULL auto_increment,
  `userstatut` enum('N','Y') NOT NULL default 'N',
  `userdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `userlang` text NOT NULL,
  `userrid` int(10) unsigned NOT NULL,
  `userutil` text NOT NULL,
  `userpass` varchar(32) NOT NULL default '',
  `userpriv` text NOT NULL,
  `useremail` text NOT NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
