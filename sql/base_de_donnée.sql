SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

SET time_zone = "+00:00";

CREATE TABLE `item` (
                        `id` int(11) NOT NULL,
                        `liste_id` int(11) NOT NULL,
                        `nom` text NOT NULL,
                        `descr` text,
                        `img` text,
                        `url` text,
                        `tarif` decimal(5,2) DEFAULT NULL,
                        `nomReserve` varchar(256) NOT NULL,
                        `msgReserve` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `liste` (
                         `no` int(11) NOT NULL,
                         `user_id` int(11) DEFAULT NULL,
                         `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                         `description` text COLLATE utf8_unicode_ci,
                         `expiration` date DEFAULT NULL,
                         `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                         `tokenPart` varchar(255) CHARACTER SET utf8 NOT NULL,
                         `statut` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `participant` (
                               `user_id` int(100) NOT NULL,
                               `no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
                        `username` varchar(256) DEFAULT NULL,
                        `user_id` int(100) NOT NULL,
                        `password_hash` varchar(256) DEFAULT NULL,
                        `email` varchar(256) DEFAULT NULL,
                        `user_level` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`username`, `user_id`, `password_hash`, `email`, `user_level`) VALUES
('anonymous', 0, '', '', 0);

ALTER TABLE `item`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `liste`
    ADD PRIMARY KEY (`no`);

ALTER TABLE `user`
    ADD PRIMARY KEY (`user_id`);

ALTER TABLE `item`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

ALTER TABLE `liste`
    MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
