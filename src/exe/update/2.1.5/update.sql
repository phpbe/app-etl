
ALTER TABLE `etl_flow_node_output_files`
ADD `file_exist` VARCHAR(30) NOT NULL DEFAULT 'override' COMMENT '同名文件操作(override - 覆盖 / append - 追加)' AFTER `content_code`;
