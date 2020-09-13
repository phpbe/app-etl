SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `etl_ds` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `type` varchar(60) NOT NULL DEFAULT '' COMMENT '驱动类型：mysql/oracle/mssql',
  `db_host` varchar(60) NOT NULL DEFAULT '' COMMENT '主机名',
  `db_port` int(11) NOT NULL DEFAULT '0' COMMENT '端口号',
  `db_user` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `db_pass` varchar(60) NOT NULL DEFAULT '' COMMENT '密码',
  `db_name` varchar(60) NOT NULL DEFAULT '' COMMENT '库名',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `is_enable` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据源';

CREATE TABLE `etl_extract` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `src_ds_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源数据源ID',
  `dst_ds_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标数据源ID',
  `src_table` varchar(60) NOT NULL DEFAULT '' COMMENT '来源数据源表名',
  `dst_table` varchar(60) NOT NULL DEFAULT '' COMMENT '目标数据源表名',
  `field_mapping_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '字段映射类型（0:不需要映射/1：字段映射/2：代码处理）',
  `field_mapping` text NOT NULL DEFAULT '' COMMENT '字段映射',
  `field_mapping_code` text NOT NULL DEFAULT '' COMMENT '代码映射',
  `breakpoint_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '断点类型（0：全量/1：有断点）',
  `breakpoint_field` varchar(60) NOT NULL DEFAULT '' COMMENT '断点字段',
  `breakpoint` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '断点',
  `breakpoint_step` varchar(60) NOT NULL DEFAULT '' COMMENT '断点递增量(1_HOUR:一小时/1_DAY:一天/1_MONTH:一个月)',
  `schedule` varchar(60) NOT NULL DEFAULT '' COMMENT '执行计划',
  `is_enable` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽取任务';


CREATE TABLE `etl_extract_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_enable` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽取任务分类';

CREATE TABLE `etl_extract_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `extract_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽取任务ID',
  `breakpoint_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '断点类型（0：全量/1：有断点）',
  `breakpoint` varchar(60) NOT NULL DEFAULT '' COMMENT '断点',
  `breakpoint_step` varchar(60) NOT NULL DEFAULT '' COMMENT '断点递增',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '总数据量',
  `offset` int(11) NOT NULL DEFAULT '0' COMMENT '已处理数据量',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态（0：创建/1：运行中/2：执行完成/-1：出错）	',
  `message` varchar(200) NOT NULL DEFAULT '' COMMENT '异常信息',
  `trigger` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '触发方式：0：系统调度/1：人工启动',
  `complete_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '完成时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽取数据';

CREATE TABLE `etl_extract_snapshot` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `extract_log_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽取任务ID',
  `extract_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽取任务ID',
  `extract_data` text NOT NULL DEFAULT '' COMMENT '抽取任务数据 JSON',
  `src_ds_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源数据源ID',
  `src_ds_data` text NOT NULL DEFAULT '' COMMENT '来源数据源数据 JSON',
  `dst_ds_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标数据源ID',
  `dst_ds_data` text NOT NULL DEFAULT '' COMMENT '目标数据源数据 JSON',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽取任务执行时记录快照';

CREATE TABLE `etl_extract_exception` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `extract_log_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽取任务ID',
  `extract_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽取任务ID',
  `message` varchar(200) NOT NULL DEFAULT '' COMMENT '异常信息',
  `trace` text NOT NULL DEFAULT '' COMMENT '异常跟踪信息',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽取任务执行异常记录';
