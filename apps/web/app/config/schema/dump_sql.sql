-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 06, 2022 at 07:20 AM
-- Server version: 5.7.26
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `admins` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日';
ALTER TABLE `admins` CHANGE `modified` `modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL COMMENT '更新日';
ALTER TABLE `admins` CHANGE `name` `name` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '氏名';
ALTER TABLE `admins` CHANGE `username` `username` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ログインアカウント';
ALTER TABLE `admins` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'パスワード ';
ALTER TABLE `admins` CHANGE `role` `role` INT(11) NULL DEFAULT '0' COMMENT '権限';


--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `created`, `modified`, `name`, `username`, `password`, `role`) VALUES
(1, '2021-01-04 17:34:19', '2021-01-04 17:34:19', '管理者', 'caters_admin', '$2y$10$7X.icRPhUBnFrsoBR784y.VMC9IrXxbbinEff3WMGa0N.WG3D8kH6', 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `page_config_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('draft','publish') NOT NULL DEFAULT 'publish',
  `name` varchar(40) NOT NULL DEFAULT '',
  `identifier` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `infos`
--

CREATE TABLE `infos` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `page_config_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('draft','publish') NOT NULL DEFAULT 'draft',
  `title` varchar(100) NOT NULL DEFAULT '',
  `notes` text,
  `start_date` date DEFAULT NULL,
  `start_time` decimal(4,0) NOT NULL DEFAULT '0',
  `end_date` date DEFAULT NULL,
  `end_time` decimal(4,0) NOT NULL DEFAULT '0',
  `image` varchar(100) NOT NULL DEFAULT '',
  `meta_description` varchar(200) NOT NULL DEFAULT '',
  `meta_keywords` varchar(200) NOT NULL DEFAULT '',
  `regist_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `index_type` decimal(1,0) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `info_contents`
--

CREATE TABLE `info_contents` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `info_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `block_type` decimal(2,0) NOT NULL DEFAULT '0',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `image` varchar(100) NOT NULL DEFAULT '',
  `image_pos` varchar(10) NOT NULL DEFAULT '',
  `file` varchar(100) NOT NULL DEFAULT '',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `file_name` varchar(100) NOT NULL DEFAULT '',
  `file_extension` varchar(10) NOT NULL DEFAULT '',
  `section_sequence_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `option_value` varchar(255) NOT NULL DEFAULT '',
  `option_value2` varchar(40) NOT NULL DEFAULT '',
  `option_value3` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `info_tags`
--

CREATE TABLE `info_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `info_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tag_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kvs`
--

CREATE TABLE `kvs` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `key_name` varchar(40) NOT NULL DEFAULT '',
  `val` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mst_literal`
--

