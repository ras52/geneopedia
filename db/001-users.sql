DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    name                TEXT NOT NULL,
    email_address       TEXT NOT NULL,
    password_crypt      TEXT NOT NULL,

    date_registered     DATETIME NOT NULL,
    date_verified       DATETIME,
    date_approved       DATETIME,

    approved_by         INTEGER UNSIGNED,

    activation_token    TEXT,
    new_email_address   TEXT,

    FOREIGN KEY (approved_by) REFERENCES users(id)

) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

