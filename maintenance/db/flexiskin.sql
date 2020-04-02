CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/flexiskin (
    fs_id           INT   NOT NULL PRIMARY KEY AUTO_INCREMENT,
    fs_name         varchar(255)   PRIMARY KEY,
    fs_active       boolean           default 0,
    fs_deleted      boolean           default 0,
    fs_config       LONGBLOB       default NULL
)/*$wgDBTableOptions*/;
