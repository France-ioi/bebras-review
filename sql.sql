/*
SQLyog Trial v12.3.2 (32 bit)
MySQL - 5.6.16 : Database - review
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`review` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `review`;

/*Table structure for table `groups` */

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `dateCreated` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `groups` */

insert  into `groups`(`ID`,`name`,`dateCreated`) values 
(1,'Group1','2016-12-01'),
(2,'Group2\r\n','2016-12-02'),
(3,'Group3\r\n','2016-12-03');

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  `content` text NOT NULL,
  `dateCreated` date NOT NULL,
  `dateModified` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `messages` */

insert  into `messages`(`ID`,`userID`,`taskID`,`content`,`dateCreated`,`dateModified`) values 
(1,1,1,'This is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.\r\nThis is message which has userID is 1 and taskID is 1.','2016-01-01','2016-01-01'),
(2,1,2,'This is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\nThis is message which has userID is 1 and taskID is 2.\r\n','2016-01-02','2016-01-02'),
(3,2,1,'This is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\nThis is message which has userID is 2 and taskID is 1.\r\n','2016-01-03','2016-01-03'),
(11,2,1,'afasdfasfas','2016-12-28','2016-12-28'),
(12,1,1,'asdf','2016-12-28','2016-12-28'),
(13,1,1,'test','2016-12-28','2016-12-28'),
(14,2,2,'jhknk','2016-12-28','2016-12-28');

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  `currentRating` int(11) NOT NULL,
  `potentialRating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `isAssigned` int(1) NOT NULL,
  `initialReviewDate` date NOT NULL,
  `lastChangeReviewDate` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `reviews` */

insert  into `reviews`(`ID`,`userID`,`taskID`,`currentRating`,`potentialRating`,`comment`,`isAssigned`,`initialReviewDate`,`lastChangeReviewDate`) values 
(1,1,1,4,5,'First Review Comment 2\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Commentxx\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment\nFirst Review Comment',1,'2016-12-06','2016-12-06'),
(2,1,2,5,4,'Second Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment\r\nSecond Review Comment',1,'2016-12-07','2016-12-07'),
(3,2,1,5,5,'Third Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment\nThird Review Comment',1,'2016-12-08','2016-12-08');

/*Table structure for table `tasks` */

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `textID` text NOT NULL,
  `folderName` text NOT NULL,
  `year` year(4) NOT NULL,
  `countryCode` text NOT NULL,
  `repositoryDate` date NOT NULL,
  `importDate` date NOT NULL,
  `svnLogin` text NOT NULL,
  `ownerID` int(11) NOT NULL,
  `htmlFilename` text NOT NULL,
  `odtFileName` text NOT NULL,
  `pdfFileName` text NOT NULL,
  `lastChangeDate` date NOT NULL,
  `assignedGroupID` int(11) NOT NULL,
  `status` text NOT NULL,
  `statusComment` text NOT NULL,
  `ownerComment` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `tasks` */

insert  into `tasks`(`ID`,`textID`,`folderName`,`year`,`countryCode`,`repositoryDate`,`importDate`,`svnLogin`,`ownerID`,`htmlFilename`,`odtFileName`,`pdfFileName`,`lastChangeDate`,`assignedGroupID`,`status`,`statusComment`,`ownerComment`) values 
(1,'2016_FR_01','2016_FR_01-not-rectangle',2016,'France\r\n','2016-12-06','2016-11-08','svgLogin',1,'htmlFileNamea','odtFileName','pdfFileName','2016-12-07',1,'Recommended','Really good task','ownerComment'),
(2,'2016_FR_02','2016_FR_02-not-rectangle',2016,'France\r\n','2016-12-07','2016-11-09','svgLogin',1,'htmlFileName','odtFileName','pdfFileName','2016-12-08',2,'Recommended','Really good task','ownerComment'),
(3,'2016_FR_03','2016_FR_03-not-rectangle',2016,'France\r\n','2016-12-08','2016-11-10','svgLogin',1,'htmlFileNamea','odtFileNameas','pdfFileName','2016-12-09',3,'Proposed','Really good tasadfsk','ownerComment');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `login` text NOT NULL,
  `svnLogin` text NOT NULL,
  `firstName` text NOT NULL,
  `lastName` text NOT NULL,
  `email` text NOT NULL,
  `salt` text NOT NULL,
  `password` text NOT NULL,
  `countryCode` text NOT NULL,
  `registrationDate` date NOT NULL,
  `LastLoginDate` date NOT NULL,
  `role` text NOT NULL,
  `groupID` int(11) NOT NULL,
  `groupRole` text NOT NULL,
  `localCheckoutFolder` text NOT NULL,
  `autoLoadTasks` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`ID`,`login`,`svnLogin`,`firstName`,`lastName`,`email`,`salt`,`password`,`countryCode`,`registrationDate`,`LastLoginDate`,`role`,`groupID`,`groupRole`,`localCheckoutFolder`,`autoLoadTasks`) values 
(1,'login\r\n','svnLogin','firstname','lastname','email','salt\r\n','0cc175b9c0f1b6a831c399e269772661','France','2016-12-01','2016-12-01','Admin',1,'Admin','http://localhost/bebras-review/SVN','false'),
(2,'login2\r\n','svnLogin','firstname2','lastname2','email','salt\r\n','8b1a9953c4611296a827abf8c47804d7','France','2016-12-01','2016-12-01','Admin',1,'Admin','E:/Work/SVN','true'),
(3,'login2\r\n','svnLogin','Mathias','Hiron','mathias.hiron@gmail.com','salt','05a671c66aefea124cc08b76ea6d30bb','France','2016-12-28','2016-12-28','Unconfirmed',1,'Admin','localCheckoutFolder','true'),
(4,'login','svnLogin','a','a','a@a.com','salt','0cc175b9c0f1b6a831c399e269772661','France','2016-12-28','2016-12-28','Unconfirmed',1,'member','localCheckoutFolder','false');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
