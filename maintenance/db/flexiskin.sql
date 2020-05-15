CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/flexiskin (
    fs_id           INT NOT NULL AUTO_INCREMENT,
    fs_name         VARCHAR(255),
    fs_active       boolean DEFAULT 0,
    fs_deleted      boolean DEFAULT 0,
    fs_config       LONGBLOB NULL,
    PRIMARY KEY( fs_id ),
    KEY ( fs_name )
) /*$wgDBTableOptions*/;
