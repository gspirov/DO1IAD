CREATE DATABASE IF NOT EXISTS `project_management`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

CREATE TABLE address (
    id INT AUTO_INCREMENT PRIMARY KEY,
    street VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country_code CHAR(2) NOT NULL
) ENGINE=InnoDb;

CREATE TABLE `client` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_name VARCHAR(155) NOT NULL,
    contact_first_name VARCHAR(100) NOT NULL,
    contact_last_name VARCHAR(100) NOT NULL,
    email VARCHAR(155) NOT NULL,
    preferred_contact_method ENUM('Post', 'Email') NOT NULL,
    address_id INT NOT NULL,
    FOREIGN KEY (address_id) REFERENCES address(id),
    CONSTRAINT uq_client_email UNIQUE (email),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDb;

CREATE TABLE employee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(155) NOT NULL,
    phone VARCHAR(35) NOT NULL,
    work_address_id INT NOT NULL,
    home_address_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (work_address_id) REFERENCES address(id),
    FOREIGN KEY (home_address_id) REFERENCES address(id),
    CONSTRAINT uq_employee_email UNIQUE (email),
    CONSTRAINT uq_employee_phone UNIQUE (phone)
) ENGINE=InnoDb;

CREATE TABLE skill_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(155) NOT NULL,
    CONSTRAINT uq_skill_type_name UNIQUE (`name`)
) ENGINE=InnoDb;

CREATE TABLE skill (
    id INT AUTO_INCREMENT PRIMARY KEY,
   `name` VARCHAR(155) NOT NULL,
   `type_id` INT NOT NULL,
    FOREIGN KEY (`type_id`) REFERENCES skill_type(id),
    CONSTRAINT uq_skill_name UNIQUE (`name`)
) ENGINE=InnoDb;

CREATE TABLE skill_level (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `level` TINYINT NOT NULL,
    `name` VARCHAR(155) NOT NULL,
    CONSTRAINT uq_skill_level UNIQUE (`level`),
    CONSTRAINT uq_skill_level_name UNIQUE (`name`)
) ENGINE=InnoDb;

CREATE TABLE employee_skill (
    employee_id INT NOT NULL,
    skill_id INT NOT NULL,
    level_id INT NOT NULL,
    FOREIGN KEY (level_id) REFERENCES skill_level(id),
    FOREIGN KEY (employee_id) REFERENCES employee(id),
    FOREIGN KEY (skill_id) REFERENCES skill(id),
    PRIMARY KEY (employee_id, skill_id)
) ENGINE=InnoDb;

CREATE TABLE project (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(155) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    budget DECIMAL(12,2) NOT NULL,
    short_description TEXT NOT NULL,
    `phase` ENUM('Design', 'Development', 'Testing', 'Deployment', 'Complete') NOT NULL,
    client_id INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES `client`(id) ON DELETE CASCADE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP,
    CHECK ( end_date > start_date )
) ENGINE=InnoDb;

