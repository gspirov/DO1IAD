-- START Management Operational Statements --
-- Find all eligible employees
SELECT *
FROM get_eligible_allocated_employees;

-- Find all eligible employees with skills that are required for the project 'Enterprise CRM Platform'
SELECT *
FROM get_eligible_allocated_employees
WHERE project_title = 'Enterprise CRM Platform';

-- Find all eligible employees with skills that are required for the project 'E-commerce Microservices Migration'
SELECT *
FROM get_eligible_allocated_employees
WHERE project_title = 'E-commerce Microservices Migration';

-- Find all eligible employees with skills that are required for the project 'Mobile Banking Application'
SELECT *
FROM get_eligible_allocated_employees
WHERE project_title = 'Mobile Banking Application';

-- Get the history of all employees
SELECT *
FROM get_employees_project_history;

-- Get the history of a specific employee
SELECT *
FROM get_employees_project_history
WHERE employee_email = 'michael.turner@example.com';

-- Get the number of projects per client
SELECT c.organization_name,
       CONCAT_WS(c.contact_first_name, ' ', c.contact_last_name) as client_names,
       COUNT(p.id) as project_count
FROM project p
JOIN `client` c ON p.client_id = c.id
GROUP BY c.id;

-- Accumulate the total budget of completed projects per client
SELECT c.organization_name,
       CONCAT(c.contact_first_name, ' ', c.contact_last_name) as client_names,
       SUM(budget) as total_budget
FROM project
JOIN `client` c ON project.client_id = c.id
WHERE `phase` = 'Complete'
GROUP BY c.id;

-- Get the number of projects per client that have more than one project
SELECT c.organization_name,
       CONCAT(c.contact_first_name, ' ', c.contact_last_name) as client_names,
       COUNT(p.id) as project_count
FROM `client` c
JOIN project p ON c.id = p.client_id
GROUP BY c.id
HAVING COUNT(p.id) > 1;

-- Get the list of projects that require skills that are not yet allocated to employees
SELECT c.organization_name,
       CONCAT(c.contact_first_name, ' ', c.contact_last_name) as client_names,
       p.title as project_title,
       s.name as skill_name,
       st.name as skill_type,
       req_sl.name as required_skill_level
FROM project p
JOIN `client` c ON p.client_id = c.id
JOIN project_skill_requirement psr ON p.id = psr.project_id
JOIN skill s ON psr.skill_id = s.id
JOIN skill_type st ON s.type_id = st.id
JOIN skill_level req_sl ON psr.level_id = req_sl.id
WHERE p.phase <> 'Complete'
AND NOT EXISTS (
    SELECT 1
    FROM project_employee_skill pes
    JOIN skill_level pes_sl ON pes_sl.id = pes.level_id
    WHERE pes.project_id = p.id
    AND pes.skill_id = psr.skill_id
    AND pes_sl.level >= req_sl.level
);
-- END Management Operational Statements --