

CREATE TABLE `etl_flow_node_input_material` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`material_id` varchar(36) NOT NULL DEFAULT '' COMMENT '素材ID',
`breakpoint` varchar(30) NOT NULL DEFAULT 'full' COMMENT '断点类型（full：全量/breakpoint：有断点）',
`breakpoint_field` varchar(60) NOT NULL DEFAULT '' COMMENT '断点字段',
`breakpoint_time` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:00' COMMENT '断点时间',
`breakpoint_step` varchar(60) NOT NULL DEFAULT '1_DAY' COMMENT '断点递增量(1_HOUR:一小时/1_DAY:一天/1_MONTH:一个月)',
`breakpoint_offset` INT NOT NULL DEFAULT '0' COMMENT '断点向前偏移量（秒）',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输入节点-素材';

ALTER TABLE `etl_flow_node_input_material`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);


CREATE TABLE `etl_flow_node_input_code` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`code` text NOT NULL COMMENT '输入代码',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输入节点-素材';

ALTER TABLE `etl_flow_node_input_code`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);


CREATE TABLE `etl_flow_node_output_material` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`material_id` varchar(36) NOT NULL DEFAULT '' COMMENT '素材ID',
`field_mapping` varchar(30) NOT NULL DEFAULT 'mapping' COMMENT '字段映射类型（mapping：字段映射/code：代码处理）',
`field_mapping_details` text NOT NULL COMMENT '字段映射',
`field_mapping_code` text NOT NULL COMMENT '代码映射',
`op` varchar(30) NOT NULL DEFAULT 'auto' COMMENT '数据操作类型（auto：插入，重复数据更新/insert：插入/update：更新/delete：删除）',
`op_field` varchar(60) NOT NULL DEFAULT 'id' COMMENT '更新/删除操作的唯一键字段',
`clean` tinyint(4) NOT NULL DEFAULT '0' COMMENT '运行前清空数据表（如：全量同步时）',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-数据源';

ALTER TABLE `etl_flow_node_output_material`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);
