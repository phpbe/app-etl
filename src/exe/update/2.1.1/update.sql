

CREATE TABLE `etl_material` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(180) NOT NULL DEFAULT '' COMMENT '名称',
`fields` mediumtext NOT NULL COMMENT '字段参数',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='素材';


ALTER TABLE `etl_material`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `name` (`name`);


CREATE TABLE `etl_material_item` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`material_id` varchar(36) NOT NULL DEFAULT '' COMMENT '素材ID',
`unique_key` varchar(180) NOT NULL COMMENT '唯一键',
`data` mediumtext NOT NULL COMMENT '数据',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='素材数据项';


ALTER TABLE `etl_material_item`
ADD PRIMARY KEY (`id`),
ADD KEY `material_id` (`material_id`,`unique_key`);
