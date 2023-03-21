CREATE TABLE `etl_ds` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
`type` varchar(60) NOT NULL DEFAULT '' COMMENT '驱动类型：mysql/oracle/mssql',
`db_host` varchar(60) NOT NULL DEFAULT '' COMMENT '主机名',
`db_port` int(11) NOT NULL DEFAULT '0' COMMENT '端口号',
`db_user` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
`db_pass` varchar(60) NOT NULL DEFAULT '' COMMENT '密码',
`db_name` varchar(60) NOT NULL DEFAULT '' COMMENT '库名',
`db_charset` varchar(60) NOT NULL DEFAULT '' COMMENT '字符编码',
`remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
`is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据源';

ALTER TABLE `etl_ds`
ADD PRIMARY KEY (`id`);


CREATE TABLE `etl_flow_category` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
`ordering` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
`is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='数据流分类';

ALTER TABLE `etl_flow_category`
ADD PRIMARY KEY (`id`);


CREATE TABLE `etl_flow` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
`category_id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
`schedule` varchar(60) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '执行计划',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
`is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='数据流';

ALTER TABLE `etl_flow`
ADD PRIMARY KEY (`id`),
ADD KEY `category_id` (`category_id`);


CREATE TABLE `etl_flow_node` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '数据流ID',
`index` tinyint(4) NOT NULL DEFAULT '0' COMMENT '编号',
`type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'input: 输入/process: 处理/output: 输出',
`item_type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'input_ds: 输入数据源/process_code: 代码处理/output_ds: 输出数据源/output_csv: 输出CSV/output_files: 输出文件包/output_folders: 输出目录包/output_api: 输出API调用',
`item_id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='数据流节点';

ALTER TABLE `etl_flow_node`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_id` (`flow_id`);



CREATE TABLE `etl_flow_node_input_ds` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`ds_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据源ID',
`ds_type` varchar(30) NOT NULL DEFAULT 'table' COMMENT '来源数据源类型（table - 表 / sql：SQL',
`ds_table` varchar(60) NOT NULL DEFAULT '' COMMENT '来源数据源表名',
`ds_sql` varchar(3000) NOT NULL DEFAULT '' COMMENT '来源数据源SQL',
`breakpoint` varchar(30) NOT NULL DEFAULT 'full' COMMENT '断点类型（full：全量/breakpoint：有断点）',
`breakpoint_field` varchar(60) NOT NULL DEFAULT '' COMMENT '断点字段',
`breakpoint_time` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:00' COMMENT '断点时间',
`breakpoint_step` varchar(60) NOT NULL DEFAULT '1_DAY' COMMENT '断点递增量(1_HOUR:一小时/1_DAY:一天/1_MONTH:一个月)',
`breakpoint_offset` INT NOT NULL DEFAULT '0' COMMENT '断点向前偏移量（秒）',
`output` text NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输入节点-数据源';

ALTER TABLE `etl_flow_node_input_ds`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);



CREATE TABLE `etl_flow_node_process_code` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`code` text NOT NULL COMMENT '代码处理',
`output` text NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库处理节点-代码处理';

ALTER TABLE `etl_flow_node_process_code`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);



