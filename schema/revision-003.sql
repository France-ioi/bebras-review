CREATE TABLE IF NOT EXISTS `rejected` (
`ID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `reason` enum('participated','biased','time','other') NOT NULL DEFAULT 'other'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `rejected`
 ADD PRIMARY KEY (`ID`), ADD KEY `taskID` (`taskID`), ADD KEY `userID` (`userID`);

ALTER TABLE `rejected`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
