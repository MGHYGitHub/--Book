/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 100432
 Source Host           : localhost:3306
 Source Schema         : book

 Target Server Type    : MySQL
 Target Server Version : 100432
 File Encoding         : 65001

 Date: 04/07/2024 12:12:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for book
-- ----------------------------
DROP TABLE IF EXISTS `book`;
CREATE TABLE `book`  (
  `bid` int NOT NULL AUTO_INCREMENT,
  `categorize` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分类编号',
  `bname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图书名称',
  `author` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '作者',
  `publishing` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '出版社',
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片',
  `price` decimal(5, 2) NULL DEFAULT NULL COMMENT '单价',
  PRIMARY KEY (`bid`) USING BTREE,
  INDEX `idx_bid`(`bid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 665545 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of book
-- ----------------------------
INSERT INTO `book` VALUES (112266, 'TP3/12', 'FoxBASE', '张三', '电子工业出版社', 'images/foxbase.jpg', 23.60);
INSERT INTO `book` VALUES (113388, 'TR7/90', '大学英语', '胡玲', '清华大学出版社', 'images/dxyy.jpg', 12.50);
INSERT INTO `book` VALUES (114455, 'TR9/12', '线性代数', '孙业', '北京大学出版社', 'images/xxds.jpg', 20.80);
INSERT INTO `book` VALUES (118801, 'TP4/15', '计算机网络', '黄力钧', '高等教育出版社', 'images/jsjwl.jpg', 21.80);
INSERT INTO `book` VALUES (332211, 'TP5/10', '计算机基础', '李伟', '高等教育出版社', 'images/jsjjc.jpg', 18.00);
INSERT INTO `book` VALUES (445503, 'TP3/12', '数据库导论', '王强', '科学出版社', 'images/sjkdl.jpg', 17.90);
INSERT INTO `book` VALUES (446601, 'TP4/13', '数据库基础', '马凌云', '人民邮电出版社', 'images/sjkjc.jpg', 23.00);
INSERT INTO `book` VALUES (449901, 'TR8/15', '人类简史', '尤瓦尔·赫拉利', '中信出版股份有限公司', 'images/rljs.jpg', 28.00);
INSERT INTO `book` VALUES (665544, 'TS7/21', '高等数学', '刘明', '高等教育出版社', 'images/gdsx.jpg', 20.00);

-- ----------------------------
-- Table structure for cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart`  (
  `uid` int NOT NULL COMMENT '用户编号',
  `bid` int NOT NULL COMMENT '图书编号',
  `quantity` int NULL DEFAULT NULL COMMENT '数量',
  PRIMARY KEY (`uid`, `bid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cart
-- ----------------------------
INSERT INTO `cart` VALUES (6, 118801, 2);
INSERT INTO `cart` VALUES (6, 446601, 1);
INSERT INTO `cart` VALUES (6, 449901, 2);

-- ----------------------------
-- Table structure for favorites
-- ----------------------------
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `fk_favorites_books`(`book_id` ASC) USING BTREE,
  INDEX `fk_favorites_users`(`user_id` ASC) USING BTREE,
  CONSTRAINT `fk_favorites_books` FOREIGN KEY (`book_id`) REFERENCES `book` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favorites_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 79 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of favorites
-- ----------------------------
INSERT INTO `favorites` VALUES (1, 6, 118801);
INSERT INTO `favorites` VALUES (2, 6, 445503);
INSERT INTO `favorites` VALUES (3, 6, 112266);
INSERT INTO `favorites` VALUES (4, 6, 446601);
INSERT INTO `favorites` VALUES (5, 6, 665544);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`email` ASC) USING BTREE,
  INDEX `idx_id`(`id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (6, 'admin', 'admin@qq.com', '$2y$10$gqqqjJKdaAVFYm5Uj0Dy4O/YjfTHLU9PLcoBWJaY1HkQ1By.LOC/a', '2024-07-04 11:40:02');

SET FOREIGN_KEY_CHECKS = 1;
