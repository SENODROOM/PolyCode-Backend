# SQL INSERT Statement

## Basic INSERT

### Insert Single Row
```sql
-- Insert with column names
INSERT INTO employees (employee_id, first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES (101, 'John', 'Doe', 'john.doe@company.com', '2024-01-15', 'IT_PROG', 75000, 60);

-- Insert with all columns (order matters)
INSERT INTO employees 
VALUES (102, 'Jane', 'Smith', 'jane.smith@company.com', '2024-01-16', 'HR_REP', 65000, 40);
```

### Insert Multiple Rows
```sql
-- Insert multiple rows (MySQL, PostgreSQL, SQL Server)
INSERT INTO employees (employee_id, first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES 
    (103, 'Bob', 'Johnson', 'bob.johnson@company.com', '2024-01-17', 'SA_MAN', 85000, 50),
    (104, 'Alice', 'Brown', 'alice.brown@company.com', '2024-01-18', 'MK_REP', 55000, 30),
    (105, 'Charlie', 'Davis', 'charlie.davis@company.com', '2024-01-19', 'PU_CLERK', 45000, 20);
```

## Insert with Subquery

### Insert from Another Table
```sql
-- Insert using SELECT statement
INSERT INTO employee_archive (employee_id, first_name, last_name, email, hire_date)
SELECT employee_id, first_name, last_name, email, hire_date
FROM employees 
WHERE department_id = 10;

-- Insert with calculated values
INSERT INTO salary_history (employee_id, old_salary, new_salary, change_date)
SELECT employee_id, salary * 0.9, salary, CURRENT_DATE
FROM employees 
WHERE department_id = 50;
```

## Insert with Default Values

### Using DEFAULT
```sql
-- Insert with explicit DEFAULT
INSERT INTO employees (employee_id, first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES (106, 'David', 'Wilson', 'david.wilson@company.com', DEFAULT, 'IT_PROG', 70000, 60);

-- Insert partial columns (others use defaults)
INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (107, 'Eva', 'Martinez', 'eva.martinez@company.com');
```

## Insert from Variables

### Parameterized Insert
```sql
-- Using parameters (application code)
INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES (?, ?, ?, ?, ?, ?, ?);

-- With named parameters
INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES (:first_name, :last_name, :email, :hire_date, :job_id, :salary, :department_id);
```

## Conditional Insert

### INSERT with WHERE
```sql
-- Insert only if condition is met
INSERT INTO high_earners (employee_id, name, salary)
SELECT employee_id, first_name || ' ' || last_name, salary
FROM employees 
WHERE salary > 80000
AND employee_id NOT IN (SELECT employee_id FROM high_earners);
```

## Database-Specific Syntax

### MySQL
```sql
-- MySQL - INSERT IGNORE (skip duplicates)
INSERT IGNORE INTO employees (employee_id, first_name, last_name, email)
VALUES (108, 'Frank', 'Miller', 'frank.miller@company.com');

-- MySQL - ON DUPLICATE KEY UPDATE
INSERT INTO employees (employee_id, first_name, last_name, email, salary)
VALUES (108, 'Frank', 'Miller', 'frank.miller@company.com', 60000)
ON DUPLICATE KEY UPDATE 
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    email = VALUES(email),
    salary = VALUES(salary);
```

### PostgreSQL
```sql
-- PostgreSQL - ON CONFLICT DO NOTHING
INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (108, 'Frank', 'Miller', 'frank.miller@company.com')
ON CONFLICT (employee_id) DO NOTHING;

-- PostgreSQL - ON CONFLICT UPDATE
INSERT INTO employees (employee_id, first_name, last_name, email, salary)
VALUES (108, 'Frank', 'Miller', 'frank.miller@company.com', 60000)
ON CONFLICT (employee_id) DO UPDATE 
SET first_name = EXCLUDED.first_name,
    last_name = EXCLUDED.last_name,
    email = EXCLUDED.email,
    salary = EXCLUDED.salary;
```

### SQL Server
```sql
-- SQL Server - INSERT with OUTPUT
INSERT INTO employees (employee_id, first_name, last_name, email, salary)
OUTPUT INSERTED.employee_id, INSERTED.first_name
VALUES (109, 'Grace', 'Taylor', 'grace.taylor@company.com', 65000);

-- SQL Server - MERGE for upsert
MERGE INTO employees AS target
USING (SELECT 109 AS employee_id, 'Grace' AS first_name, 'Taylor' AS last_name, 
              'grace.taylor@company.com' AS email, 65000 AS salary) AS source
ON target.employee_id = source.employee_id
WHEN MATCHED THEN
    UPDATE SET first_name = source.first_name, 
               last_name = source.last_name,
               email = source.email,
               salary = source.salary
WHEN NOT MATCHED THEN
    INSERT (employee_id, first_name, last_name, email, salary)
    VALUES (source.employee_id, source.first_name, source.last_name, source.email, source.salary);
```

## Examples

### Employee Management
```sql
-- Add new employee
INSERT INTO employees (employee_id, first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES (110, 'Henry', 'Anderson', 'henry.anderson@company.com', '2024-01-20', 'ST_MAN', 75000, 50);

-- Batch insert new hires
INSERT INTO employees (employee_id, first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES 
    (111, 'Ivy', 'Thomas', 'ivy.thomas@company.com', '2024-01-21', 'AD_ASST', 45000, 10),
    (112, 'Jack', 'Jackson', 'jack.jackson@company.com', '2024-01-22', 'FI_ACCOUNT', 68000, 70),
    (113, 'Kate', 'White', 'kate.white@company.com', '2024-01-23', 'PU_MAN', 72000, 20);

-- Copy employees to archive table
INSERT INTO employee_archive 
SELECT *, CURRENT_TIMESTAMP AS archived_date
FROM employees 
WHERE hire_date < '2023-01-01';
```

## Best Practices
- Always specify column names in INSERT statements
- Use parameterized queries to prevent SQL injection
- Consider using transactions for multiple related inserts
- Handle duplicate keys appropriately for your use case
- Use appropriate data types for optimal performance
- Consider bulk inserts for better performance with large datasets
