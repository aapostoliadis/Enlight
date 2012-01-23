--
-- Tabellenstruktur für Tabelle `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headline` varchar(50) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `creation_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `post`
--

INSERT INTO `post` (`id`, `headline`, `content`, `creation_date`) VALUES
(62, 'Get help - use our Enlight wiki', '​​<b>You need help with downloading or installing Enlight on your system?</b><br>\n<img src="/enlight/Apps/Blog/images/enlight.png">\n<br><br><br>Don''t worry because our wiki provides an answer to almost any question you might have. In numerous articles you will find comprehensive information on every aspect of the framework. <br><br>Just click <a href="http://www.enlight.de/wiki">here</a>.', '2012-01-20'),
(63, 't3n magazine praises Enlight', '​​The renowned German open source journal “t3n” reports about the new Enlight framework, praising it as a “clearly-structured and well-documented open source ecommerce framework for sophisticated and adaptable ecommerce solutions”. Read the full <a href="http://t3n.de/news/enlight-neues-open-source-359012/">article</a>.<br><br>The Enlight team says thank you – we promise to keep up the good work ;-)<br><br>\n<img src="/enlight/Apps/Blog/images/t3nenlightblog.jpg">', '2012-01-20');