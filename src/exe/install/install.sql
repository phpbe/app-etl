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
`update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='数据源';


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
`breakpoint_step` varchar(60) NOT NULL DEFAULT '' COMMENT '断点递增量(1_HOUR:一小时/1_DAY:一天/1_MONTH:一个月)',
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