CREATE TABLE `mst_literal` (
  `id` int(10) UNSIGNED NOT NULL,
  `ltrl_sys_kb` decimal(1,0) NOT NULL DEFAULT '0' COMMENT '0:通常、1:管理用',
  `ltrl_kb` char(3) NOT NULL,
  `position` decimal(3,0) NOT NULL COMMENT '表示順',
  `status` enum('publish','draft') NOT NULL DEFAULT 'publish',
  `ltrl_cd` char(10) NOT NULL,
  `ltrl_nm` varchar(60) DEFAULT NULL,
  `ltrl_val` varchar(60) DEFAULT NULL,
  `ltrl_sub_val` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `page_configs`
--

CREATE TABLE `page_configs` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `site_config_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `page_title` varchar(100) NOT NULL DEFAULT '',
  `slug` varchar(40) NOT NULL DEFAULT '',
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `is_public_date` decimal(1,0) NOT NULL DEFAULT '0',
  `is_public_time` decimal(1,0) NOT NULL DEFAULT '0',
  `page_template_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `is_category` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_category_sort` enum('Y','N') NOT NULL DEFAULT 'N',
  `list_style` decimal(2,0) NOT NULL DEFAULT '1',
  `root_dir_type` decimal(1,0) NOT NULL DEFAULT '0',
  `link_color` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page_configs`
--

INSERT INTO `page_configs` (`id`, `created`, `modified`, `site_config_id`, `position`, `page_title`, `slug`, `header`, `footer`, `is_public_date`, `is_public_time`, `page_template_id`, `description`, `keywords`, `is_category`, `is_category_sort`, `list_style`, `root_dir_type`, `link_color`) VALUES
(1, '2021-01-04 17:34:21', '2021-01-04 17:34:21', 1, 1, 'お知らせ', 'information', '', '', '0', '0', 0, '', '', 'N', 'N', '1', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `page_templates`
--

CREATE TABLE `page_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('draft','publish') NOT NULL DEFAULT 'draft',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(60) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page_templates`
--

INSERT INTO `page_templates` (`id`, `created`, `modified`, `status`, `position`, `name`) VALUES
(1, '2021-01-04 17:34:20', '2021-01-04 17:34:20', 'publish', 1, '標準');

-- --------------------------------------------------------

--
-- Table structure for table `section_sequences`
--

CREATE TABLE `section_sequences` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `info_content_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `site_configs`
--

CREATE TABLE `site_configs` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('draft','publish') NOT NULL DEFAULT 'draft',
  `site_name` varchar(100) NOT NULL DEFAULT '',
  `slug` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_configs`
--

INSERT INTO `site_configs` (`id`, `created`, `modified`, `position`, `status`, `site_name`, `slug`) VALUES
(1, '2021-01-04 17:34:20', '2021-01-04 17:34:20', 1, 'draft', 'サンプルサイト', 'sample');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `tag` varchar(40) NOT NULL DEFAULT '',
  `status` enum('publish','draft') NOT NULL DEFAULT 'publish',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `page_config_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `email` varchar(200) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL DEFAULT '',
  `password_hash` varchar(200) NOT NULL DEFAULT '',
  `temp_password` varchar(40) NOT NULL DEFAULT '',
  `temp_pass_expired` datetime NOT NULL ,
  `temp_key` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(60) NOT NULL DEFAULT '',
  `status` enum('publish','draft') NOT NULL DEFAULT 'publish',
  `role` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日';
ALTER TABLE `users` CHANGE `modified` `modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL COMMENT '更新日';
ALTER TABLE `users` CHANGE `email` `email` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'メールアドレス';
ALTER TABLE `users` CHANGE `username` `username` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ログインアカウント';
ALTER TABLE `users` CHANGE `password_hash` `password_hash` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'パスワードハッシュ';
ALTER TABLE `users` CHANGE `temp_password` `temp_password` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'パスワードテンプレート';
ALTER TABLE `users` CHANGE `role` `role` INT(10) NULL DEFAULT '1' COMMENT '権限';
ALTER TABLE `users` CHANGE `temp_pass_expired` `temp_pass_expired` DATETIME NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `status` `status` ENUM('publish','draft') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'publish' COMMENT 'ステイタス';
ALTER TABLE `users` CHANGE `name` `name` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '氏名';
ALTER TABLE `users` CHANGE `temp_key` `temp_key` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

ALTER TABLE `users` DROP `temp_pass_expired`, DROP `temp_key`;
-- --------------------------------------------------------

--
-- Table structure for table `user_sites`
--

CREATE TABLE `user_sites` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `site_config_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `infos`
--
ALTER TABLE `infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info_contents`
--
ALTER TABLE `info_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info_tags`
--
ALTER TABLE `info_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kvs`
--
ALTER TABLE `kvs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_literal`
--
ALTER TABLE `mst_literal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqe_sys_kb_kb_cd` (`ltrl_sys_kb`,`ltrl_kb`,`ltrl_cd`);

--
-- Indexes for table `page_configs`
--
ALTER TABLE `page_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_templates`
--
ALTER TABLE `page_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section_sequences`
--
ALTER TABLE `section_sequences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_configs`
--
ALTER TABLE `site_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_sites`
--
ALTER TABLE `user_sites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `infos`
--
ALTER TABLE `infos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `info_contents`
--
ALTER TABLE `info_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `info_tags`
--
ALTER TABLE `info_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kvs`
--
ALTER TABLE `kvs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mst_literal`
--
ALTER TABLE `mst_literal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_configs`
--
ALTER TABLE `page_configs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `page_templates`
--
ALTER TABLE `page_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `section_sequences`
--
ALTER TABLE `section_sequences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_configs`
--
ALTER TABLE `site_configs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_sites`
--
ALTER TABLE `user_sites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;




-- 追加項目
CREATE TABLE append_items
(
	id int unsigned NOT NULL AUTO_INCREMENT,
	created datetime NOT NULL,
	modified datetime NOT NULL,
	page_config_id int unsigned DEFAULT 0 NOT NULL,
	position int unsigned DEFAULT 0 NOT NULL,
	name varchar(40) DEFAULT '' NOT NULL,
	slug varchar(30) DEFAULT '' NOT NULL,
	value_type decimal(2) DEFAULT 0 NOT NULL,
	max_length int unsigned DEFAULT 0 NOT NULL,
	is_required decimal(1) unsigned DEFAULT 0 NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 記事追加項目
CREATE TABLE info_append_items
(
	id int unsigned NOT NULL AUTO_INCREMENT,
	created datetime NOT NULL,
	modified datetime NOT NULL,
	info_id int unsigned DEFAULT 0 NOT NULL,
	append_item_id int unsigned DEFAULT 0 NOT NULL,
	value_text varchar(200) DEFAULT '' NOT NULL,
	value_textarea text NOT NULL,
	value_date date  NOT NULL,
	value_datetime datetime  NOT NULL,
	value_time time DEFAULT 0 NOT NULL,
	value_int int unsigned DEFAULT 0 NOT NULL,
	value_decimal decimal(3) unsigned DEFAULT 0 NOT NULL,
	file varchar(100) DEFAULT '' NOT NULL,
	file_size int unsigned DEFAULT 0 NOT NULL,
	file_name varchar(100) DEFAULT '' NOT NULL,
	file_extension varchar(10) DEFAULT '' NOT NULL,
	image varchar(100) DEFAULT '' NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE mst_lists (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  position decimal(3,0) NOT NULL COMMENT '表示順',
  status enum('publish', 'draft') NOT NULL DEFAULT 'publish',
  use_target_id int unsigned DEFAULT 0 NOT NULL,
  ltrl_nm varchar(60) DEFAULT NULL,
  ltrl_val varchar(60) DEFAULT NULL,
  ltrl_sub_val text NULL,
  ltrl_slug varchar(100) DEFAULT '' NOT NULL,
  list_name varchar(100) DEFAULT '' NOT NULL,
  sys_cd decimal(2) DEFAULT '0' NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE `user_sites` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `user_sites` CHANGE `modified` `modified` DATETIME on update CURRENT_TIMESTAMP NULL;

ALTER TABLE `page_configs` CHANGE `header` `header` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `page_configs` CHANGE `footer` `footer` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


ALTER TABLE `infos` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日', 
CHANGE `modified` `modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL COMMENT '更新日', 
CHANGE `status` `status` ENUM('draft','publish') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'draft' COMMENT 'ステイタス', 
CHANGE `title` `title` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'タイトル', 
CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '概要', 
CHANGE `start_date` `start_date` DATE NOT NULL COMMENT '掲載日', 
CHANGE `start_time` `start_time` DECIMAL(4,0) NULL COMMENT '掲載時', 
CHANGE `end_time` `end_time` DECIMAL(4,0) NULL DEFAULT NULL, 
CHANGE `image` `image` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'イメージ', 
CHANGE `meta_description` `meta_description` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
CHANGE `meta_keywords` `meta_keywords` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
CHANGE `regist_user_id` `regist_user_id` INT(10) UNSIGNED NOT NULL COMMENT '操作者', CHANGE `category_id` `category_id` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `site_configs` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日', 
CHANGE `modified` `modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL COMMENT '更新日', 
CHANGE `status` `status` ENUM('draft','publish') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'draft' COMMENT 'ステイタス', 
CHANGE `site_name` `site_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'サイト名', 
CHANGE `slug` `slug` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ディレクトリ名';

ALTER TABLE `page_configs` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日', 
CHANGE `modified` `modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL COMMENT '更新日',
CHANGE `site_config_id` `site_config_id` INT(10) UNSIGNED NOT NULL COMMENT 'サイトコンフィグID	', 
CHANGE `page_title` `page_title` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'タイトル', 
CHANGE `slug` `slug` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'slug', 
CHANGE `header` `header` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ヘッダー', 
CHANGE `footer` `footer` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'フッター', 
CHANGE `is_public_date` `is_public_date` DECIMAL(1,0) NOT NULL, 
CHANGE `description` `description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
CHANGE `keywords` `keywords` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
CHANGE `link_color` `link_color` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '色';


CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` blob,
  `expires` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `page_configs` CHANGE `is_public_date` `is_public_date` DECIMAL(1,0) NULL;
ALTER TABLE `infos` CHANGE `category_id` `category_id` INT(10) UNSIGNED NULL;