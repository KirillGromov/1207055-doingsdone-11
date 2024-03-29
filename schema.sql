DROP DATABASE IF EXISTS doings_done;

CREATE DATABASE doings_done
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;
USE doings_done;

CREATE TABLE user (
                      id INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
                      reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                      email CHAR(255) NOT NULL UNIQUE,
                      name CHAR(255) NOT NULL,
                      password VARCHAR(255)
);

CREATE TABLE project (
                         id INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
                         user INT,
                         FOREIGN KEY(user) REFERENCES user(id),
                         name CHAR(255),
                         alias CHAR(255)
);

CREATE TABLE task (
                      user_id INT,
                      project_id INT,
                      FOREIGN KEY(user_id) REFERENCES project(user),
                      FOREIGN KEY(project_id) REFERENCES project(id),
                      task INT AUTO_INCREMENT PRIMARY KEY,
                      date_create TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                      status TINYINT(1) DEFAULT '0',
                      title CHAR(255) NOT NULL,
                      file VARCHAR(255),
                      deadline DATE NULL
);
CREATE INDEX user ON user(id);
CREATE INDEX project ON project(id);
CREATE INDEX task ON task(user_id);

CREATE FULLTEXT INDEX task_search
    ON task(title);
