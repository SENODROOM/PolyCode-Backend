# SQL Stored Procedures

## Basic Stored Procedures

### Simple Procedure
```sql
-- Create basic stored procedure (MySQL)
DELIMITER //
CREATE PROCEDURE GetEmployeeById(IN emp_id INT)
BEGIN
    SELECT employee_id, first_name, last_name, email, salary
    FROM employees
    WHERE employee_id = emp_id;
END //
DELIMITER ;

-- Call the procedure
CALL GetEmployeeById(101);

-- PostgreSQL syntax
CREATE OR REPLACE FUNCTION GetEmployeeById(emp_id INT)
RETURNS TABLE (
    employee_id INT,
    first_name VARCHAR,
    last_name VARCHAR,
    email VARCHAR,
    salary DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT employee_id, first_name, last_name, email, salary
    FROM employees
    WHERE employee_id = emp_id;
END;
$$ LANGUAGE plpgsql;

-- Call the function
SELECT * FROM GetEmployeeById(101);
```

### Procedure with Parameters
```sql
-- Procedure with input and output parameters (SQL Server)
CREATE PROCEDURE sp_GetEmployeeDetails
    @emp_id INT,
    @first_name VARCHAR(50) OUTPUT,
    @last_name VARCHAR(50) OUTPUT,
    @salary DECIMAL(10,2) OUTPUT
AS
BEGIN
    SELECT 
        @first_name = first_name,
        @last_name = last_name,
        @salary = salary
    FROM employees
    WHERE employee_id = @emp_id;
END;

-- Execute with output parameters
DECLARE @first_name VARCHAR(50), @last_name VARCHAR(50), @salary DECIMAL(10,2);
EXEC sp_GetEmployeeDetails @emp_id = 101, 
    @first_name = @first_name OUTPUT, 
    @last_name = @last_name OUTPUT, 
    @salary = @salary OUTPUT;
SELECT @first_name, @last_name, @salary;
```

## Control Flow

### IF-ELSE Statements
```sql
-- MySQL procedure with conditional logic
DELIMITER //
CREATE PROCEDURE CheckEmployeeSalary(IN emp_id INT)
BEGIN
    DECLARE emp_salary DECIMAL(10,2);
    
    SELECT salary INTO emp_salary
    FROM employees
    WHERE employee_id = emp_id;
    
    IF emp_salary > 80000 THEN
        SELECT 'High Salary' as salary_level;
    ELSEIF emp_salary > 50000 THEN
        SELECT 'Medium Salary' as salary_level;
    ELSE
        SELECT 'Low Salary' as salary_level;
    END IF;
END //
DELIMITER ;

-- PostgreSQL version
CREATE OR REPLACE FUNCTION CheckEmployeeSalary(emp_id INT)
RETURNS TEXT AS $$
DECLARE
    emp_salary DECIMAL(10,2);
    result TEXT;
BEGIN
    SELECT salary INTO emp_salary
    FROM employees
    WHERE employee_id = emp_id;
    
    IF emp_salary > 80000 THEN
        result := 'High Salary';
    ELSIF emp_salary > 50000 THEN
        result := 'Medium Salary';
    ELSE
        result := 'Low Salary';
    END IF;
    
    RETURN result;
END;
$$ LANGUAGE plpgsql;
```

### CASE Statements
```sql
-- MySQL procedure with CASE
DELIMITER //
CREATE PROCEDURE GetEmployeeCategory(IN emp_id INT)
BEGIN
    SELECT 
        CASE 
            WHEN salary > 80000 THEN 'Executive'
            WHEN salary > 60000 THEN 'Senior'
            WHEN salary > 40000 THEN 'Mid-level'
            ELSE 'Junior'
        END as category
    FROM employees
    WHERE employee_id = emp_id;
END //
DELIMITER ;

-- SQL Server procedure with CASE
CREATE PROCEDURE sp_GetEmployeeCategory
    @emp_id INT
AS
BEGIN
    SELECT 
        CASE 
            WHEN salary > 80000 THEN 'Executive'
            WHEN salary > 60000 THEN 'Senior'
            WHEN salary > 40000 THEN 'Mid-level'
            ELSE 'Junior'
        END as category
    FROM employees
    WHERE employee_id = @emp_id;
END;
```

