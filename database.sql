SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `payment` (
  `id` int(12) NOT NULL,
  `productId` int(12) NOT NULL DEFAULT '0',
  `transID` varchar(150) NOT NULL,
  `bitcoinaddress` varchar(150) DEFAULT NULL,
  `price` decimal(10,8) NOT NULL DEFAULT '0.00000000',
  `currency` varchar(3) DEFAULT '',
  `btc_price` decimal(18,8) DEFAULT '0.00000000',
  `received` decimal(10,8) DEFAULT '0.00000000',
  `status` decimal(10,0) NOT NULL DEFAULT '0',
  `created` int(13) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `product` (
  `productId` int(12) NOT NULL,
  `detail` text CHARACTER SET utf8 COLLATE utf8_bin,
  `description` varchar(350) NOT NULL DEFAULT '',
  `bitcoin_address` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `size` varchar(30) NOT NULL DEFAULT '',
  `line` int(12) NOT NULL DEFAULT '0',
  `sellit` int(1) NOT NULL DEFAULT '0',
  `timeit` int(1) NOT NULL DEFAULT '0',
  `timelist` varchar(30) NOT NULL DEFAULT '',
  `passdelete` varchar(300) NOT NULL DEFAULT '',
  `download` int(12) NOT NULL DEFAULT '0',
  `btc_price` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `currency` varchar(3) NOT NULL DEFAULT '',
  `created` int(13) DEFAULT NULL,
  `del` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);


ALTER TABLE `product`
  ADD PRIMARY KEY (`productId`),
  ADD UNIQUE KEY `productId` (`productId`);

  
ALTER TABLE `payment`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

  
ALTER TABLE `product`
  MODIFY `productId` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;