CREATE TABLE project_skill_requirement (
    project_id INT NOT NULL,
    skill_id INT NOT NULL,
    level_id INT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skill(id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES skill_level(id) ON DELETE CASCADE,
    PRIMARY KEY (project_id, skill_id, level_id)
) ENGINE=InnoDb;

CREATE TABLE project_employee (
    project_id INT NOT NULL,
    employee_id INT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employee(id) ON DELETE CASCADE,
    CONSTRAINT uq_project_employee UNIQUE (employee_id),
    PRIMARY KEY (project_id, employee_id)
) ENGINE=InnoDb;

CREATE TABLE project_employee_skill (
    project_id INT NOT NULL,
    employee_id INT NOT NULL,
    skill_id INT NOT NULL,
    level_id INT NOT NULL,
    FOREIGN KEY (project_id, employee_id) REFERENCES project_employee(project_id, employee_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skill(id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES skill_level(id) ON DELETE CASCADE,
    PRIMARY KEY (project_id, employee_id, skill_id)
) ENGINE=InnoDb;

CREATE OR REPLACE VIEW get_eligible_allocated_employees AS
SELECT p.id AS project_id,
       p.title AS project_title,
       CONCAT_WS(' ', e.first_name, e.last_name) AS employee_full_name,
       e.email AS employee_email,
       s.name AS skill,
       st.name AS skill_type,
       req_sl.name AS required_skill_level,
       emp_sl.name AS employee_skill_level
FROM employee e
JOIN employee_skill es ON es.employee_id = e.id
JOIN skill s ON s.id = es.skill_id
JOIN skill_level emp_sl ON emp_sl.id = es.level_id
JOIN project_skill_requirement psr ON psr.skill_id = es.skill_id
JOIN skill_level req_sl ON req_sl.id = psr.level_id
JOIN project p ON p.id = psr.project_id
JOIN skill_type st ON st.id = s.type_id
WHERE emp_sl.level >= req_sl.level
AND NOT EXISTS (
    SELECT 1
    FROM project_employee_skill pes
    WHERE pes.employee_id = e.id
    AND pes.project_id = p.id
    AND pes.skill_id = psr.skill_id
)
AND NOT EXISTS (
    SELECT 1
    FROM project_employee pe
    JOIN project p2 ON p2.id = pe.project_id
    WHERE pe.employee_id = e.id
    AND p2.id <> p.id
    AND p2.phase != 'Complete'
);

DROP PROCEDURE IF EXISTS assign_employee_to_project;

DELIMITER $$

CREATE PROCEDURE assign_employee_to_project(
    IN p_employee_email VARCHAR(155),
    IN p_project_id INT,
    IN p_skill_id INT,
    IN p_skill_level_id INT
)
BEGIN
    DECLARE v_employee_id INT;
    DECLARE v_project_title VARCHAR(155);
    DECLARE v_skill_name VARCHAR(155);
    DECLARE v_skill_level_name VARCHAR(155);
    DECLARE v_skill_level INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT id
    INTO v_employee_id
    FROM employee
    WHERE email = p_employee_email;

    IF v_employee_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Employee not found (no such email).';
    END IF;

    SELECT sl.name, sl.level
    INTO v_skill_level_name, v_skill_level
    FROM skill_level sl
    WHERE sl.id = p_skill_level_id;

    IF v_skill_level IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Skill level not found.';
    END IF;

    IF EXISTS(
        SELECT 1
        FROM project_employee pe
        JOIN project p on pe.project_id = p.id
        WHERE p.id <> p_project_id
        AND pe.employee_id = v_employee_id
        AND p.phase <> 'Complete'
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Employee is already assigned to other project.';
    END IF;

    IF NOT EXISTS(
        SELECT 1
        FROM project_skill_requirement psr
        JOIN skill_level req_level ON psr.level_id = req_level.id
        WHERE psr.project_id = p_project_id
        AND psr.skill_id = p_skill_id
        AND psr.level_id <= p_skill_level_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Skill level not found for this project as requirement.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM employee_skill es
        JOIN skill_level emp_level ON emp_level.id = es.level_id
        WHERE es.employee_id = v_employee_id
        AND es.skill_id = p_skill_id
        AND emp_level.level >= v_skill_level
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Employee does not meet the skill requirements for this project.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM project_employee pe
        JOIN project_employee_skill pes ON pes.project_id = pe.project_id
        WHERE pe.employee_id = v_employee_id
        AND pes.skill_id = p_skill_id
        AND pes.project_id = p_project_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Employee is already assigned to this project for that skill.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM project_employee pe
        WHERE pe.employee_id = v_employee_id
    ) THEN
        INSERT INTO project_employee(project_id, employee_id)
        VALUES (p_project_id, v_employee_id);
    END IF;

    INSERT INTO project_employee_skill(project_id, employee_id, skill_id, level_id)
    VALUES (p_project_id, v_employee_id, p_skill_id, p_skill_level_id);
    COMMIT;

    SELECT title INTO v_project_title FROM project p WHERE p.id = p_project_id;
    SELECT `name` INTO v_skill_name FROM skill s WHERE s.id = p_skill_id;

    SELECT 'OK' AS status,
           CONCAT(
               'Employee ', p_employee_email,
               ' assigned to project ', v_project_title,
               ' as ', v_skill_level_name, ' ', v_skill_name
           ) AS message;
END;

CREATE OR REPLACE VIEW get_employees_project_history AS
SELECT p.title as project_title,
       CONCAT_WS(' ', e.first_name, e.last_name) AS employee_full_name,
       e.email AS employee_email,
       p.phase AS project_phase,
       p.start_date AS project_start_date,
       p.end_date AS project_end_date,
       sl.name as skill_level,
       s.name as skill
FROM project p
JOIN project_employee pe ON pe.project_id = p.id
JOIN project_employee_skill pes ON pes.project_id = p.id AND pes.employee_id = pe.employee_id
JOIN skill s ON s.id = pes.skill_id
JOIN skill_level sl ON sl.id = pes.level_id
JOIN employee e ON e.id = pe.employee_id;

INSERT INTO skill_type (`name`)
VALUES
    ('Backend'),
    ('Frontend'),
    ('Testing'),
    ('Design'),
    ('Project Management');

SELECT id INTO @backendSkillTypeId FROM skill_type WHERE `name` = 'Backend';
SELECT id INTO @frontendSkillTypeId FROM skill_type WHERE `name` = 'Frontend';
SELECT id INTO @testingSkillTypeId FROM skill_type WHERE `name` = 'Testing';
SELECT id INTO @designSkillTypeId FROM skill_type WHERE `name` = 'Design';
SELECT id INTO @projectManagerSkillTypeId FROM skill_type WHERE `name` = 'Project Management';

INSERT INTO skill_level (`level`, `name`)
VALUES
    (1, 'Beginner'),
    (2, 'Intermediate'),
    (3, 'Advanced'),
    (4, 'Expert');

SELECT id INTO @beginnerLevelId FROM skill_level WHERE `level` = 1;
SELECT id INTO @intermediateLevelId FROM skill_level WHERE `level` = 2;
SELECT id INTO @advancedLevelId FROM skill_level WHERE `level` = 3;
SELECT id INTO @expertLevelId FROM skill_level WHERE `level` = 4;

INSERT INTO skill (`name`, type_id)
VALUES
-- backend
('PHP', @backendSkillTypeId),
('Java', @backendSkillTypeId),
('Python', @backendSkillTypeId),
('MySQL', @backendSkillTypeId),
-- frontend
('Javascript', @frontendSkillTypeId),
('HTML', @frontendSkillTypeId),
('CSS', @frontendSkillTypeId),
-- qa
('JUnit', @testingSkillTypeId),
('Selenium', @testingSkillTypeId),
-- design
('Figma', @designSkillTypeId),
('Adobe XD', @designSkillTypeId),
('Sketch', @designSkillTypeId),
-- project management
('Scrum', @projectManagerSkillTypeId),
('Jira', @projectManagerSkillTypeId),
('Confluence', @projectManagerSkillTypeId),
('Kanban', @projectManagerSkillTypeId);

SELECT id INTO @phpSkillId FROM skill WHERE `name` = 'PHP';
SELECT id INTO @javaSkillId FROM skill WHERE `name` = 'Java';
SELECT id INTO @pythonSkillId FROM skill WHERE `name` = 'Python';
SELECT id INTO @mysqlSkillId FROM skill WHERE `name` = 'MySQL';

SELECT id INTO @javascriptSkillId FROM skill WHERE `name` = 'Javascript';
SELECT id INTO @htmlSkillId FROM skill WHERE `name` = 'HTML';
SELECT id INTO @cssSkillId FROM skill WHERE `name` = 'CSS';

SELECT id INTO @junitSkillId FROM skill WHERE `name` = 'JUnit';
SELECT id INTO @seleniumSkillId FROM skill WHERE `name` = 'Selenium';

SELECT id INTO @figmaSkillId FROM skill WHERE `name` = 'Figma';
SELECT id INTO @adobeXdSkillId FROM skill WHERE `name` = 'Adobe XD';
SELECT id INTO @sketchSkillId FROM skill WHERE `name` = 'Sketch';

SELECT id INTO @scrumSkillId FROM skill WHERE `name` = 'Scrum';
SELECT id INTO @jiraSkillId FROM skill WHERE `name` = 'Jira';
SELECT id INTO @confluenceSkillId FROM skill WHERE `name` = 'Confluence';

INSERT INTO address (street, city, postal_code, country_code)
VALUES
-- start employees addresses
('221B Baker Street', 'London', 'NW1 6XE', 'GB'),
('42 Kensington High Street', 'London', 'W8 5SA', 'GB'),
('25 King William Street', 'London', 'EC4R 9AR', 'GB'),
('8 Queen Square', 'London', 'WC1N 3AT', 'GB'),
('14 Liverpool Street', 'London', 'EC2M 7PD', 'GB'),
-- all employees share same work address
('33 Victoria Street', 'London', 'SW1H 0HW', 'GB'),
-- end employees addresses
-- start client addresses
('7 Bridge Street', 'Manchester', 'M3 3BT', 'GB'),
('18 Princes Avenue', 'Liverpool', 'L8 1RB', 'GB'),
('27 Victoria Road', 'Nottingham', 'NG1 2GB', 'GB');
-- end client addresses

SELECT id INTO @workAddressId FROM address WHERE street = '33 Victoria Street';

SELECT id INTO @firstEmployeeHomeAddressId FROM address WHERE street = '221B Baker Street';
SELECT id INTO @secondEmployeeHomeAddressId FROM address WHERE street = '42 Kensington High Street';
SELECT id INTO @thirdEmployeeHomeAddressId FROM address WHERE street = '25 King William Street';
SELECT id INTO @fourthEmployeeHomeAddressId FROM address WHERE street = '8 Queen Square';
SELECT id INTO @fifthEmployeeHomeAddressId FROM address WHERE street = '14 Liverpool Street';

SELECT id INTO @firstClientAddressId FROM address WHERE street = '7 Bridge Street';
SELECT id INTO @secondClientAddressId FROM address WHERE street = '18 Princes Avenue';
SELECT id INTO @thirdClientAddressId FROM address WHERE street = '27 Victoria Road';

INSERT INTO employee (first_name, last_name, email, phone, work_address_id, home_address_id)
VALUES
(
    'Michael',
    'Turner',
    'michael.turner@example.com',
    '+447700900123',
    @workAddressId,
    @firstEmployeeHomeAddressId
),
(
    'Sophie',
    'Bennett',
    'sophie.bennett@example.com',
    '+447700900124',
    @workAddressId,
    @secondEmployeeHomeAddressId
),
(
    'Daniel',
    'Hughes',
    'daniel.hughes@example.com',
    '+447700900125',
    @workAddressId,
    @thirdEmployeeHomeAddressId
),
(
    'Emma',
    'Collins',
    'emma.collins@example.com',
    '+447700900126',
    @workAddressId,
    @fourthEmployeeHomeAddressId
),
(
    'Oliver',
    'Ward',
    'oliver.ward@example.com',
    '+447700900127',
    @workAddressId,
    @fifthEmployeeHomeAddressId
);

SELECT id INTO @firstEmployeeId FROM employee WHERE email = 'michael.turner@example.com';
SELECT id INTO @secondEmployeeId FROM employee WHERE email = 'sophie.bennett@example.com';
SELECT id INTO @thirdEmployeeId FROM employee WHERE email = 'daniel.hughes@example.com';
SELECT id INTO @fourthEmployeeId FROM employee WHERE email = 'emma.collins@example.com';

INSERT INTO employee_skill (employee_id, skill_id, level_id)
VALUES
-- backend department
(@firstEmployeeId, @phpSkillId, @expertLevelId),
(@firstEmployeeId, @javaSkillId, @advancedLevelId),
(@firstEmployeeId, @pythonSkillId, @intermediateLevelId),
(@firstEmployeeId, @mysqlSkillId, @expertLevelId),
-- frontend department
(@firstEmployeeId, @htmlSkillId, @expertLevelId),
(@firstEmployeeId, @cssSkillId, @expertLevelId),
(@firstEmployeeId, @javascriptSkillId, @intermediateLevelId),
-- testing department
(@thirdEmployeeId, @junitSkillId, @intermediateLevelId),
(@thirdEmployeeId, @seleniumSkillId, @intermediateLevelId),
-- design department
(@secondEmployeeId, @figmaSkillId, @expertLevelId),
(@secondEmployeeId, @adobeXdSkillId, @expertLevelId),
(@secondEmployeeId, @sketchSkillId, @expertLevelId),
-- project management department
(@fourthEmployeeId, @scrumSkillId, @advancedLevelId),
(@fourthEmployeeId, @jiraSkillId, @intermediateLevelId),
(@fourthEmployeeId, @confluenceSkillId, @expertLevelId);

INSERT INTO `client` (organization_name, contact_first_name, contact_last_name, email, preferred_contact_method, address_id)
VALUES
(
    'Northbridge Developments Ltd.',
    'Oliver',
    'Mitchell',
    'oliver.mitchell@northbridge.co.uk',
    'Email',
    @firstClientAddressId
),
(
    'Greenfield Property Group',
    'Amelia',
    'Roberts',
    'amelia.roberts@greenfieldgroup.co.uk',
    'Post',
    @secondClientAddressId
),
(
    'Union Capital Bank',
    'Laura',
    'Mitchell',
    'laura.mitchell@unioncapitalbank.co.uk',
    'Email',
    @thirdClientAddressId
);

SELECT id INTO @firstClientId FROM `client` WHERE email = 'oliver.mitchell@northbridge.co.uk';
SELECT id INTO @secondClientId FROM `client` WHERE email = 'amelia.roberts@greenfieldgroup.co.uk';
SELECT id INTO @thirdClientId FROM `client` WHERE email = 'laura.mitchell@unioncapitalbank.co.uk';

INSERT INTO project (title, start_date, end_date, budget, short_description, `phase`, client_id)
VALUES
(
    'Enterprise CRM Platform',
    '2026-03-15',
    '2026-11-30',
    250000.00,
    'Design and development of a custom enterprise CRM system with REST API and web dashboard.',
    'Design',
    @firstClientId
),
(
    'E-commerce Microservices Migration',
    '2026-04-01',
    '2026-09-15',
    180000.00,
    'Refactoring a monolithic e-commerce system into scalable microservices architecture.',
    'Design',
    @secondClientId
),
(
    'Mobile Banking Application',
    '2026-05-10',
    '2027-01-20',
    420000.00,
    'Full-cycle development of a secure mobile banking application for iOS and Android.',
    'Design',
    @thirdClientId
);

SELECT id INTO @firstProjectId FROM `project` WHERE title = 'Enterprise CRM Platform' AND client_id = @firstClientId;
SELECT id INTO @secondProjectId FROM `project` WHERE title = 'E-commerce Microservices Migration' AND client_id = @secondClientId;
SELECT id INTO @thirdProjectId FROM `project` WHERE title = 'Mobile Banking Application' AND client_id = @thirdClientId;

INSERT INTO project_skill_requirement (project_id, skill_id, level_id)
VALUES
-- start first project skills
(@firstProjectId, @figmaSkillId, @expertLevelId),
(@firstProjectId, @phpSkillId, @expertLevelId),
(@firstProjectId, @mysqlSkillId, @intermediateLevelId),
(@firstProjectId, @htmlSkillId, @expertLevelId),
(@firstProjectId, @cssSkillId, @beginnerLevelId),
(@firstProjectId, @javascriptSkillId, @intermediateLevelId),
(@firstProjectId, @jiraSkillId, @intermediateLevelId),
(@firstProjectId, @confluenceSkillId, @intermediateLevelId),
-- end first project skills
-- start second project skills
(@secondProjectId, @phpSkillId, @expertLevelId),
(@secondProjectId, @mysqlSkillId, @expertLevelId),
(@secondProjectId, @jiraSkillId, @intermediateLevelId),
(@secondProjectId, @confluenceSkillId, @intermediateLevelId),
# -- end second project skills
# -- start third project skills
(@thirdProjectId, @adobeXdSkillId, @intermediateLevelId),
(@thirdProjectId, @figmaSkillId, @expertLevelId),
(@thirdProjectId, @javaSkillId, @expertLevelId),
(@thirdProjectId, @mysqlSkillId, @expertLevelId),
(@thirdProjectId, @jiraSkillId, @intermediateLevelId);
-- end third project skills

-- Assign employees to the projects
CALL assign_employee_to_project(
    'michael.turner@example.com',
    @firstProjectId,
    @phpSkillId,
    @expertLevelId
 );

CALL assign_employee_to_project(
    'michael.turner@example.com',
    @firstProjectId,
    @mysqlSkillId,
    @expertLevelId
 );