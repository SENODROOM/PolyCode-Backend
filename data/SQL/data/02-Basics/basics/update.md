# SQL UPDATE Statement

## Basic UPDATE

### Update Single Column
```sql
-- Update single column for all rows
UPDATE employees SET salary = salary * 1.05;

-- Update single column with condition
UPDATE employees SET salary = 65000 WHERE employee_id = 101;
```

### Update Multiple Columns
```sql
-- Update multiple columns
UPDATE employees 
SET salary = 70000, 
    department_id = 60, 
    job_id = 'IT_PROG'
WHERE employee_id = 102;
```

## Conditional Updates

### WHERE Clause
```sql
-- Update with multiple conditions
UPDATE employees 
SET salary = salary * 1.10 
WHERE department_id = 50 AND job_id = 'SA_MAN';

-- Update using IN clause
UPDATE employees 
SET salary = salary * 1.05 
WHERE department_id IN (10, 20, 30);

-- Update using BETWEEN
UPDATE employees 
SET salary = salary * 1.03 
WHERE salary BETWEEN 40000 AND 60000;

-- Update using LIKE
UPDATE employees 
SET email = LOWER(email) 
WHERE email LIKE '%COMPANY.COM';
```

## Updates with Subqueries

### Using Subquery in WHERE
```sql
-- Update based on subquery result
UPDATE employees 
SET salary = salary * 1.10 
WHERE department_id = (
    SELECT department_id 
    FROM departments 
    WHERE department_name = 'IT'
);

-- Update using EXISTS
UPDATE employees e
SET salary = salary * 1.05 
WHERE EXISTS (
    SELECT 1 
    FROM job_history jh 
    WHERE jh.employee_id = e.employee_id 
    AND jh.department_id = 60
);
```

### Using Subquery in SET
```sql
-- Update column from subquery
UPDATE employees e
SET salary = (
    SELECT AVG(salary) 
    FROM employees 
    WHERE department_id = e.department_id
) * 1.20
WHERE job_id = 'MANAGER';

-- Update from another table
UPDATE employees e
SET department_name = (
    SELECT department_name 
    FROM departments d 
    WHERE d.department_id = e.department_id
);
```

## Database-Specific Updates

### MySQL
```sql
-- MySQL - UPDATE with JOIN
UPDATE employees e
JOIN departments d ON e.department_id = d.department_id
SET e.salary = e.salary * 1.05 
WHERE d.location_id = 1700;

-- MySQL - UPDATE with LIMIT
UPDATE employees 
SET salary = salary * 1.02 
WHERE department_id = 50 
LIMIT 10;
```

### PostgreSQL
```sql
-- PostgreSQL - UPDATE with JOIN
UPDATE employees e
SET salary = e.salary * 1.05 
FROM departments d 
WHERE e.department_id = d.department_id 
AND d.location_id = 1700;

-- PostgreSQL - UPDATE with RETURNING
UPDATE employees 
SET salary = salary * 1.05 
WHERE employee_id = 101 
RETURNING employee_id, salary;
```

### SQL Server
```sql
-- SQL Server - UPDATE with JOIN
UPDATE e
SET e.salary = e.salary * 1.05 
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
WHERE d.location_id = 1700;

-- SQL Server - UPDATE with OUTPUT
UPDATE employees 
SET salary = salary * 1.05 
OUTPUT INSERTED.employee_id, INSERTED.salary, DELETED.salary AS old_salary
WHERE department_id = 50;
```

## Conditional Logic

### CASE Statement
```sql
-- Update with CASE statement
UPDATE employees 
SET salary = CASE 
    WHEN salary < 40000 THEN salary * 1.15
    WHEN salary BETWEEN 40000 AND 60000 THEN salary * 1.10
    WHEN salary > 80000 THEN salary * 1.05
    ELSE salary * 1.07
END;

-- Update multiple columns with CASE
UPDATE employees 
SET bonus = CASE 
    WHEN department_id = 50 THEN 5000
    WHEN department_id = 60 THEN 3000
    ELSE 1000
END,
    commission_pct = CASE 
    WHEN job_id = 'SA_MAN' THEN 0.15
    WHEN job_id = 'SA_REP' THEN 0.10
    ELSE 0
END;
```

## Updates with Functions

### String Functions
```sql
-- Update with string functions
UPDATE employees 
SET email = LOWER(REPLACE(email, ' ', '.'));

-- Update with concatenation
UPDATE employees 
SET full_name = first_name || ' ' || last_name;

-- Update with substring
UPDATE employees 
SET phone = '(' || SUBSTRING(phone, 1, 3) ') ' || SUBSTRING(phone, 4, 3) || '-' || SUBSTRING(phone, 7, 4);
```

### Date Functions
```sql
-- Update with date functions
UPDATE employees 
SET hire_date = DATE_ADD(hire_date, INTERVAL 1 YEAR)
WHERE department_id = 10;

-- Update with date calculation
UPDATE employees 
SET years_of_service = TIMESTAMPDIFF(YEAR, hire_date, CURRENT_DATE);
```

## Bulk Updates

### Update Multiple Rows
```sql
-- Update with VALUES clause (PostgreSQL)
UPDATE employees 
SET salary = data.new_salary
FROM (VALUES 
    (101, 75000),
    (102, 68000),
    (103, 82000)
) AS data(employee_id, new_salary)
WHERE employees.employee_id = data.employee_id;

-- Update using temporary table
CREATE TEMPORARY TABLE temp_updates (
    employee_id INT,
    new_salary DECIMAL(10,2)
);

INSERT INTO temp_updates VALUES (101, 75000), (102, 68000), (103, 82000);

UPDATE employees e
SET salary = t.new_salary
FROM temp_updates t
WHERE e.employee_id = t.employee_id;

DROP TABLE temp_updates;
```

## Examples

### Employee Management
```sql
-- Give 5% raise to all employees
UPDATE employees SET salary = salary * 1.05;

-- Give 10% raise to IT department
UPDATE employees 
SET salary = salary * 1.10 
WHERE department_id = 60;

-- Update employee job and salary
UPDATE employees 
SET job_id = 'SR_ANALYST', 
    salary = 75000 
WHERE employee_id = 101;

-- Update email format for all employees
UPDATE employees 
SET email = LOWER(first_name || '.' || last_name || '@company.com')
WHERE email NOT LIKE '%@company.com';

-- Update manager references
UPDATE employees 
SET manager_id = 100 
WHERE manager_id IS NULL AND job_id = 'MANAGER';
```

### Data Cleanup
```sql
-- Standardize phone numbers
UPDATE employees 
SET phone = REGEXP_REPLACE(phone, '[^0-9]', '')
WHERE phone IS NOT NULL;

-- Remove leading/trailing spaces
UPDATE employees 
SET first_name = TRIM(first_name),
    last_name = TRIM(last_name),
    email = TRIM(email);

-- Convert email to lowercase
UPDATE employees 
SET email = LOWER(email);
```

## Best Practices
- Always use WHERE clause to avoid updating entire table accidentally
- Use transactions for critical updates
- Test updates with SELECT first
- Consider performance impact on large tables
- Use appropriate indexes for WHERE conditions
- Backup data before bulk updates
- Use parameterized queries in applications
- Consider using CASE for complex conditional logic
