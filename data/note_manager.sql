-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: May 14, 2025 at 12:35 PM
-- Server version: 8.4.2
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `note_manager`
--

CREATE DATABASE IF NOT EXISTS note_manager;
USE note_manager;

-- --------------------------------------------------------

--
-- Table structure for table `Account`
--

CREATE TABLE `Account` (
  `accountId` char(36) NOT NULL,
  `userName` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `activation_token` varchar(50) DEFAULT NULL,
  `refresh_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'test token',
  `expired_time` datetime DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `profilePicture` varchar(255) DEFAULT NULL,
  `roleId` int NOT NULL DEFAULT '1',
  `isVerified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Account`
--

INSERT INTO `Account` (`accountId`, `userName`, `password`, `isDeleted`, `activation_token`, `refresh_token`, `expired_time`, `email`, `profilePicture`, `roleId`, `isVerified`) VALUES
('1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'Dranov 2', '$2y$10$AhazMEBDLZRs6IyXVf/pZ.ExY1p1929gLtrCFP1Jpk0R4k8XXnrF2', 0, 'b8b124efd76384af19af22daef4ae5f0', '9d7e881ae752c1334920e87c7e511a63', '2025-05-20 16:27:12', 'thanhlongduong6a3@gmail.com', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747074353/Pernote/user-icon/v0j2wx9nfx24w9x5vlj9.png', 1, 1),
('6ee779e1-4abe-4cb1-934c-175ef09fc3d6', 'CheetoBuri', '$2y$10$WW9ro3zch1aoZt9D9CxTr.yApVeBWPHB843zgXXczJfMlfu3ffGZy', 0, '41897b1a76693a8ecdbf8fed97f7ffdb', '7beb9eedde6c431a27995ebf18fba78b', '2025-05-19 05:41:55', '523K0010@student.tdtu.edu.vn', '', 1, 1),
('aeae6450-2a94-11f0-ab83-0242ac150002', 'john_doe', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', 0, NULL, 'test token', NULL, 'john@example.com', NULL, 1, 0),
('aeae689e-2a94-11f0-ab83-0242ac150002', 'jane_smith', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', 0, NULL, 'test token', NULL, 'jane@example.com', NULL, 2, 0),
('aeae6c5b-2a94-11f0-ab83-0242ac150002', 'michael_adams', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', 0, NULL, 'test token', NULL, 'michael@example.com', NULL, 3, 0),
('aeae7103-2a94-11f0-ab83-0242ac150002', 'emily_jones', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', 0, NULL, 'test token', NULL, 'emily@example.com', NULL, 4, 0),
('aeae7c2b-2a94-11f0-ab83-0242ac150002', 'alex_brown', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', 0, NULL, 'test token', NULL, 'alex@example.com', NULL, 2, 0),
('d2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'Dranov Original', '$2y$10$JHnJ9.yeJ298vWuCMeKnSOcqHbZAlo5fTIn3bI2in8TKkhwbvJSse', 0, 'cb7f4959455c4f7d629a63f8fd68fc54', 'f9b04e35c37873d93b743ad1cf877613', '2025-05-20 10:42:19', 'duongthanhlong220805@gmail.com', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747018395/Pernote/user-icon/cjtrnqpks0x3csnjztvk.png', 1, 1),
('df2c7a1c-fc81-42f0-b6a3-16ee310d0d81', 'minh', '$2y$10$z8xjOQcp1pf9wdXaPuXVH.hoOwWp3ehsluGc6hJIUevHScfWje3Xa', 0, '9d0b8caddea0deed42e037b0a1c75909', '055c11060c38ea5d85723721f2c24f7d', '2025-05-19 11:32:15', 'admin@gmail.com', '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Image`
--

CREATE TABLE `Image` (
  `imageId` char(36) NOT NULL,
  `noteId` char(36) NOT NULL,
  `title` varchar(200) NOT NULL,
  `imageLink` varchar(200) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Image`
--

INSERT INTO `Image` (`imageId`, `noteId`, `title`, `imageLink`, `isDeleted`) VALUES
('07e603ef-0137-4f13-8198-3e11d483e27f', '00694ac0-65cb-4aad-b038-c53eeddcf53c', 'vr3gas9399rkhrhhkdnu.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747109776/Pernote/user-image/vr3gas9399rkhrhhkdnu.png', 0),
('5536066a-d80b-4a62-843a-e834e93d0a2b', '1bc971c5-68f0-4d02-a7f0-e6a61177a1eb', 't73iyoxdvkfy0thoyrml.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747192530/Pernote/user-image/t73iyoxdvkfy0thoyrml.png', 0),
('7f308acf-c8b9-4649-a719-b177559ffdb3', '82bd8335-9390-4f98-93ee-0144246ee597', 'hp3rgpc6nz9tmzhebkzv.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747109826/Pernote/user-image/hp3rgpc6nz9tmzhebkzv.png', 0),
('8617826a-ed37-42a1-b2b8-ed3bd593a688', '7d239de5-318f-41d5-93c3-41dff53c8c61', 'crpzvmai7xp5af1gb1pr.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747147506/Pernote/user-image/crpzvmai7xp5af1gb1pr.png', 0),
('a99d4d25-6afe-41ef-b383-1d38d2923ef7', 'f7cf33b9-c2c9-465e-9a4f-51d84354050a', 'tlyztp33zbjruy6jmdpd.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747132632/Pernote/user-image/tlyztp33zbjruy6jmdpd.png', 0),
('aeb6101d-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Shopping List Image', 'grocery_list.png', 0),
('aeb61167-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Project Diagram', 'project_diagram.png', 0),
('aeb612e2-2a94-11f0-ab83-0242ac150002', 'aeae6c5b-2a94-11f0-ab83-0242ac150002', 'Meeting Screenshot', 'meeting_notes.png', 0),
('aeb6136f-2a94-11f0-ab83-0242ac150002', 'aeae7103-2a94-11f0-ab83-0242ac150002', 'Workout Poster', 'workout_plan.png', 0),
('aeb613b1-2a94-11f0-ab83-0242ac150002', 'aeae7c2b-2a94-11f0-ab83-0242ac150002', 'Italy Travel Map', 'vacation_map.png', 0),
('c58985e2-1f27-4363-9343-060ce8f090d0', 'e773219d-d30b-4d6a-90d9-133662f25387', 'qh2bfsqihincb5jvjgxp.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1747062012/Pernote/user-image/qh2bfsqihincb5jvjgxp.png', 0),
('c929b37f-4e37-4331-a6a7-1536d97eb6e4', 'af0f9b7d-3373-405b-a9a5-171f095bf540', 'ndjlq5flu9chrjdnooav.png', 'https://res.cloudinary.com/dydpf7z8u/image/upload/v1746678296/Pernote/user-image/ndjlq5flu9chrjdnooav.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Label`
--

CREATE TABLE `Label` (
  `labelId` char(36) NOT NULL,
  `accountId` char(36) NOT NULL,
  `labelName` varchar(200) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Label`
--

INSERT INTO `Label` (`labelId`, `accountId`, `labelName`, `isDeleted`) VALUES
('0c92e562-7cc4-4612-9189-6852996bad04', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'New label here', 0),
('282c4f24-6997-43a0-8567-4800dd220697', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'test label', 0),
('392593b7-9dd0-49e6-82bc-7df9e72ce222', 'df2c7a1c-fc81-42f0-b6a3-16ee310d0d81', '1234', 0),
('83c7ff6f-0cf6-4efd-982c-53f79b8808bc', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'Work', 0),
('f21241d1-18b1-4e81-992a-dee633d1887a', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'Homework', 0);

-- --------------------------------------------------------

--
-- Table structure for table `LogNote`
--

CREATE TABLE `LogNote` (
  `logNoteId` char(36) NOT NULL,
  `noteId` char(36) NOT NULL,
  `content` text NOT NULL,
  `process` varchar(200) NOT NULL,
  `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `flag` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Modification`
--

CREATE TABLE `Modification` (
  `modifyId` char(36) NOT NULL,
  `noteId` char(36) NOT NULL,
  `isPinned` tinyint(1) NOT NULL DEFAULT '0',
  `pinnedTime` datetime DEFAULT NULL,
  `isShared` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Modification`
--

INSERT INTO `Modification` (`modifyId`, `noteId`, `isPinned`, `pinnedTime`, `isShared`) VALUES
('04ea0ae7-b367-4fb6-aaee-6d7d86de7b20', '82bd8335-9390-4f98-93ee-0144246ee597', 1, '2025-05-13 19:27:16', 0),
('1f7efc97-511f-463c-a8ad-db75dcce810d', '282394ef-7566-45a0-9d6f-4aa7401c7170', 0, NULL, 0),
('2837b497-f39a-460a-95dc-51be1acb665f', '00694ac0-65cb-4aad-b038-c53eeddcf53c', 1, '2025-05-13 16:28:05', 0),
('34b34f70-1d1b-4756-bcda-ffb06a2d3c51', 'a1fab8fd-266b-42f2-b373-0af19c0a4014', 0, NULL, 0),
('41aec458-e804-42aa-8585-fab21a4eef5b', '6c9a17d3-f8fd-45e2-8a40-94a782395a45', 0, NULL, 0),
('4c533222-ca61-4f79-930c-c0d89f36ed44', '3ed8b5a7-8a81-43bf-b54e-034b44f30b30', 0, NULL, 0),
('5b6655c4-a6d9-4a27-88ed-d4f7d389a5f7', '870c6c65-327c-48af-9d40-baaf8261ce06', 0, NULL, 0),
('629be348-acf8-43b0-8332-eb1827c973a6', '1bc971c5-68f0-4d02-a7f0-e6a61177a1eb', 0, '2025-05-13 17:25:37', 0),
('642ffd73-dc6b-4855-8559-76721469c2ed', 'f7cf33b9-c2c9-465e-9a4f-51d84354050a', 0, '2025-05-12 22:02:27', 0),
('763cc1d4-5838-4c9e-a856-65dd4016d41b', '6e875b49-1149-47ba-adc3-4b1552513426', 0, NULL, 0),
('79bc0f08-a4bc-42fd-a167-ebe7b9b66d85', 'ba21ea1d-abb4-403c-9545-d8e58ce51605', 1, '2025-05-14 12:20:06', 0),
('810d0507-24f5-4c78-ac01-95be685b8c01', '350dc667-f0d6-4e29-ad9c-c8b29f271f13', 1, '2025-05-13 19:27:18', 0),
('84b58fce-380d-44fe-9287-fc87366f4d36', 'e773219d-d30b-4d6a-90d9-133662f25387', 0, '2025-05-13 16:28:07', 0),
('94f48050-de2f-4789-9f1f-6bc771062af3', '18e1e3f0-4c1e-4c9e-a9c2-90db4a7a1443', 1, '2025-05-13 19:27:20', 0),
('9ce34db0-31c0-413f-bdb4-93fb8afc65e2', 'cc10ef36-9571-4ea7-8be4-178b38b81794', 0, NULL, 0),
('a1069ef3-4fcf-49c2-bb3e-b389116e04d8', 'e3b6111c-ee67-49f6-aaa1-a6c76a1096e7', 0, NULL, 0),
('aeb57e50-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 1, '2025-05-06 16:10:52', 1),
('aeb58019-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 0, NULL, 1),
('aeb5811e-2a94-11f0-ab83-0242ac150002', 'aeae6c5b-2a94-11f0-ab83-0242ac150002', 1, '2025-05-06 16:10:52', 0),
('aeb58179-2a94-11f0-ab83-0242ac150002', 'aeae7103-2a94-11f0-ab83-0242ac150002', 0, NULL, 1),
('aeb581a4-2a94-11f0-ab83-0242ac150002', 'aeae7c2b-2a94-11f0-ab83-0242ac150002', 1, '2025-05-06 16:10:52', 1),
('b08c7068-fa10-4d61-a2f0-44e0736e7b41', '79c07775-8d9b-4996-aa2e-e936bf22adea', 1, '2025-05-12 18:34:18', 0),
('c8039324-7089-4e82-b37f-d7a416361953', '7815ccb9-d56f-450e-9bb3-dadf03edf9ce', 0, '2025-05-14 12:20:03', 0),
('c89db4cd-777a-4e8b-b171-678b34bcbb75', 'af0f9b7d-3373-405b-a9a5-171f095bf540', 1, '2025-05-12 09:50:48', 0),
('dd18b7be-7942-4477-8685-f97db387a400', 'e6a8ebc6-a880-4d47-9af1-5bd608c1936f', 1, '2025-05-12 18:35:38', 0),
('e5de048f-a3c5-405d-ab38-01ad80acdc1b', 'b3c40b1d-5e49-432d-aeb3-ed8721407c1d', 0, NULL, 0),
('e679b907-8f48-4dbf-86a2-7275f2d26bfc', '7d239de5-318f-41d5-93c3-41dff53c8c61', 1, '2025-05-13 17:25:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Note`
--

CREATE TABLE `Note` (
  `noteId` char(36) NOT NULL,
  `accountId` char(36) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `createDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `isProtected` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Note`
--

INSERT INTO `Note` (`noteId`, `accountId`, `title`, `content`, `createDate`, `isDeleted`, `isProtected`) VALUES
('00694ac0-65cb-4aad-b038-c53eeddcf53c', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'Test title 1', 'content 3sadfasdwdfsgasf\nasdfasfasfasdfasdfasf\nhjvjhbasfasf\nasfasfasf\nasfdasfsadfs\nasfasfasfdjhbjh', '2025-05-14 08:15:47', 0, 0),
('18e1e3f0-4c1e-4c9e-a9c2-90db4a7a1443', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'asdfasdfas', 'tesatsdsafasfhjaskdf\nsadfhjkasdgfkjlas\nsadfjasl;kfhasld\nshgdf\nhrtyjr5tjrtyhjrty\ndfghdfghdfghdfgh\ndfghdfghdfgjogeritoipqwer\n\wertwerhgouwernoiwerg\newrgnouiwerghrwei9og\nwergfhiowerghwoerasDFS\nasdfasdfasdfp', '2025-05-14 00:39:56', 0, 0),
('1bc971c5-68f0-4d02-a7f0-e6a61177a1eb', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'This is another title', 'asdfsafasdfadsfadsfsdth\nsadfasfasdfdasdfasdf\nContent title 2ssadfassadfasfasfasf\nthis is another contentsafas\nsdfasfasdfasfasdf\nssfadfadsfasfsazzxvfgdh\nasdfasfasddfasdfsa\nsdfaasfdwqrqwadfasdf', '2025-05-14 15:26:47', 0, 0),
('282394ef-7566-45a0-9d6f-4aa7401c7170', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'test title 3', 'test 3', '2025-05-13 18:40:58', 0, 0),
('350dc667-f0d6-4e29-ad9c-c8b29f271f13', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'asdfasdfa', 'asfasfasfasdfasdfasdsa\nasdfasdfasdfasdf\nsadfasfasdfasf\nasdfasdfasdfs\nasdfasfasdfasdfasd\nfasdfasdfsadfasdfas\nasdfasdfasfasdfas\nfasdfsdfas\nasfdasfasdfasdfasfd\nasfdsdafasd\nasfasfasfasfdasdf', '2025-05-14 08:49:57', 0, 0),
('3ed8b5a7-8a81-43bf-b54e-034b44f30b30', 'df2c7a1c-fc81-42f0-b6a3-16ee310d0d81', '3123', '123', '2025-05-12 18:33:34', 0, 0),
('6c9a17d3-f8fd-45e2-8a40-94a782395a45', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'asdfasdfas', 'dsagfdsfagasga', '2025-05-13 15:48:27', 1, 0),
('6e875b49-1149-47ba-adc3-4b1552513426', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'conteent 34', 'title 44', '2025-05-14 12:23:37', 1, 0),
('7815ccb9-d56f-450e-9bb3-dadf03edf9ce', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'this is another content', 'this is another\nsdfasfasdfasfasdf\nsrvgsrgtgtreg', '2025-05-14 12:59:53', 0, 0),
('79c07775-8d9b-4996-aa2e-e936bf22adea', 'df2c7a1c-fc81-42f0-b6a3-16ee310d0d81', '12313', '123', '2025-05-12 18:32:31', 0, 0),
('7d239de5-318f-41d5-93c3-41dff53c8c61', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'Test tiel4324safs', 'asdfsafasdfadsfadsfsdth\nTest new title content here2\nthis is another contentsafas\nsdfasfasdfasfasdf\nssfadfadsfasfsa\nasdfasfasddf', '2025-05-14 13:00:28', 0, 0),
('82bd8335-9390-4f98-93ee-0144246ee597', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'test 2', 'test 2asfavcv\nasdfasdfwsdfasfdsfas\nasfdasfafssadafdasdff\nasfasffadsfssfsasdfsda\nasdfsdfgsdgasdfasdf\nasdsdgsdasfasdfaasdfasf\nfasdfasdfas\nasdfasdfafdaassdafdsafa\nsadfasdfasdfasdfasd\nasasdfasdfsafasdf\ndfasdfasdfasa\nasdfasdfas', '2025-05-14 09:25:15', 0, 0),
('870c6c65-327c-48af-9d40-baaf8261ce06', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'test 2', 'test', '2025-05-13 18:39:01', 0, 0),
('a1fab8fd-266b-42f2-b373-0af19c0a4014', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'asdfasfasf', 'sadfasdfsadfasfasd', '2025-05-13 18:44:25', 0, 0),
('aeb119ff-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Grocery List', 'Milk, Eggs, Bread, Butter', '2025-05-06 16:10:52', 0, 0),
('aeb11c29-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Project Plan', 'Finish phase 1 by next week', '2025-05-06 16:10:52', 0, 1),
('aeb11dd3-2a94-11f0-ab83-0242ac150002', 'aeae6c5b-2a94-11f0-ab83-0242ac150002', 'Meeting Notes', 'Discussed budget planning', '2025-05-06 16:10:52', 0, 0),
('aeb11e76-2a94-11f0-ab83-0242ac150002', 'aeae7103-2a94-11f0-ab83-0242ac150002', 'Workout Plan', 'Monday: Cardio, Tuesday: Strength', '2025-05-06 16:10:52', 0, 0),
('aeb11ecc-2a94-11f0-ab83-0242ac150002', 'aeae7c2b-2a94-11f0-ab83-0242ac150002', 'Vacation Ideas', 'Planning trip to Italy', '2025-05-06 16:10:52', 0, 1),
('aeb18671-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Grocery List', 'Milk, Eggs, Bread, Butter', '2025-05-06 16:10:52', 0, 0),
('aeb187a4-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Project Plan', 'Finish phase 1 by next week', '2025-05-06 16:10:52', 0, 1),
('aeb187e3-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Meeting Notes', 'Discussed budget planning', '2025-05-06 16:10:52', 0, 0),
('aeb18815-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Workout Plan', 'Monday: Cardio, Tuesday: Strength', '2025-05-06 16:10:52', 0, 0),
('aeb1883c-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Vacation Ideas', 'Planning trip to Italy', '2025-05-06 16:10:52', 0, 1),
('aeb18864-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Reading List', 'Atomic Habits, Deep Work', '2025-05-06 16:10:52', 0, 0),
('aeb1888c-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Birthday Plans', 'Dinner with friends, cake pickup', '2025-05-06 16:10:52', 0, 0),
('aeb188b0-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Shopping List', 'Shoes, Jacket, Hat', '2025-05-06 16:10:52', 0, 0),
('aeb188d6-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Chores', 'Clean room, do laundry', '2025-05-06 16:10:52', 0, 0),
('aeb188ff-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'App Ideas', 'Note app, Budget tracker', '2025-05-06 16:10:52', 0, 1),
('aeb18927-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Journal Entry', 'Had a productive day', '2025-05-06 16:10:52', 0, 0),
('aeb1894c-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Recipe Notes', 'Pasta with tomato sauce', '2025-05-06 16:10:52', 0, 0),
('aeb18970-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Study Topics', 'Math, History, Programming', '2025-05-06 16:10:52', 0, 0),
('aeb18995-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Wishlist', 'New laptop, Headphones', '2025-05-06 16:10:52', 0, 0),
('aeb189bc-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'Goals', 'Run 5k, Read 10 books', '2025-05-06 16:10:52', 0, 1),
('aeb298b0-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Grocery List', 'Eggs, Chicken, Rice, Coffee', '2025-05-06 16:10:52', 0, 0),
('aeb29b14-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Travel Checklist', 'Passport, Tickets, Camera', '2025-05-06 16:10:52', 0, 1),
('aeb29b5c-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Daily Routine', 'Wake up at 7, Workout at 8', '2025-05-06 16:10:52', 0, 0),
('aeb29b86-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Workout Log', 'Squats, Bench Press, Deadlifts', '2025-05-06 16:10:52', 0, 0),
('aeb29bb1-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Movie Watchlist', 'Inception, Interstellar', '2025-05-06 16:10:52', 0, 0),
('aeb2a963-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Startup Notes', 'Pitch deck, MVP features', '2025-05-06 16:10:52', 0, 1),
('aeb2a9cf-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Study Plan', 'Revise algorithms, system design', '2025-05-06 16:10:52', 0, 0),
('aeb2abc4-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Project Deadlines', 'Submit report by Monday', '2025-05-06 16:10:52', 0, 0),
('aeb2ae48-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Ideas Dump', 'SaaS platform, Content creator tools', '2025-05-06 16:10:52', 0, 1),
('aeb2b2e1-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Reflection', 'Week went better than expected', '2025-05-06 16:10:52', 0, 0),
('aeb2b61e-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Fitness Goals', 'Lose 5kg, Gain muscle', '2025-05-06 16:10:52', 0, 0),
('aeb2b6c1-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Meal Plan', 'High protein, low carb', '2025-05-06 16:10:52', 0, 0),
('aeb2b6ec-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Learning Notes', 'GraphQL, Docker basics', '2025-05-06 16:10:52', 0, 0),
('aeb2b711-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Wishlist', 'Smartwatch, Bluetooth speaker', '2025-05-06 16:10:52', 0, 0),
('aeb2b735-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Goals', 'Launch side project, 10k steps daily', '2025-05-06 16:10:52', 0, 1),
('af0f9b7d-3373-405b-a9a5-171f095bf540', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'asdfasfasfasfsf', 'tasadsffas\ngsggaas\ndfasfasdfasdf\nasfasdfsadfsadf\nasdfasdfadsfas\nasfasdfasdfasf\nadsdfasfasdfasf\nasdfasdfasfasfasasfasfas\nasdfasfasfasdf\nasfasdfasdfasfasfd', '2025-05-13 11:19:02', 1, 0),
('b3c40b1d-5e49-432d-aeb3-ed8721407c1d', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'asdfasdfas', 'asdfasasdfasd\nasdfasdfas\ndfasdfasfas\ndfsadfasdf\nasdfasdfas\nasdfasdfasd', '2025-05-13 19:27:25', 1, 0),
('ba21ea1d-abb4-403c-9545-d8e58ce51605', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'title 5ewwer', 'title 55werqwr\ndsfgdgsfgsfg\nssdgfsdgf\nsggsfddggsdfgsdsg\\\nsdf\ngerwegwergrwegwe', '2025-05-14 13:00:40', 0, 0),
('cc10ef36-9571-4ea7-8be4-178b38b81794', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'test title 223', 'test 4asdfasfas\nasfasfasdfasf\nasdfasfasdfasdfas\nfasdfasfdasf\nasfsdafdsfasf', '2025-05-14 00:40:05', 0, 0),
('e3b6111c-ee67-49f6-aaa1-a6c76a1096e7', '6ee779e1-4abe-4cb1-934c-175ef09fc3d6', '123134', 'test note', '2025-05-12 10:05:04', 0, 0),
('e6a8ebc6-a880-4d47-9af1-5bd608c1936f', 'df2c7a1c-fc81-42f0-b6a3-16ee310d0d81', '1234', '512314', '2025-05-12 18:34:26', 0, 0),
('e773219d-d30b-4d6a-90d9-133662f25387', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'Test  1 asdfasdfas', 'Test 1 asasdf\nasfasddfasfsadfasdfa\ndasdfasdfaasdfasd\nasfassadfasfaasfasf\ndfasdfasfashkjhasdfa\nklasfdasfdas\nsadfasafsas\nsadfasdsdagfsa\ndgdsfsadfasfffasdf\nasdfasfasffasdfasdfads\nfasdfasdfasdssdf\nfasdasfasdfasdfasdfasfasioui\nfasdasdfasdfasdf\nfasdasdfasfadasfasdfsadfas\nasfasfadsfasasfas\nasfasfasfasfas32441324y\n', '2025-05-13 18:30:22', 0, 0),
('f7cf33b9-c2c9-465e-9a4f-51d84354050a', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'This is a title', 'asdfasdfasdfasasfastyy\nasdfasdfasdfasdfasd\nasdfasdfasdfasdfsadsads\nasdfasdfasdfasdfasadf\nhfjghfjgfjgfhjasdfasy\nfasdfasdfasdfasdasdfasdfafda\nasdfasdfasdfaasdfas\ndsaasdfasdfasdfdsfasdfasd\nfasdfasdfasdfasdfasdasdfa\nasdfasdfasdfas', '2025-05-13 18:30:13', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `NoteLabel`
--

CREATE TABLE `NoteLabel` (
  `noteLabelId` char(36) NOT NULL,
  `noteId` char(36) NOT NULL,
  `labelId` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `NoteLabel`
--

INSERT INTO `NoteLabel` (`noteLabelId`, `noteId`, `labelId`) VALUES
('02d27098-eee4-4614-943b-c254b02e66d5', 'e773219d-d30b-4d6a-90d9-133662f25387', '83c7ff6f-0cf6-4efd-982c-53f79b8808bc'),
('0f1a0523-2590-4425-b8bb-bee9cb17ed3d', '79c07775-8d9b-4996-aa2e-e936bf22adea', '392593b7-9dd0-49e6-82bc-7df9e72ce222'),
('1708d567-e23c-4c7d-ad48-2022c8927bba', 'af0f9b7d-3373-405b-a9a5-171f095bf540', '83c7ff6f-0cf6-4efd-982c-53f79b8808bc'),
('1e001eaa-5c52-4e9e-870f-1abe7642575d', '82bd8335-9390-4f98-93ee-0144246ee597', '675e7f8c-b09e-44af-996f-7225038efbd3'),
('2afe4aa4-8a19-46a3-8405-13f23ff481d5', '7d239de5-318f-41d5-93c3-41dff53c8c61', 'c935758c-cdb2-4083-a1df-e046f1de7dfb'),
('303a5433-5226-4405-a3e4-a1268758b47b', 'b3c40b1d-5e49-432d-aeb3-ed8721407c1d', '675e7f8c-b09e-44af-996f-7225038efbd3'),
('3a942c5c-39f1-4ce7-b95e-6c095ec19ede', '82bd8335-9390-4f98-93ee-0144246ee597', '83c7ff6f-0cf6-4efd-982c-53f79b8808bc'),
('42be9acd-8506-440e-9f8e-9e15bf51cdbb', '00694ac0-65cb-4aad-b038-c53eeddcf53c', '675e7f8c-b09e-44af-996f-7225038efbd3'),
('4dfde821-300b-47da-939c-de7fb06f6968', 'f7cf33b9-c2c9-465e-9a4f-51d84354050a', 'f21241d1-18b1-4e81-992a-dee633d1887a'),
('5e91fe8c-b651-4ce4-b425-94fcb4b31a4b', 'af0f9b7d-3373-405b-a9a5-171f095bf540', '675e7f8c-b09e-44af-996f-7225038efbd3'),
('6626c7d4-7f23-4259-bfd4-45bc0317b462', '00694ac0-65cb-4aad-b038-c53eeddcf53c', '83c7ff6f-0cf6-4efd-982c-53f79b8808bc'),
('7d335e35-0ff9-44bc-b5cc-d1a969b39678', 'e773219d-d30b-4d6a-90d9-133662f25387', 'f21241d1-18b1-4e81-992a-dee633d1887a'),
('854e9fbf-52ba-49a6-8be0-40a5c7690001', '7d239de5-318f-41d5-93c3-41dff53c8c61', '0c92e562-7cc4-4612-9189-6852996bad04'),
('9d2397dc-3f82-496c-898b-9aff9e4ec7c2', 'f7cf33b9-c2c9-465e-9a4f-51d84354050a', '675e7f8c-b09e-44af-996f-7225038efbd3'),
('aa8b101e-60e6-4956-9c04-0d4bef6067b2', 'b3c40b1d-5e49-432d-aeb3-ed8721407c1d', 'f21241d1-18b1-4e81-992a-dee633d1887a'),
('b29ecbd2-dd88-4ab4-8aae-889de1b56c33', '82bd8335-9390-4f98-93ee-0144246ee597', 'f21241d1-18b1-4e81-992a-dee633d1887a'),
('c1b71976-45e9-4c67-b07d-cd2274be4ffd', '1bc971c5-68f0-4d02-a7f0-e6a61177a1eb', 'c935758c-cdb2-4083-a1df-e046f1de7dfb'),
('dc666b31-3e7c-41cd-99fb-3a4e10ec144a', '6c9a17d3-f8fd-45e2-8a40-94a782395a45', '282c4f24-6997-43a0-8567-4800dd220697'),
('e3563274-7aab-43f0-b5aa-284a2ef1d50f', '1bc971c5-68f0-4d02-a7f0-e6a61177a1eb', '282c4f24-6997-43a0-8567-4800dd220697'),
('f1091dfe-70ee-499b-b899-dede16f64dd6', 'e773219d-d30b-4d6a-90d9-133662f25387', '675e7f8c-b09e-44af-996f-7225038efbd3'),
('fc13eb9b-261e-4fe6-a99b-2797fab245bd', '7d239de5-318f-41d5-93c3-41dff53c8c61', '282c4f24-6997-43a0-8567-4800dd220697');

-- --------------------------------------------------------

--
-- Table structure for table `NoteProtect`
--

CREATE TABLE `NoteProtect` (
  `noteProtectId` char(36) NOT NULL,
  `noteId` char(36) NOT NULL,
  `password` varchar(200) NOT NULL,
  `isEnabled` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `NoteProtect`
--

INSERT INTO `NoteProtect` (`noteProtectId`, `noteId`, `password`, `isEnabled`, `isDeleted`) VALUES
('aeb519c9-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'securepassword123', 1, 0),
('aeb51b35-2a94-11f0-ab83-0242ac150002', 'aeae7c2b-2a94-11f0-ab83-0242ac150002', 'supersecret321', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `NoteSharing`
--

CREATE TABLE `NoteSharing` (
  `noteSharingId` char(36) NOT NULL,
  `noteId` char(36) NOT NULL,
  `sharedEmail` varchar(200) NOT NULL,
  `receivedEmail` varchar(200) NOT NULL,
  `timeShared` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `canEdit` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `NoteSharing`
--

INSERT INTO `NoteSharing` (`noteSharingId`, `noteId`, `sharedEmail`, `receivedEmail`, `timeShared`, `canEdit`) VALUES
('877f1aa0-11bd-45bd-8b94-51c4cc7df8f7', 'f7cf33b9-c2c9-465e-9a4f-51d84354050a', 'duongthanhlong220805@gmail.com', 'thanhlongduong6a3@gmail.com', '2025-05-13 09:04:53', 0),
('ea3ffb8c-63fc-4a4a-86a3-70732d6f3f6a', '1bc971c5-68f0-4d02-a7f0-e6a61177a1eb', 'thanhlongduong6a3@gmail.com', 'duongthanhlong220805@gmail.com', '2025-05-13 05:01:55', 1),
('f044d0f1-c5d0-45b2-845a-ffad207a9770', '82bd8335-9390-4f98-93ee-0144246ee597', 'duongthanhlong220805@gmail.com', 'thanhlongduong6a3@gmail.com', '2025-05-13 07:53:00', 1),
('f88223de-94d1-4f85-9760-54860ad24d57', '7d239de5-318f-41d5-93c3-41dff53c8c61', 'thanhlongduong6a3@gmail.com', 'duongthanhlong220805@gmail.com', '2025-05-13 05:01:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Preference`
--

CREATE TABLE `Preference` (
  `preferenceId` char(36) NOT NULL,
  `accountId` char(36) NOT NULL,
  `layout` varchar(30) NOT NULL,
  `noteFont` varchar(200) NOT NULL,
  `noteColor` varchar(200) NOT NULL,
  `font` varchar(200) NOT NULL,
  `isDarkTheme` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Preference`
--

INSERT INTO `Preference` (`preferenceId`, `accountId`, `layout`, `noteFont`, `noteColor`, `font`, `isDarkTheme`) VALUES
('66a6f0e3-c2fd-4e1a-b36a-8fc6bed2b48a', 'd2ba9f70-91b1-4957-b9e7-2674d484ba8f', 'list', '14px', '#ffffff', 'Arial', 0),
('977c80fc-4520-4882-8d34-dabcd74ff109', '6ee779e1-4abe-4cb1-934c-175ef09fc3d6', 'list', '16px', '#000000', 'Arial', 0),
('aa99c93d-c0e5-4413-963e-82e875722e0b', '1d84c8f9-a722-4e4a-99d0-ac8fac2cb5c6', 'list', '16px', '#000000', 'Arial', 0),
('aeb00837-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'grid', 'Arial', '#ffcc00', 'Calibri', 1),
('aeb009c0-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'list', 'Times New Roman', '#00ffcc', 'Verdana', 0),
('aeb00ab3-2a94-11f0-ab83-0242ac150002', 'aeae6c5b-2a94-11f0-ab83-0242ac150002', 'list', 'Courier New', '#ff5733', 'Arial', 1),
('aeb00b09-2a94-11f0-ab83-0242ac150002', 'aeae7103-2a94-11f0-ab83-0242ac150002', 'grid', 'Helvetica', '#33ff57', 'Tahoma', 0),
('aeb00b38-2a94-11f0-ab83-0242ac150002', 'aeae7c2b-2a94-11f0-ab83-0242ac150002', 'grid', 'Georgia', '#5733ff', 'Sans-serif', 1),
('cf0f229d-6bb0-4e02-9a13-172c27ff9120', 'df2c7a1c-fc81-42f0-b6a3-16ee310d0d81', 'list', '14px', '#007bff', 'Arial', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userId` char(36) NOT NULL,
  `accountId` char(36) NOT NULL,
  `firstName` varchar(200) NOT NULL,
  `lastName` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `profilePicture` varchar(200) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userId`, `accountId`, `firstName`, `lastName`, `email`, `phone`, `address`, `profilePicture`, `isDeleted`) VALUES
('aeb0793d-2a94-11f0-ab83-0242ac150002', 'aeae6450-2a94-11f0-ab83-0242ac150002', 'John', 'Doe', 'john@example.com', '123-456-7890', '123 Main St', 'john_profile.jpg', 0),
('aeb07a5f-2a94-11f0-ab83-0242ac150002', 'aeae689e-2a94-11f0-ab83-0242ac150002', 'Jane', 'Smith', 'jane@example.com', '987-654-3210', '456 Elm St', 'jane_profile.jpg', 0),
('aeb07b49-2a94-11f0-ab83-0242ac150002', 'aeae6c5b-2a94-11f0-ab83-0242ac150002', 'Michael', 'Adams', 'michael@example.com', '555-123-4567', '789 Pine St', 'michael_profile.jpg', 0),
('aeb07bac-2a94-11f0-ab83-0242ac150002', 'aeae7103-2a94-11f0-ab83-0242ac150002', 'Emily', 'Jones', 'emily@example.com', '555-987-6543', '321 Oak St', 'emily_profile.jpg', 0),
('aeb07bde-2a94-11f0-ab83-0242ac150002', 'aeae7c2b-2a94-11f0-ab83-0242ac150002', 'Alex', 'Brown', 'alex@example.com', '333-666-9999', '654 Maple St', 'alex_profile.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Account`
--
ALTER TABLE `Account`
  ADD PRIMARY KEY (`accountId`);

--
-- Indexes for table `Image`
--
ALTER TABLE `Image`
  ADD PRIMARY KEY (`imageId`);

--
-- Indexes for table `Label`
--
ALTER TABLE `Label`
  ADD PRIMARY KEY (`labelId`),
  ADD UNIQUE KEY `accountId` (`accountId`,`labelName`);

--
-- Indexes for table `LogNote`
--
ALTER TABLE `LogNote`
  ADD PRIMARY KEY (`logNoteId`);

--
-- Indexes for table `Modification`
--
ALTER TABLE `Modification`
  ADD PRIMARY KEY (`modifyId`);

--
-- Indexes for table `Note`
--
ALTER TABLE `Note`
  ADD PRIMARY KEY (`noteId`);

--
-- Indexes for table `NoteLabel`
--
ALTER TABLE `NoteLabel`
  ADD PRIMARY KEY (`noteLabelId`);

--
-- Indexes for table `NoteProtect`
--
ALTER TABLE `NoteProtect`
  ADD PRIMARY KEY (`noteProtectId`);

--
-- Indexes for table `NoteSharing`
--
ALTER TABLE `NoteSharing`
  ADD PRIMARY KEY (`noteSharingId`);

--
-- Indexes for table `Preference`
--
ALTER TABLE `Preference`
  ADD PRIMARY KEY (`preferenceId`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
