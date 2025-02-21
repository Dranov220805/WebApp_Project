CREATE DATABASE IF NOT EXISTS note_manager;
USE note_manager;

CREATE TABLE `accounts` (
  `id` int PRIMARY KEY auto_increment NOT NULL,
  `name` varchar(128) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL
);

INSERT INTO `accounts` (`id`, `name`, `password`, `role`) VALUES
('1','Macbook Pro', '1500', '16 inch, 32GB RAM'),
('2', 'iPhone X', '1100', 'No Adapter');
