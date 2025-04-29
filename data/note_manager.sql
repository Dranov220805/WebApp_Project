-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: db
-- Generation Time: Jan 06, 2025 at 04:39 PM
-- Server version: 9.1.0
-- PHP Version: 8.2.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `note_manager`
CREATE DATABASE IF NOT EXISTS note_manager;
USE note_manager;

-- --------------------------------------------------------

-- Table structure for table `Account`
CREATE TABLE `Account` (
  `accountId` CHAR(36) NOT NULL PRIMARY KEY,
  `userName` VARCHAR(200) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE,
  `activation_token` VARCHAR(50) DEFAULT NULL,
  `remember_token` VARCHAR(255) NULL DEFAULT 'test token',
  `email` VARCHAR(200) NOT NULL,
  `profilePicture` VARCHAR(255) DEFAULT NULL,
  `roleId` INT NOT NULL DEFAULT 1,
  `isVerified` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `Preference`
CREATE TABLE `Preference` (
  `preferenceId` CHAR(36) NOT NULL PRIMARY KEY,
  `accountId` CHAR(36) NOT NULL,
  `layout` VARCHAR(30) NOT NULL,
  `noteFont` VARCHAR(200) NOT NULL,
  `noteColor` VARCHAR(200) NOT NULL,
  `font` VARCHAR(200) NOT NULL,
  `isDarkTheme` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `Users`
CREATE TABLE `Users` (
  `userId` CHAR(36) NOT NULL PRIMARY KEY,
  `accountId` CHAR(36) NOT NULL,
  `firstName` VARCHAR(200) NOT NULL,
  `lastName` VARCHAR(200) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `phone` VARCHAR(200) NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `profilePicture` VARCHAR(200) NOT NULL,
  `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `Note`
CREATE TABLE `Note` (
  `noteId` CHAR(36) NOT NULL PRIMARY KEY,
  `accountId` CHAR(36) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `content` TEXT NOT NULL,
  `createDate` DATETIME NOT NULL DEFAULT NOW(),
  `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE,
  `isProtected` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `NoteSharing`
CREATE TABLE `NoteSharing` (
  `noteSharingId` CHAR(36) NOT NULL PRIMARY KEY,
  `noteId` CHAR(36) NOT NULL,
  `timeShared` DATETIME NOT NULL DEFAULT NOW(),
  `canEdit` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `NoteLabel`
CREATE TABLE `NoteLabel` (
  `noteLabelId` CHAR(36) NOT NULL PRIMARY KEY,
  `noteId` CHAR(36) NOT NULL,
  `labelName` VARCHAR(200) NOT NULL,
  `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `NoteProtect`
CREATE TABLE `NoteProtect` (
  `noteProtectId` CHAR(36) NOT NULL PRIMARY KEY,
  `noteId` CHAR(36) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `isEnabled` BOOLEAN NOT NULL DEFAULT FALSE,
  `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `Modification`
CREATE TABLE `Modification` (
  `modifyId` CHAR(36) NOT NULL PRIMARY KEY,
  `noteId` CHAR(36) NOT NULL,
  `isPinned` BOOLEAN NOT NULL DEFAULT FALSE,
  `pinnedTime` DATETIME DEFAULT NULL,
  `isShared` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `Image`
CREATE TABLE `Image` (
  `imageId` CHAR(36) NOT NULL PRIMARY KEY,
  `noteId` CHAR(36) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `imageLink` VARCHAR(200) NOT NULL,
  `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE
);

-- Table structure for table `LogNote`
CREATE TABLE `LogNote` (
  `logNoteId` CHAR(36) NOT NULL PRIMARY KEY,
  `noteId` CHAR(36) NOT NULL,
  `content` TEXT NOT NULL,
  `process` VARCHAR(200) NOT NULL,
  `updateTime` DATETIME NOT NULL DEFAULT NOW(),
  `flag` VARCHAR(20) NOT NULL
);


-- --------------------------------------------------------

-- Enable UUID function in MySQL
SET @uuid1 = UUID();
SET @uuid2 = UUID();
SET @uuid3 = UUID();
SET @uuid4 = UUID();
SET @uuid5 = UUID();
SET @uuid6 = UUID();
SET @uuid7 = UUID();
SET @uuid8 = UUID();
SET @uuid9 = UUID();
SET @uuid10 = UUID();
SET @uuid11 = UUID();
SET @uuid12 = UUID();
SET @uuid13 = UUID();
SET @uuid14 = UUID();
SET @uuid15 = UUID();
SET @uuid16 = UUID();
SET @uuid17 = UUID();
SET @uuid18 = UUID();
SET @uuid19 = UUID();
SET @uuid20 = UUID();

-- Insert data into `Account`
INSERT INTO `Account` (`accountId`, `userName`, `password`, `isDeleted`, `email`, `roleId`)
VALUES
  (@uuid1, 'john_doe', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', FALSE, 'john@example.com', 1),
  (@uuid2, 'jane_smith', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', FALSE, 'jane@example.com', 2),
  (@uuid3, 'michael_adams', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', FALSE, 'michael@example.com', 3),
  (@uuid4, 'emily_jones', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', FALSE, 'emily@example.com', 4),
  (@uuid5, 'alex_brown', '$2y$10$0lTiUI79Y2XYyKjMpo1S2uoNLjY3w1aozxqFXry9RCNWGDIYup8ga', FALSE, 'alex@example.com', 2);

-- Insert data into `Preference`

INSERT INTO `Preference` (`preferenceId`, `accountId`, `layout`, `noteFont`, `noteColor`, `font`, `isDarkTheme`)
VALUES
  (UUID(), @uuid1, 'grid', 'Arial', '#ffcc00', 'Calibri', TRUE),
  (UUID(), @uuid2, 'list', 'Times New Roman', '#00ffcc', 'Verdana', FALSE),
  (UUID(), @uuid3, 'list', 'Courier New', '#ff5733', 'Arial', TRUE),
  (UUID(), @uuid4, 'grid', 'Helvetica', '#33ff57', 'Tahoma', FALSE),
  (UUID(), @uuid5, 'grid', 'Georgia', '#5733ff', 'Sans-serif', TRUE);

-- Insert data into `Users`

INSERT INTO `Users` (`userId`, `accountId`, `firstName`, `lastName`, `email`, `phone`, `address`, `profilePicture`, `isDeleted`)
VALUES
  (UUID(), @uuid1, 'John', 'Doe', 'john@example.com', '123-456-7890', '123 Main St', 'john_profile.jpg', FALSE),
  (UUID(), @uuid2, 'Jane', 'Smith', 'jane@example.com', '987-654-3210', '456 Elm St', 'jane_profile.jpg', FALSE),
  (UUID(), @uuid3, 'Michael', 'Adams', 'michael@example.com', '555-123-4567', '789 Pine St', 'michael_profile.jpg', FALSE),
  (UUID(), @uuid4, 'Emily', 'Jones', 'emily@example.com', '555-987-6543', '321 Oak St', 'emily_profile.jpg', FALSE),
  (UUID(), @uuid5, 'Alex', 'Brown', 'alex@example.com', '333-666-9999', '654 Maple St', 'alex_profile.jpg', FALSE);

-- Insert data into `Note`

INSERT INTO `Note` (`noteId`, `accountId`, `title`, `content`, `createDate`, `isDeleted`, `isProtected`)
VALUES
  (UUID(), @uuid1, 'Grocery List', 'Milk, Eggs, Bread, Butter', NOW(), FALSE, FALSE),
  (UUID(), @uuid2, 'Project Plan', 'Finish phase 1 by next week', NOW(), FALSE, TRUE),
  (UUID(), @uuid3, 'Meeting Notes', 'Discussed budget planning', NOW(), FALSE, FALSE),
  (UUID(), @uuid4, 'Workout Plan', 'Monday: Cardio, Tuesday: Strength', NOW(), FALSE, FALSE),
  (UUID(), @uuid5, 'Vacation Ideas', 'Planning trip to Italy', NOW(), FALSE, TRUE);

-- 15 notes for @uuid1
INSERT INTO `Note` (`noteId`, `accountId`, `title`, `content`, `createDate`, `isDeleted`, `isProtected`)
VALUES
    (UUID(), @uuid1, 'Grocery List', 'Milk, Eggs, Bread, Butter', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Project Plan', 'Finish phase 1 by next week', NOW(), FALSE, TRUE),
    (UUID(), @uuid1, 'Meeting Notes', 'Discussed budget planning', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Workout Plan', 'Monday: Cardio, Tuesday: Strength', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Vacation Ideas', 'Planning trip to Italy', NOW(), FALSE, TRUE),
    (UUID(), @uuid1, 'Reading List', 'Atomic Habits, Deep Work', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Birthday Plans', 'Dinner with friends, cake pickup', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Shopping List', 'Shoes, Jacket, Hat', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Chores', 'Clean room, do laundry', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'App Ideas', 'Note app, Budget tracker', NOW(), FALSE, TRUE),
    (UUID(), @uuid1, 'Journal Entry', 'Had a productive day', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Recipe Notes', 'Pasta with tomato sauce', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Study Topics', 'Math, History, Programming', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Wishlist', 'New laptop, Headphones', NOW(), FALSE, FALSE),
    (UUID(), @uuid1, 'Goals', 'Run 5k, Read 10 books', NOW(), FALSE, TRUE);

-- 15 notes for @uuid2
INSERT INTO `Note` (`noteId`, `accountId`, `title`, `content`, `createDate`, `isDeleted`, `isProtected`)
VALUES
    (UUID(), @uuid2, 'Grocery List', 'Eggs, Chicken, Rice, Coffee', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Travel Checklist', 'Passport, Tickets, Camera', NOW(), FALSE, TRUE),
    (UUID(), @uuid2, 'Daily Routine', 'Wake up at 7, Workout at 8', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Workout Log', 'Squats, Bench Press, Deadlifts', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Movie Watchlist', 'Inception, Interstellar', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Startup Notes', 'Pitch deck, MVP features', NOW(), FALSE, TRUE),
    (UUID(), @uuid2, 'Study Plan', 'Revise algorithms, system design', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Project Deadlines', 'Submit report by Monday', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Ideas Dump', 'SaaS platform, Content creator tools', NOW(), FALSE, TRUE),
    (UUID(), @uuid2, 'Reflection', 'Week went better than expected', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Fitness Goals', 'Lose 5kg, Gain muscle', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Meal Plan', 'High protein, low carb', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Learning Notes', 'GraphQL, Docker basics', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Wishlist', 'Smartwatch, Bluetooth speaker', NOW(), FALSE, FALSE),
    (UUID(), @uuid2, 'Goals', 'Launch side project, 10k steps daily', NOW(), FALSE, TRUE);

-- Insert data into `NoteSharing`

INSERT INTO `NoteSharing` (`noteSharingId`, `noteId`, `timeShared`, `canEdit`)
VALUES
  (UUID(), @uuid1, NOW(), TRUE),
  (UUID(), @uuid2, NOW(), FALSE),
  (UUID(), @uuid3, NOW(), TRUE),
  (UUID(), @uuid4, NOW(), FALSE),
  (UUID(), @uuid5, NOW(), TRUE);

-- Insert data into `NoteLabel`

INSERT INTO `NoteLabel` (`noteLabelId`, `noteId`, `labelName`, `isDeleted`)
VALUES
  (UUID(), @uuid1, 'Personal', FALSE),
  (UUID(), @uuid2, 'Work', FALSE),
  (UUID(), @uuid3, 'Meetings', FALSE),
  (UUID(), @uuid4, 'Fitness', FALSE),
  (UUID(), @uuid5, 'Travel', FALSE);

-- Insert data into `NoteProtect`

INSERT INTO `NoteProtect` (`noteProtectId`, `noteId`, `password`, `isEnabled`, `isDeleted`)
VALUES
  (UUID(), @uuid2, 'securepassword123', TRUE, FALSE),
  (UUID(), @uuid5, 'supersecret321', TRUE, FALSE);

-- Insert data into `Modification`

INSERT INTO `Modification` (`modifyId`, `noteId`, `isPinned`, `pinnedTime`, `isShared`)
VALUES
  (UUID(), @uuid1, TRUE, NOW(), TRUE),
  (UUID(), @uuid2, FALSE, NULL, TRUE),
  (UUID(), @uuid3, TRUE, NOW(), FALSE),
  (UUID(), @uuid4, FALSE, NULL, TRUE),
  (UUID(), @uuid5, TRUE, NOW(), TRUE);

-- Insert data into `Image`

INSERT INTO `Image` (`imageId`, `noteId`, `title`, `imageLink`, `isDeleted`)
VALUES
  (UUID(), @uuid1, 'Shopping List Image', 'grocery_list.png', FALSE),
  (UUID(), @uuid2, 'Project Diagram', 'project_diagram.png', FALSE),
  (UUID(), @uuid3, 'Meeting Screenshot', 'meeting_notes.png', FALSE),
  (UUID(), @uuid4, 'Workout Poster', 'workout_plan.png', FALSE),
  (UUID(), @uuid5, 'Italy Travel Map', 'vacation_map.png', FALSE);



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
