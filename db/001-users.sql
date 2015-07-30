DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    name                TEXT NOT NULL,
    email_address       TEXT NOT NULL,
    password_crypt      TEXT NOT NULL,

    date_registered     DATETIME NOT NULL,

    activation_token    TEXT,
    date_verified       DATETIME,
    new_email_address   TEXT
);

