/*
 Navicat Premium Data Transfer

 Source Server         : docker-mysql1
 Source Server Type    : MySQL
 Source Server Version : 80018
 Source Host           : 192.168.1.18:3300
 Source Schema         : testbase

 Target Server Type    : MySQL
 Target Server Version : 80018
 File Encoding         : 65001

 Date: 19/02/2020 16:48:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_permission
-- ----------------------------
DROP TABLE IF EXISTS `account_permission`;
CREATE TABLE `account_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(100) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of t_permission
-- ----------------------------
BEGIN;
INSERT INTO `account_permission` VALUES (1, 'user', 'index', '用户列表1', NULL);
INSERT INTO `account_permission` VALUES (2, 'user', 'save', '保存用户', NULL);
INSERT INTO `account_permission` VALUES (3, 'user', 'update', '更新用户', NULL);
INSERT INTO `account_permission` VALUES (4, 'user', 'delete', '删除用户', NULL);
INSERT INTO `account_permission` VALUES (5, 'user', 'read', '获取单个用户', NULL);
INSERT INTO `account_permission` VALUES (6, 'role', 'index', '角色列表', '');
INSERT INTO `account_permission` VALUES (7, 'role', 'save', '保存角色', '');
INSERT INTO `account_permission` VALUES (8, 'role', 'update', '更新角色', NULL);
INSERT INTO `account_permission` VALUES (9, 'role', 'delete', '删除角色', NULL);
INSERT INTO `account_permission` VALUES (10, 'role', 'read', '获取单个角色', NULL);
INSERT INTO `account_permission` VALUES (11, 'product', 'index', NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for t_role
-- ----------------------------
DROP TABLE IF EXISTS `account_role`;
CREATE TABLE `account_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `permission` varchar(255) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of t_role
-- ----------------------------
BEGIN;
INSERT INTO `account_role` VALUES (3, '财务经理', 1, '1,2,3,4,5,6,7,8,9,10,11', 1574505759, 1574747396);
INSERT INTO `account_role` VALUES (4, '编辑管理员', 1, '2,3', 1574505782, 1574505782);
INSERT INTO `account_role` VALUES (5, '编辑管理员', 1, '1,2,3,4,5,6,7,8,9', 1574754603, 1574754603);
INSERT INTO `account_role` VALUES (6, '编辑管理员', NULL, NULL, 1576038894, 1576038894);
COMMIT;

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `account_user`;
CREATE TABLE `account_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '用户名',
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '密码',
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` bigint(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '令牌',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of t_user
-- ----------------------------
BEGIN;
INSERT INTO `account_user` VALUES (1, 'admin', '10470c3b4b1fed12c3baac014be15fac67c6e815', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `account_user` VALUES (2, 'Van', '10470c3b4b1fed12c3baac014be15fac67c6e815', 'van@gmail.com', 18927705761, 1, 'xczczxc', NULL, 1574502520);
INSERT INTO `account_user` VALUES (3, 'LinlIn', '0987e3rwer', NULL, NULL, NULL, 'reter', NULL, NULL);
INSERT INTO `account_user` VALUES (9, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574389544, 1574389544);
INSERT INTO `account_user` VALUES (16, 'sadasd', '12345633333', NULL, NULL, NULL, NULL, 1574418351, 1574420308);
INSERT INTO `account_user` VALUES (17, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574420521, 1574420521);
INSERT INTO `account_user` VALUES (18, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574420546, 1574420546);
INSERT INTO `account_user` VALUES (19, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574420547, 1574420547);
INSERT INTO `account_user` VALUES (20, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574422208, 1574422208);
INSERT INTO `account_user` VALUES (21, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574422846, 1574422846);
INSERT INTO `account_user` VALUES (22, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574422847, 1574422847);
INSERT INTO `account_user` VALUES (23, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574422945, 1574422945);
INSERT INTO `account_user` VALUES (24, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574423043, 1574423043);
INSERT INTO `account_user` VALUES (25, 'Jack', '123456', NULL, NULL, NULL, NULL, 1574423367, 1574423367);
INSERT INTO `account_user` VALUES (26, 'eeeeee', '8cef9789f4539cf5ee3e9fdb730a30bbaf2c08a0', 'dsadada@dsadasda.com', 18927705761, 1, NULL, 1574423521, 1574495954);
INSERT INTO `account_user` VALUES (35, 'ewwrwrw', '68207758289131219d4108f407dee7c3b9354a76', NULL, NULL, NULL, NULL, 1574657531, 1574657531);
INSERT INTO `account_user` VALUES (36, 'Van112', '$P$BLXrH/br5uv4X5eUF9yqVpWJCz.NuD/', 'van@gmail.com', 18927698980, 1, NULL, 1574658144, 1576038893);
INSERT INTO `account_user` VALUES (37, 'VIViD', '5fef42224f7745c4f892ad9c2265a74e1842ab9f', 'vivid@hotmail.com', 17892205761, 1, NULL, 1574746215, 1574746215);
INSERT INTO `account_user` VALUES (38, 'VIViD', '5fef42224f7745c4f892ad9c2265a74e1842ab9f', 'vivid@hotmail.com', 17892205761, 1, NULL, 1574746216, 1574746216);
INSERT INTO `account_user` VALUES (39, 'VIViD', '5fef42224f7745c4f892ad9c2265a74e1842ab9f', 'vivid@hotmail.com', 17892205761, 1, NULL, 1574754301, 1574754301);
INSERT INTO `account_user` VALUES (40, 'VIViD', '5fef42224f7745c4f892ad9c2265a74e1842ab9f', 'vivid@hotmail.com', 17892205761, 1, NULL, 1574754603, 1574754603);
INSERT INTO `account_user` VALUES (41, 'VIViD9999', '$P$Bl33RV4jTn1VBfV8Yy3NyYdEQWHySO.', 'vivid@hotmail.com', 17892205761, 1, NULL, 1575008177, 1575008177);
INSERT INTO `account_user` VALUES (42, 'VIViD99999', '$P$B8JbJ5DGdEB81Ir00D8WHeDL.2yHHg/', 'vivid@hotmail.com', 17892205761, 1, NULL, 1575359676, 1575359676);
COMMIT;

-- ----------------------------
-- Table structure for userrole
-- ----------------------------
DROP TABLE IF EXISTS `userrole`;
CREATE TABLE `userrole` (
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of userrole
-- ----------------------------
BEGIN;
INSERT INTO `userrole` VALUES (2, 3);
INSERT INTO `userrole` VALUES (2, 4);
INSERT INTO `userrole` VALUES (1, 3);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