CREATE TABLE `etl_flow_node_output_ds` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`ds_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据源ID',
`ds_table` varchar(60) NOT NULL DEFAULT '' COMMENT '数据源表名',
`field_mapping` varchar(30) NOT NULL DEFAULT 'mapping' COMMENT '字段映射类型（mapping：字段映射/code：代码处理）',
`field_mapping_details` text NOT NULL COMMENT '字段映射',
`field_mapping_code` text NOT NULL COMMENT '代码映射',
`output` text NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-数据源';

ALTER TABLE `etl_flow_node_output_ds`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);



CREATE TABLE `etl_flow_node_output_csv` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`field_mapping` varchar(30) NOT NULL DEFAULT 'mapping' COMMENT '字段映射类型（mapping：字段映射/code：代码处理）',
`field_mapping_details` text NOT NULL COMMENT '字段映射',
`field_mapping_code` text NOT NULL COMMENT '代码映射',
`output` text NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-CSV';

ALTER TABLE `etl_flow_node_output_csv`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);



CREATE TABLE `etl_flow_node_output_files` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`name` varchar(30) NOT NULL DEFAULT 'template' COMMENT '字件名生成方式（template：模板/code：代码处理）',
`name_template` varchar(300) NOT NULL DEFAULT '' COMMENT '字件名模板',
`name_code` varchar(1000) NOT NULL DEFAULT '' COMMENT '文件名代码处理',
`content` varchar(30) NOT NULL DEFAULT 'template' COMMENT '文件内容生成方式（template：模板/code：代码处理）',
`content_template` varchar(300) NOT NULL DEFAULT '' COMMENT '文件内容模板',
`content_code` varchar(1000) NOT NULL DEFAULT '' COMMENT '文件内容代码处理',
`output` text NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-文件包';

ALTER TABLE `etl_flow_node_output_files`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);



CREATE TABLE `etl_flow_node_output_folders` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`name` varchar(30) NOT NULL DEFAULT 'template' COMMENT '目录生成方式（template：模板/code：代码处理）',
`name_template` varchar(300) NOT NULL DEFAULT '' COMMENT '目录名模板',
`name_code` varchar(1000) NOT NULL DEFAULT '' COMMENT '目录代码处理',
`files` text NOT NULL COMMENT '目录内文件列表',
`files_code` text NOT NULL COMMENT '代码输出文件列表',
`output` text NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-目录包';

ALTER TABLE `etl_flow_node_output_folders`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);


CREATE TABLE `etl_flow_node_output_api` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`url` varchar(300) NOT NULL DEFAULT '' COMMENT  '发布网址',
`headers` text NOT NULL COMMENT  '请求头',
`format` varchar(30) NOT NULL DEFAULT 'form' COMMENT  '请求格式（form/json）',
`field_mapping` varchar(30) NOT NULL DEFAULT 'mapping' COMMENT '字段映射类型（mapping：字段映射/code：代码处理）',
`field_mapping_details` text NOT NULL COMMENT '字段映射',
`field_mapping_code` text NOT NULL COMMENT '代码映射',
`success_mark` varchar(60) NOT NULL DEFAULT '' COMMENT  '成功标识',
`interval` int(11) NOT NULL DEFAULT '1000' COMMENT '间隔时间（毫秒）',
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-API调用';

ALTER TABLE `etl_flow_node_output_api`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);


CREATE TABLE `etl_flow_log` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_id` varchar(36) NOT NULL DEFAULT '' COMMENT '抽取任务ID',
`status` varchar(30) NOT NULL DEFAULT 'create' COMMENT '状态（create：创建/running：运行中/finish：执行完成/error：出错）',
`message` varchar(600) NOT NULL DEFAULT '' COMMENT '异常信息',
`complete_time` TIMESTAMP NULL DEFAULT '1970-01-02 00:00:00' COMMENT '完成时间',
`total` int(11) NOT NULL DEFAULT '1000' COMMENT '总数据数',
`total_success` int(11) NOT NULL DEFAULT '1000' COMMENT '总成功数据数',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据流日志';

ALTER TABLE `etl_flow_log`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_id` (`flow_id`);


CREATE TABLE `etl_flow_node_log` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_log_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流日志ID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`config` text NOT NULL COMMENT '配置数据 序列化',
`output_file` varchar(600) NOT NULL DEFAULT '' COMMENT '最终输出的文件',
`total_success` int(11) NOT NULL DEFAULT '1000' COMMENT '总成功数据数',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据流节点日志';

ALTER TABLE `etl_flow_node_log`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_log_id` (`flow_log_id`);


CREATE TABLE `etl_flow_node_item_log` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_log_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点日志ID',
`input` text NOT NULL COMMENT '输入数据（php序列化）',
`output` text NOT NULL COMMENT '输出数据（php序列化）',
`success` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否成功',
`message` varchar(600) NOT NULL DEFAULT '' COMMENT '异常信息',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据流节点项目日志';

ALTER TABLE `etl_flow_node_item_log`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_log_id` (`flow_node_log_id`);

