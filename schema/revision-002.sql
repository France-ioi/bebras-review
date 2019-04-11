ALTER TABLE `reviews`
    ADD `itsInformatics` ENUM('none','great','canImprove','missing') NOT NULL DEFAULT 'none' AFTER `potentialRating`,
    ADD `ageDifficulty` ENUM('none','easier','good','harder','missing') NOT NULL DEFAULT 'none' AFTER `itsInformatics`;