### Loops and Iteration
```sql
-- MySQL WHILE loop
DELIMITER //
CREATE PROCEDURE GenerateEmployeeReport()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE emp_id INT;
    DECLARE emp_name VARCHAR(100);
    DECLARE emp_salary DECIMAL(10,2);
    
    -- Cursor for employees
    DECLARE emp_cursor CURSOR FOR 
        SELECT employee_id, CONCAT(first_name, ' ', last_name), salary
        FROM employees
        WHERE department_id = 50;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Create temporary table for report
    CREATE TEMPORARY TABLE IF NOT EXISTS emp_report (
        employee_id INT,
        name VARCHAR(100),
        salary DECIMAL(10,2),
        bonus DECIMAL(10,2)
    );
    
    OPEN emp_cursor;
    
    emp_loop: LOOP
        FETCH emp_cursor INTO emp_id, emp_name, emp_salary;
        IF done THEN
            LEAVE emp_loop;
        END IF;
        
        -- Calculate bonus
        INSERT INTO emp_report VALUES (
            emp_id, 
            emp_name, 
            emp_salary, 
            emp_salary * 0.10
        );
    END LOOP;
    
    CLOSE emp_cursor;
    
    -- Return report
    SELECT * FROM emp_report ORDER BY salary DESC;
END //
DELIMITER ;

-- PostgreSQL FOR loop
CREATE OR REPLACE FUNCTION GenerateEmployeeReport()
RETURNS TABLE (
    employee_id INT,
    name VARCHAR,
    salary DECIMAL,
    bonus DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        employee_id,
        CONCAT(first_name, ' ', last_name),
        salary,
        salary * 0.10 as bonus
    FROM employees
    WHERE department_id = 50
    ORDER BY salary DESC;
END;
$$ LANGUAGE plpgsql;
```

## Error Handling

### Try-Catch Blocks
```sql
-- SQL Server error handling
CREATE PROCEDURE sp_InsertEmployee
    @first_name VARCHAR(50),
    @last_name VARCHAR(50),
    @email VARCHAR(100),
    @salary DECIMAL(10,2)
AS
BEGIN
    BEGIN TRY
        -- Insert employee
        INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
        VALUES (@first_name, @last_name, @email, GETDATE(), 'IT_PROG', @salary, 60);
        
        SELECT 'Employee created successfully' as message;
    END TRY
    BEGIN CATCH
        SELECT 
            ERROR_NUMBER() as error_number,
            ERROR_MESSAGE() as error_message,
            'Failed to create employee' as message;
    END CATCH
END;
```

### Exception Handling
```sql
-- PostgreSQL exception handling
CREATE OR REPLACE FUNCTION InsertEmployee(
    p_first_name VARCHAR,
    p_last_name VARCHAR,
    p_email VARCHAR,
    p_salary DECIMAL
) RETURNS TEXT AS $$
DECLARE
    v_employee_id INT;
BEGIN
    -- Insert employee
    INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
    VALUES (p_first_name, p_last_name, p_email, CURRENT_DATE, 'IT_PROG', p_salary, 60)
    RETURNING employee_id INTO v_employee_id;
    
    RETURN 'Employee created successfully: ' || v_employee_id;
    
EXCEPTION
    WHEN unique_violation THEN
        RETURN 'Error: Email already exists';
    WHEN OTHERS THEN
        RETURN 'Error: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;
```

## Database-Specific Procedures

