Settings/project/sync-project-data


INSERT INTO managers(name,email,mobile,org_id,status,created_at,created_by)
SELECT CONCAT(first_name,' ',last_name, ' - ', designation), email, mobile, org_id, 1, NOW(), 1 FROM `employees` WHERE designation LIKE '%Director General%' ORDER BY CONCAT(first_name,' ',last_name) ASC