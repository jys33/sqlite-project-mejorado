--https://dev.mysql.com/doc/refman/8.0/en/integer-types.html
CREATE DATABASE app CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE `app`.`user` (
  `user_id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `last_name` VARCHAR (50) NOT NULL,
  `first_name` VARCHAR (50) NOT NULL,
  `user_email` VARCHAR (50) NOT NULL,
  `user_name` VARCHAR (50) NOT NULL,
  `password` VARCHAR (255) NOT NULL,
  `activation` CHAR (32) NOT NULL,
  `created_on` DATETIME NOT NULL,
  `last_modified_on` DATETIME NOT NULL,
  `disabled` TINYINT (1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX (`user_name`, `user_email`)
) ENGINE = INNODB CHARSET = utf8 COLLATE = utf8_general_ci ;

-- Tabla de ejemplo para pruebas
CREATE TABLE employee (
  employee_id INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre CHAR(4) NOT NULL,
  apellido VARCHAR (4) NOT NULL,
  salary DECIMAL (8, 2) UNSIGNED NOT NULL,-- MAX VALUE 999999.99
  PRIMARY KEY (employee_id)
) ENGINE = INNODB CHARSET = utf8 COLLATE = utf8_general_ci ;

INSERT INTO employee (salary) 
VALUES
  ('80336.97') ;

INSERT INTO employee (nombre, apellido, salary) 
VALUES
  ('ab ', 'cd  ', '910000.00') ;
--Si un valor dado se almacena en las columnas CHAR (4) Y VARCHAR (4), los valores recuperados de las columnas
--NO siempre son los mismos porque los espacios finales se eliminan de las columnas CHAR al recuperarlos.
--El siguiente ejemplo ilustra esta diferencia:
SELECT 
  CONCAT('(', nombre, ')') nombre_char,
  CONCAT('(', apellido, ')') apellido_varchar 
FROM
  employee ;

nombre_char	apellido_varchar
(ab)	(cd  )


--https://dev.mysql.com/doc/refman/8.0/en/encryption-functions.html#function_sha2
INSERT INTO USER (
  last_name,
  first_name,
  user_email,
  user_name,
  PASSWORD,
  activation,
  created_on,
  last_modified_on
) 
VALUES
  (
    'Khan',
    'Louis',
    'louik@gmail.com',
    'magic',
    sha2 ('ABC 123456/*', 512),
    'activated',
    '2019-1-2 17:32:39',
    '2019-1-2 17:32:39'
  ) ;

INSERT INTO USER (
  last_name,
  first_name,
  user_email,
  user_name,
  PASSWORD,
  activation,
  created_on,
  last_modified_on
) 
VALUES
  (
    'Doe',
    'Jeff',
    'mcjeff@yahoo.com.us',
    'jeffy33',
    sha2 ('ABC 123456/*', 512),
    'activated',
    NOW(),
    NOW()
  ) ;

SELECT 
  CONCAT(
    SUBSTR(last_name, 1, 1),
    '. ',
    first_name
  ) AS USER 
FROM
  USER ;

SELECT 
  CONCAT(
    SUBSTR(last_name, 1, 1),
    '. ',
    first_name
  ) AS USER,
  DATE_FORMAT(created_on, "%d/%m/%Y") AS fecha_de_registro,
  DATE_FORMAT(
    created_on,
    "%a, %d de %M de %Y"
  ) AS fecha_larga_de_registro 
FROM
  USER ;