### MySQL Procedures
```sql
-- MySQL stored procedure with multiple parameters
DELIMITER //
CREATE PROCEDURE sp_UpdateEmployeeSalary(
    IN emp_id INT,
    IN increase_percent DECIMAL(5,2),
    OUT new_salary DECIMAL(10,2)
)
BEGIN
    DECLARE current_salary DECIMAL(10,2);
    
    -- Get current salary
    SELECT salary INTO current_salary
    FROM employees
    WHERE employee_id = emp_id;
    
    -- Calculate new salary
    SET new_salary = current_salary * (1 + increase_percent / 100);
    
    -- Update salary
    UPDATE employees
    SET salary = new_salary
    WHERE employee_id = emp_id;
    
    -- Return new salary
    SELECT new_salary;
END //
DELIMITER ;

-- MySQL procedure with OUT parameters
DELIMITER //
CREATE PROCEDURE sp_GetDepartmentStats(
    IN dept_id INT,
    OUT emp_count INT,
    OUT avg_salary DECIMAL(10,2),
    OUT max_salary DECIMAL(10,2)
)
BEGIN
    SELECT 
        COUNT(*) INTO emp_count,
        AVG(salary) INTO avg_salary,
        MAX(salary) INTO max_salary
    FROM employees
    WHERE department_id = dept_id;
END //
DELIMITER ;
```

### PostgreSQL Functions
```sql
-- PostgreSQL function returning table
CREATE OR REPLACE FUNCTION GetEmployeesByDepartment(dept_id INT)
RETURNS TABLE (
    employee_id INT,
    first_name VARCHAR,
    last_name VARCHAR,
    email VARCHAR,
    salary DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT employee_id, first_name, last_name, email, salary
    FROM employees
    WHERE department_id = dept_id
    ORDER BY salary DESC;
END;
$$ LANGUAGE plpgsql;

-- PostgreSQL function with complex logic
CREATE OR REPLACE FUNCTION CalculateEmployeeBonus(emp_id INT)
RETURNS DECIMAL(10,2) AS $$
DECLARE
    v_salary DECIMAL(10,2);
    v_hire_date DATE;
    v_years_service INT;
    v_bonus DECIMAL(10,2);
BEGIN
    -- Get employee data
    SELECT salary, hire_date INTO v_salary, v_hire_date
    FROM employees
    WHERE employee_id = emp_id;
    
    -- Calculate years of service
    v_years_service := EXTRACT(YEAR FROM AGE(CURRENT_DATE, v_hire_date));
    
    -- Calculate bonus based on salary and service
    IF v_years_service > 10 THEN
        v_bonus := v_salary * 0.15; -- 15% for 10+ years
    ELSIF v_years_service > 5 THEN
        v_bonus := v_salary * 0.10; -- 10% for 5+ years
    ELSIF v_years_service > 2 THEN
        v_bonus := v_salary * 0.05; -- 5% for 2+ years
    ELSE
        v_bonus := v_salary * 0.02; -- 2% for less than 2 years
    END IF;
    
    -- Cap bonus at $5000
    IF v_bonus > 5000 THEN
        v_bonus := 5000;
    END IF;
    
    RETURN v_bonus;
END;
$$ LANGUAGE plpgsql;
```

### SQL Server Procedures
```sql
-- SQL Server procedure with table-valued parameter
CREATE TYPE EmployeeTable AS TABLE (
    employee_id INT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100)
);

CREATE PROCEDURE sp_BulkInsertEmployees
    @employees EmployeeTable READONLY
AS
BEGIN
    SET NOCOUNT ON;
    
    INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
    SELECT 
        first_name, 
        last_name, 
        email, 
        GETDATE(), 
        'IT_PROG', 
        50000, 
        60
    FROM @employees;
    
    SELECT CAST(@@ROWCOUNT AS VARCHAR) + ' employees inserted';
END;

-- SQL Server procedure with dynamic SQL
CREATE PROCEDURE sp_GetDynamicEmployeeData
    @department_id INT = NULL,
    @job_id VARCHAR(20) = NULL,
    @min_salary DECIMAL(10,2) = NULL,
    @max_salary DECIMAL(10,2) = NULL
AS
BEGIN
    DECLARE @sql NVARCHAR(MAX);
    DECLARE @params NVARCHAR(MAX);
    
    SET @sql = N'
        SELECT employee_id, first_name, last_name, email, salary
        FROM employees
        WHERE 1=1';
    
    SET @params = N'@dept_id INT, @job_id VARCHAR(20), @min_sal DECIMAL(10,2), @max_sal DECIMAL(10,2)';
    
    IF @department_id IS NOT NULL
        SET @sql = @sql + N' AND department_id = @dept_id';
    
    IF @job_id IS NOT NULL
        SET @sql = @sql + N' AND job_id = @job_id';
    
    IF @min_salary IS NOT NULL
        SET @sql = @sql + N' AND salary >= @min_sal';
    
    IF @max_salary IS NOT NULL
        SET @sql = @sql + N' AND salary <= @max_sal';
    
    EXEC sp_executesql @sql, @params, 
        @department_id, @job_id, @min_salary, @max_salary;
END;
```

