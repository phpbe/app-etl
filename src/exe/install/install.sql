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
`node_qty` tinyint(4) NOT NULL DEFAULT '0' COMMENT '节点数',
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
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
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
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
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
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
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
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
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
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
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
`output` text NOT NULL COMMENT '输出（键值对数组，php序列化）',
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



CREATE TABLE `etl_extract` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
`category_id` varchar(36) NOT NULL DEFAULT '' COMMENT '分类ID',
`src_ds_id` varchar(36) NOT NULL DEFAULT '' COMMENT '来源数据源ID',
`dst_ds_id` varchar(36) NOT NULL DEFAULT '' COMMENT '目标数据源ID',
`src_type` varchar(30) NOT NULL DEFAULT 'table' COMMENT '来源数据源类型（table - 表 / sql：SQL',
`src_table` varchar(60) NOT NULL DEFAULT '' COMMENT '来源数据源表名',
`src_sql` varchar(3000) NOT NULL DEFAULT '' COMMENT '来源数据源SQL',
`dst_table` varchar(60) NOT NULL DEFAULT '' COMMENT '目标数据源表名',
`field_mapping_type` varchar(30) NOT NULL DEFAULT 'same' COMMENT '字段映射类型（same:完全一致/mapping：字段映射/code：代码处理）',
`field_mapping` text NOT NULL COMMENT '字段映射',
`field_mapping_code` text NOT NULL COMMENT '代码映射',
`breakpoint_type` varchar(30) NOT NULL DEFAULT 'full' COMMENT '断点类型（full：全量/breakpoint：有断点）',
`breakpoint_field` varchar(60) NOT NULL DEFAULT '' COMMENT '断点字段',
`breakpoint` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:00' COMMENT '断点',
`breakpoint_step` varchar(60) NOT NULL DEFAULT '1_DAY' COMMENT '断点递增量(1_HOUR:一小时/1_DAY:一天/1_MONTH:一个月)',
`breakpoint_offset` INT NOT NULL DEFAULT '0' COMMENT '断点向前偏移量（秒）',
`schedule` varchar(60) NOT NULL DEFAULT '' COMMENT '执行计划',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
`is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='抽取任务';


CREATE TABLE `etl_extract_category` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
`ordering` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
`is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='抽取任务分类';

CREATE TABLE `etl_extract_log` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`extract_id` varchar(36) NOT NULL DEFAULT '' COMMENT '抽取任务ID',
`breakpoint_type` varchar(30) NOT NULL DEFAULT 'full' COMMENT '断点类型（full：全量/breakpoint：有断点）',
`breakpoint` varchar(60) NOT NULL DEFAULT '' COMMENT '断点',
`breakpoint_step` varchar(60) NOT NULL DEFAULT '' COMMENT '断点递增',
`total` int(11) NOT NULL DEFAULT '0' COMMENT '总数据量',
`offset` int(11) NOT NULL DEFAULT '0' COMMENT '已处理数据量',
`status` varchar(30) NOT NULL DEFAULT 'create' COMMENT '状态（create：创建/running：运行中/finish：执行完成/error：出错）	',
`message` varchar(200) NOT NULL DEFAULT '' COMMENT '异常信息',
`trigger` varchar(30) NOT NULL DEFAULT 'system' COMMENT '触发方式：system：系统调度/manual：人工启动',
`complete_time` TIMESTAMP NULL DEFAULT '1970-01-02 00:00:00' COMMENT '完成时间',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='抽取数据';

CREATE TABLE `etl_extract_error` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`extract_log_id` varchar(36) NOT NULL DEFAULT '' COMMENT '抽取任务ID',
`extract_id` varchar(36) NOT NULL DEFAULT '' COMMENT '抽取任务ID',
`message` varchar(200) NOT NULL DEFAULT '' COMMENT '错误信息',
`trace` text NOT NULL COMMENT '错误跟踪信息',
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='抽取任务执行错误记录';


CREATE TABLE `etl_extract_snapshot` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`extract_log_id` varchar(36) NOT NULL DEFAULT '' COMMENT '抽取任务ID',
`extract_id` varchar(36) NOT NULL DEFAULT '' COMMENT '抽取任务ID',
`extract_data` text NOT NULL COMMENT '抽取任务数据 JSON',
`src_ds_id` varchar(36) NOT NULL DEFAULT '' COMMENT '来源数据源ID',
`src_ds_data` text NOT NULL COMMENT '来源数据源数据 JSON',
`dst_ds_id` varchar(36) NOT NULL DEFAULT '' COMMENT '目标数据源ID',
`dst_ds_data` text NOT NULL COMMENT '目标数据源数据 JSON',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='抽取任务执行时记录快照';

