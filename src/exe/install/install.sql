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
`item_type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'input_ds: 输入数据源/input_material: 输入素材/input_code: 输入代码/process_code: 代码处理/output_ds: 输出数据源/output_csv: 输出CSV/output_files: 输出文件包/output_folders: 输出目录包/output_api: 输出API调用/output_material: 输出到素材',
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
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输入节点-数据源';

ALTER TABLE `etl_flow_node_input_ds`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);


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



CREATE TABLE `etl_flow_node_process_clean` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`clean_field` varchar(60) NOT NULL DEFAULT 'assign' COMMENT '清洗字段',
`clean_values` text NOT NULL COMMENT '清洗掉的内容列表',
`insert_tags` tinyint(4) NOT NULL DEFAULT '0' COMMENT '插入标签',
`match_case` tinyint(4) NOT NULL DEFAULT '0' COMMENT '区分大小写',
`sign` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标记清洗过',
`sign_field` varchar(60) NOT NULL DEFAULT '' COMMENT '标记字段名',
`sign_field_value_0` varchar(60) NOT NULL DEFAULT '' COMMENT '标记字段值（默认值）',
`sign_field_value_1` varchar(60) NOT NULL DEFAULT '' COMMENT '标记字段值（已清洗）',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库处理节点-清洗';

ALTER TABLE `etl_flow_node_process_clean`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);


CREATE TABLE `etl_flow_node_process_filter` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`filter_field` varchar(60) NOT NULL DEFAULT 'assign' COMMENT '过滤字段',
`filter_op` varchar(60) NOT NULL DEFAULT 'include' COMMENT '过滤操作(include：钇含/start：以...开头/end：以...结尾/eq：等于/gt：大于/gte：大于等于/lt：小于/lte：小于等于/between：范围)',
`filter_values` text NOT NULL COMMENT '过滤值列表',
`insert_tags` tinyint(4) NOT NULL DEFAULT '0' COMMENT '插入标签',
`match_case` tinyint(4) NOT NULL DEFAULT '0' COMMENT '区分大小写',
`op` varchar(30) NOT NULL DEFAULT 'allow' COMMENT '操作（allow：符合条件的放行/deny：符合条件的中止处理）',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库处理节点-过滤';

ALTER TABLE `etl_flow_node_process_filter`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);




CREATE TABLE `etl_flow_node_process_chatgpt` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`system_prompt` text NOT NULL COMMENT '系统提示语',
`user_prompt` text NOT NULL COMMENT '用户提示语',
`output_field` varchar(30) NOT NULL DEFAULT 'assign' COMMENT '输出字段（assign：指定现有字段/custom：自定议）',
`output_field_assign` varchar(60) NOT NULL DEFAULT '' COMMENT '输出字段：指定字段',
`output_field_custom` varchar(60) NOT NULL DEFAULT '' COMMENT '输出字段：自定义字段',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库处理节点-代码处理';

ALTER TABLE `etl_flow_node_process_chatgpt`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_id` (`flow_node_id`);




CREATE TABLE `etl_flow_node_process_code` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_node_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流节点ID',
`code` text NOT NULL COMMENT '代码处理',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
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
`op` varchar(30) NOT NULL DEFAULT 'auto' COMMENT '数据操作类型（auto：插入，重复数据更新/insert：插入/update：更新/delete：删除）',
`op_field` varchar(60) NOT NULL DEFAULT 'id' COMMENT '更新/删除操作的唯一键字段',
`mysql_replace` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用 MYSQL 数据库 Replace Into',
`clean` tinyint(4) NOT NULL DEFAULT '0' COMMENT '运行前清空数据表（如：全量同步时）',
`clean_type` varchar(60) NOT NULL DEFAULT 'truncate' COMMENT '清空数据表方式（truncate：TRUNCATE/delete：DELETE）',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
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
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
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
`file_exist` varchar(30) NOT NULL DEFAULT 'override' COMMENT '同名文件操作(override - 覆盖 / append - 追加)',
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
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
`output` mediumtext NOT NULL COMMENT '输出（php序列化）',
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
`output` mediumtext NOT NULL COMMENT '输出（键值对数组，php序列化）',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据库输出节点-API调用';

ALTER TABLE `etl_flow_node_output_api`
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


CREATE TABLE `etl_flow_log` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`flow_id` varchar(36) NOT NULL DEFAULT '' COMMENT '数据流ID',
`status` varchar(30) NOT NULL DEFAULT 'create' COMMENT '状态（create：创建/running：运行中/finish：执行完成/error：出错）',
`message` varchar(600) NOT NULL DEFAULT '' COMMENT '异常信息',
`finish_time` TIMESTAMP NULL DEFAULT '1970-01-02 00:00:00' COMMENT '完成时间',
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
`index` tinyint(4) NOT NULL DEFAULT '0' COMMENT '编号',
`type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'input: 输入/process: 处理/output: 输出',
`item_type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'input_ds: 输入数据源/process_code: 代码处理/output_ds: 输出数据源/output_csv: 输出CSV/output_files: 输出文件包/output_folders: 输出目录包/output_api: 输出API调用',
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
`input` mediumtext NOT NULL COMMENT '输入数据（php序列化）',
`output` mediumtext NOT NULL COMMENT '输出数据（php序列化）',
`success` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否成功',
`message` varchar(600) NOT NULL DEFAULT '' COMMENT '异常信息',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据流节点项目日志';

ALTER TABLE `etl_flow_node_item_log`
ADD PRIMARY KEY (`id`),
ADD KEY `flow_node_log_id` (`flow_node_log_id`);



CREATE TABLE `etl_material` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(180) NOT NULL DEFAULT '' COMMENT '名称',
`fields` text NOT NULL COMMENT '字段参数',
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
