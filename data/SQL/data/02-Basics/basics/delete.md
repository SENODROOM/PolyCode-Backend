# SQL DELETE Statement

## Basic DELETE

### Delete All Rows
```sql
-- Delete all rows from table
DELETE FROM employees;

-- Delete all rows (truncate is faster for large tables)
TRUNCATE TABLE employees;
```

### Delete with WHERE Clause
```sql
-- Delete specific row
DELETE FROM employees WHERE employee_id = 101;

-- Delete with condition
DELETE FROM employees WHERE department_id = 50;

-- Delete with multiple conditions
DELETE FROM employees 
WHERE department_id = 50 AND salary < 50000;
```

## Conditional Deletes

### Using Operators
```sql
-- Delete with IN clause
DELETE FROM employees 
WHERE department_id IN (10, 20, 30);

-- Delete with BETWEEN
DELETE FROM employees 
WHERE salary BETWEEN 30000 AND 40000;

-- Delete with LIKE
DELETE FROM employees 
WHERE email LIKE '%@oldcompany.com';

-- Delete with IS NULL
DELETE FROM employees 
WHERE manager_id IS NULL;

-- Delete with comparison operators
DELETE FROM employees 
WHERE hire_date < '2020-01-01';
```

## Deletes with Subqueries

### Using Subquery in WHERE
```sql
-- Delete based on subquery
DELETE FROM employees 
WHERE department_id = (
    SELECT department_id 
    FROM departments 
    WHERE department_name = 'Legacy Dept'
);

-- Delete using EXISTS
DELETE FROM employees e
WHERE EXISTS (
    SELECT 1 
    FROM job_history jh 
    WHERE jh.employee_id = e.employee_id 
    AND jh.end_date < '2020-01-01'
);

-- Delete using NOT EXISTS
DELETE FROM employees e
WHERE NOT EXISTS (
    SELECT 1 
    FROM job_history jh 
    WHERE jh.employee_id = e.employee_id
);
```

### Delete with JOIN
```sql
-- MySQL - DELETE with JOIN
DELETE e 
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
WHERE d.location_id = 1700;

-- PostgreSQL - DELETE with USING
DELETE FROM employees e
USING departments d
WHERE e.department_id = d.department_id 
AND d.location_id = 1700;

-- SQL Server - DELETE with JOIN
DELETE e
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
WHERE d.location_id = 1700;
```

## Database-Specific Deletes

### MySQL
```sql
-- MySQL - Delete with LIMIT
DELETE FROM employees 
WHERE department_id = 50 
LIMIT 10;

-- MySQL - Delete with ORDER BY and LIMIT
DELETE FROM employees 
WHERE department_id = 50 
ORDER BY hire_date ASC 
LIMIT 5;

-- MySQL - Multiple table DELETE
DELETE e, jh 
FROM employees e
LEFT JOIN job_history jh ON e.employee_id = jh.employee_id
WHERE e.department_id = 90;
```

### PostgreSQL
```sql
-- PostgreSQL - Delete with RETURNING
DELETE FROM employees 
WHERE employee_id = 101 
RETURNING employee_id, first_name, last_name;

-- PostgreSQL - Delete with USING
DELETE FROM employees e
USING departments d
WHERE e.department_id = d.department_id 
AND d.department_name = 'Old Department';
```

### SQL Server
```sql
-- SQL Server - Delete with OUTPUT
DELETE FROM employees 
OUTPUT DELETED.employee_id, DELETED.first_name, DELETED.last_name
WHERE department_id = 50;

-- SQL Server - Delete with TOP
DELETE TOP (10) FROM employees 
WHERE department_id = 50;
```

## Conditional Logic

### DELETE with CASE
```sql
-- Delete based on CASE condition
DELETE FROM employees 
WHERE CASE 
    WHEN hire_date < '2020-01-01' AND salary < 40000 THEN 1
    WHEN department_id = 90 THEN 1
    ELSE 0
END = 1;
```

## Bulk Deletes

### Large Dataset Deletes
```sql
-- Delete in batches (better for performance)
DELETE TOP (1000) FROM employees 
WHERE department_id = 50;

-- Delete with transaction and commit in batches
BEGIN TRANSACTION;
DELETE FROM employees WHERE employee_id IN (1, 2, 3, 4, 5);
COMMIT;

-- Delete using temporary table
CREATE TEMPORARY TABLE employees_to_delete (
    employee_id INT PRIMARY KEY
);

INSERT INTO employees_to_delete 
SELECT employee_id 
FROM employees 
WHERE department_id = 50;

DELETE FROM employees e
USING employees_to_delete d
WHERE e.employee_id = d.employee_id;

DROP TABLE employees_to_delete;
```

## Cascade Deletes

### Foreign Key Constraints
```sql
-- Delete with CASCADE (if foreign key allows)
DELETE FROM departments 
WHERE department_id = 50;

-- This will delete related employees if ON DELETE CASCADE is set

-- Delete with NO ACTION (default)
-- This will fail if there are related records
DELETE FROM departments 
WHERE department_id = 50;
-- Error: Cannot delete department with employees
```

## Examples

### Employee Management
```sql
-- Delete specific employee
DELETE FROM employees WHERE employee_id = 101;

-- Delete employees from dissolved department
DELETE FROM employees WHERE department_id = 90;

-- Delete inactive employees
DELETE FROM employees 
WHERE last_login_date < '2023-01-01' 
AND status = 'INACTIVE';

-- Delete duplicate records
DELETE FROM employees e1
WHERE employee_id > (
    SELECT MIN(employee_id) 
    FROM employees e2 
    WHERE e2.email = e1.email
);

-- Delete employees with no recent activity
DELETE FROM employees 
WHERE employee_id NOT IN (
    SELECT DISTINCT employee_id 
    FROM job_history 
    WHERE end_date > '2023-01-01'
);
```

### Data Cleanup
```sql
-- Delete test data
DELETE FROM employees 
WHERE email LIKE '%@test.com';

-- Delete old records
DELETE FROM employee_audit 
WHERE audit_date < DATE_SUB(CURRENT_DATE, INTERVAL 2 YEAR);

-- Delete temporary records
DELETE FROM temp_employees 
WHERE created_at < DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY);
```

## Safety Considerations

### Backup Before Delete
```sql
-- Create backup before deletion
CREATE TABLE employees_backup_20240120 AS 
SELECT * FROM employees;

-- Verify backup
SELECT COUNT(*) FROM employees_backup_20240120;

-- Then perform deletion
DELETE FROM employees WHERE department_id = 50;
```

### Transaction Safety
```sql
-- Use transaction for safety
BEGIN TRANSACTION;

-- Check what will be deleted
SELECT COUNT(*) FROM employees WHERE department_id = 50;

-- Perform deletion
DELETE FROM employees WHERE department_id = 50;

-- Verify results
SELECT COUNT(*) FROM employees WHERE department_id = 50;

-- Commit or rollback
COMMIT;
-- or ROLLBACK;
```

## Best Practices
- Always use WHERE clause unless you want to delete all rows
- Test DELETE statements with SELECT first
- Use transactions for critical deletions
- Consider using TRUNCATE for deleting all rows
- Be aware of foreign key constraints
- Backup important data before bulk deletes
- Use appropriate indexes for WHERE conditions
- Consider performance impact on large tables
- Use LIMIT for large deletions in batches
- Log important deletions for audit purposes
