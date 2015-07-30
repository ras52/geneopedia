DROP TABLE IF EXISTS files;

CREATE TABLE files (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    user_id             INTEGER UNSIGNED NOT NULL,
    date_uploaded       DATETIME NOT NULL,
    mime_type           TEXT NOT NULL,
    extension           VARCHAR(10),
    sha1                VARCHAR(40) NOT NULL,
    length              INTEGER NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)

) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