## Advanced Procedures

### Transaction Management
```sql
-- PostgreSQL procedure with transaction
CREATE OR REPLACE FUNCTION TransferFunds(
    from_account INT,
    to_account INT,
    amount DECIMAL(10,2)
) RETURNS TEXT AS $$
DECLARE
    v_from_balance DECIMAL(10,2);
    v_to_balance DECIMAL(10,2);
BEGIN
    -- Start transaction
    -- Check balances and transfer within transaction
    
    -- Get from account balance
    SELECT balance INTO v_from_balance
    FROM accounts
    WHERE account_id = from_account;
    
    -- Check sufficient funds
    IF v_from_balance < amount THEN
        RETURN 'Insufficient funds';
    END IF;
    
    -- Update accounts
    UPDATE accounts
    SET balance = balance - amount
    WHERE account_id = from_account;
    
    UPDATE accounts
    SET balance = balance + amount
    WHERE account_id = to_account;
    
    -- Record transaction
    INSERT INTO transactions (from_account, to_account, amount, transaction_date)
    VALUES (from_account, to_account, amount, CURRENT_DATE);
    
    RETURN 'Transfer completed successfully';
    
EXCEPTION
    WHEN OTHERS THEN
        -- Rollback is automatic in PostgreSQL if exception occurs
        RETURN 'Transfer failed: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;
```

### Recursive Procedures
```sql
-- PostgreSQL recursive function for hierarchy
CREATE OR REPLACE FUNCTION GetEmployeeHierarchy(emp_id INT)
RETURNS TABLE (
    employee_id INT,
    first_name VARCHAR,
    last_name VARCHAR,
    manager_id INT,
    level INT
) AS $$
BEGIN
    -- Base case
    RETURN QUERY
    SELECT 
        employee_id,
        first_name,
        last_name,
        manager_id,
        1 as level
    FROM employees
    WHERE employee_id = emp_id;
    
    -- Recursive case
    RETURN QUERY
    SELECT 
        e.employee_id,
        e.first_name,
        e.last_name,
        e.manager_id,
        h.level + 1
    FROM employees e
    JOIN GetEmployeeHierarchy(emp_id) h ON e.manager_id = h.employee_id;
END;
$$ LANGUAGE plpgsql;

-- MySQL recursive procedure
DELIMITER //
CREATE PROCEDURE sp_GetEmployeeHierarchy(IN root_emp_id INT)
BEGIN
    -- Create temporary table for hierarchy
    CREATE TEMPORARY TABLE IF NOT EXISTS emp_hierarchy (
        employee_id INT,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        manager_id INT,
        level INT
    );
    
    -- Clear table
    TRUNCATE TABLE emp_hierarchy;
    
    -- Insert root employee
    INSERT INTO emp_hierarchy
    SELECT employee_id, first_name, last_name, manager_id, 1
    FROM employees
    WHERE employee_id = root_emp_id;
    
    -- Recursive insert
    REPEAT
        INSERT INTO emp_hierarchy
        SELECT e.employee_id, e.first_name, e.last_name, e.manager_id, h.level + 1
        FROM employees e
        JOIN emp_hierarchy h ON e.manager_id = h.employee_id
        WHERE h.level = (SELECT MAX(level) FROM emp_hierarchy);
    UNTIL ROW_COUNT() = 0;
    
    -- Return hierarchy
    SELECT * FROM emp_hierarchy ORDER BY level, employee_id;
END //
DELIMITER ;
```

