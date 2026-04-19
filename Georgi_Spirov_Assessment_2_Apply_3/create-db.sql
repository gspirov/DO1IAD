DROP DATABASE IF EXISTS portfolio;
CREATE DATABASE portfolio
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

DROP DATABASE IF EXISTS portfolio_test;
CREATE DATABASE portfolio_test
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

USE portfolio;

DROP USER IF EXISTS 'web'@'localhost';
DROP USER IF EXISTS 'admin'@'localhost';

CREATE USER 'web'@'localhost' IDENTIFIED BY '2Yl6OKFNR4e8fLja';
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'GPKV23Wbwb81oqzi';

GRANT SELECT, INSERT, UPDATE, DELETE
ON portfolio.*
TO 'web'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE,
CREATE, ALTER, DROP, REFERENCES
ON portfolio.*
TO 'admin'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE
ON portfolio_test.*
TO 'web'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE,
CREATE, ALTER, DROP, REFERENCES
ON portfolio_test.*
TO 'admin'@'localhost';

FLUSH PRIVILEGES;
