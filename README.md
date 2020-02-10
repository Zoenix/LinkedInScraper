# LinkedInScraper

Prerequesites:

a running instance of chromedriver on port 4444.
a mysql database linkedin with the table experience and the structure:

CREATE TABLE IF NOT EXISTS `experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `experience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
)

Config:

you need to replace both $email and $password in UrlController line 28 with matching credentials for LinkedIn