## Examples

### Employee Management System
```sql
-- Complete employee management procedures

-- Add new employee
DELIMITER //
CREATE PROCEDURE sp_AddEmployee(
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_salary DECIMAL(10,2),
    IN p_department_id INT,
    IN p_job_id VARCHAR(20),
    OUT p_employee_id INT,
    OUT p_message VARCHAR(200)
)
BEGIN
    DECLARE v_duplicate_email INT;
    
    -- Check for duplicate email
    SELECT COUNT(*) INTO v_duplicate_email
    FROM employees
    WHERE email = p_email;
    
    IF v_duplicate_email > 0 THEN
        SET p_message = 'Email already exists';
        SET p_employee_id = 0;
    ELSE
        -- Insert employee
        INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
        VALUES (p_first_name, p_last_name, p_email, CURRENT_DATE, p_job_id, p_salary, p_department_id);
        
        SET p_employee_id = LAST_INSERT_ID();
        SET p_message = 'Employee created successfully';
    END IF;
END //
DELIMITER ;

-- Update employee salary
DELIMITER //
CREATE PROCEDURE sp_UpdateEmployeeSalary(
    IN p_employee_id INT,
    IN p_increase_percent DECIMAL(5,2),
    OUT p_old_salary DECIMAL(10,2),
    OUT p_new_salary DECIMAL(10,2),
    OUT p_message VARCHAR(200)
)
BEGIN
    -- Get current salary
    SELECT salary INTO p_old_salary
    FROM employees
    WHERE employee_id = p_employee_id;
    
    IF p_old_salary IS NULL THEN
        SET p_message = 'Employee not found';
        SET p_new_salary = 0;
    ELSE
        -- Calculate new salary
        SET p_new_salary = p_old_salary * (1 + p_increase_percent / 100);
        
        -- Update salary
        UPDATE employees
        SET salary = p_new_salary
        WHERE employee_id = p_employee_id;
        
        SET p_message = 'Salary updated successfully';
    END IF;
END //
DELIMITER ;

-- Get department statistics
DELIMITER //
CREATE PROCEDURE sp_GetDepartmentStatistics(IN p_department_id INT)
BEGIN
    -- Create temporary table for results
    CREATE TEMPORARY TABLE IF NOT EXISTS dept_stats (
        metric VARCHAR(50),
        value VARCHAR(100)
    );
    
    -- Employee count
    INSERT INTO dept_stats
    SELECT 'Employee Count', CAST(COUNT(*) AS VARCHAR)
    FROM employees
    WHERE department_id = p_department_id;
    
    -- Average salary
    INSERT INTO dept_stats
    SELECT 'Average Salary', CAST(ROUND(AVG(salary), 2) AS VARCHAR)
    FROM employees
    WHERE department_id = p_department_id;
    
    -- Salary range
    INSERT INTO dept_stats
    SELECT 'Salary Range', 
           CONCAT(CAST(MIN(salary) AS VARCHAR), ' - ', CAST(MAX(salary) AS VARCHAR))
    FROM employees
    WHERE department_id = p_department_id;
    
    -- Newest hire
    INSERT INTO dept_stats
    SELECT 'Newest Hire', DATE_FORMAT(MAX(hire_date), '%Y-%m-%d')
    FROM employees
    WHERE department_id = p_department_id;
    
    -- Return results
    SELECT * FROM dept_stats;
END //
DELIMITER ;
```

## Best Practices
- Use meaningful parameter names
- Include proper error handling
- Use transactions for data consistency
- Document procedure purpose and parameters
- Keep procedures focused on single responsibility
- Use appropriate data types for parameters
- Include input validation
- Use SET NOCOUNT ON in SQL Server for performance
- Consider security implications (SQL injection)
- Test procedures with various data scenarios
- Use appropriate exception handling for your database system
