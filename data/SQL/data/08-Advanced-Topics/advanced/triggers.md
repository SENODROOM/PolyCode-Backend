# SQL Triggers

## Basic Triggers

### Simple INSERT Trigger
```sql
-- Trigger to log new employee insertions (MySQL)
DELIMITER //
CREATE TRIGGER tr_employee_insert_log
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    INSERT INTO employee_audit (
        employee_id,
        action,
        old_values,
        new_values,
        changed_by,
        changed_at
    ) VALUES (
        NEW.employee_id,
        'INSERT',
        NULL,
        JSON_OBJECT(
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
            'email', NEW.email,
            'salary', NEW.salary
        ),
        CURRENT_USER(),
        NOW()
    );
END //
DELIMITER ;

-- PostgreSQL trigger for logging
CREATE OR REPLACE FUNCTION tr_employee_insert_log()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO employee_audit (
        employee_id,
        action,
        old_values,
        new_values,
        changed_by,
        changed_at
    ) VALUES (
        NEW.employee_id,
        'INSERT',
        NULL,
        json_build_object(
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
            'email', NEW.email,
            'salary', NEW.salary
        ),
        current_user,
        current_timestamp
    );
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_employee_insert_log
AFTER INSERT ON employees
FOR EACH ROW
EXECUTE FUNCTION tr_employee_insert_log();
```

### UPDATE Trigger
```sql
-- Trigger to track salary changes (MySQL)
DELIMITER //
CREATE TRIGGER tr_salary_update_log
BEFORE UPDATE ON employees
FOR EACH ROW
BEGIN
    IF OLD.salary != NEW.salary THEN
        INSERT INTO salary_history (
            employee_id,
            old_salary,
            new_salary,
            change_date,
            changed_by
        ) VALUES (
            NEW.employee_id,
            OLD.salary,
            NEW.salary,
            NOW(),
            CURRENT_USER()
        );
    END IF;
END //
DELIMITER ;

-- SQL Server update trigger
CREATE TRIGGER tr_employee_update
ON employees
AFTER UPDATE
AS
BEGIN
    IF UPDATE(salary)
    BEGIN
        INSERT INTO salary_history (
            employee_id,
            old_salary,
            new_salary,
            change_date,
            changed_by
        )
        SELECT 
            i.employee_id,
            d.salary, -- old salary from deleted table
            i.salary, -- new salary from inserted table
            GETDATE(),
            SUSER_SNAME()
        FROM inserted i
        JOIN deleted d ON i.employee_id = d.employee_id
        WHERE i.salary != d.salary;
    END
END;
```

### DELETE Trigger
```sql
-- Trigger to archive deleted employees (MySQL)
DELIMITER //
CREATE TRIGGER tr_employee_delete_archive
BEFORE DELETE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO employee_archive (
        employee_id,
        first_name,
        last_name,
        email,
        hire_date,
        salary,
        department_id,
        archived_date,
        archived_by
    ) VALUES (
        OLD.employee_id,
        OLD.first_name,
        OLD.last_name,
        OLD.email,
        OLD.hire_date,
        OLD.salary,
        OLD.department_id,
        NOW(),
        CURRENT_USER()
    );
END //
DELIMITER ;

-- PostgreSQL delete trigger
CREATE OR REPLACE FUNCTION tr_employee_delete_archive()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO employee_archive (
        employee_id,
        first_name,
        last_name,
        email,
        hire_date,
        salary,
        department_id,
        archived_date,
        archived_by
    ) VALUES (
        OLD.employee_id,
        OLD.first_name,
        OLD.last_name,
        OLD.email,
        OLD.hire_date,
        OLD.salary,
        OLD.department_id,
        current_timestamp,
        current_user
    );
    
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_employee_delete_archive
BEFORE DELETE ON employees
FOR EACH ROW
EXECUTE FUNCTION tr_employee_delete_archive();
```

## Advanced Triggers

### Conditional Triggers
```sql
-- Trigger with business logic (MySQL)
DELIMITER //
CREATE TRIGGER tr_employee_salary_validation
BEFORE INSERT ON employees
FOR EACH ROW
BEGIN
    -- Validate salary range
    IF NEW.salary < 20000 OR NEW.salary > 200000 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Salary must be between 20000 and 200000';
    END IF;
    
    -- Set default department if not specified
    IF NEW.department_id IS NULL THEN
        SET NEW.department_id = 10;
    END IF;
    
    -- Set hire date if not specified
    IF NEW.hire_date IS NULL THEN
        SET NEW.hire_date = CURRENT_DATE();
    END IF;
END //
DELIMITER ;

-- PostgreSQL conditional trigger
CREATE OR REPLACE FUNCTION tr_employee_salary_validation()
RETURNS TRIGGER AS $$
BEGIN
    -- Validate salary range
    IF NEW.salary < 20000 OR NEW.salary > 200000 THEN
        RAISE EXCEPTION 'Salary must be between 20000 and 200000';
    END IF;
    
    -- Set default department if not specified
    IF NEW.department_id IS NULL THEN
        NEW.department_id := 10;
    END IF;
    
    -- Set hire date if not specified
    IF NEW.hire_date IS NULL THEN
        NEW.hire_date := current_timestamp;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_employee_salary_validation
BEFORE INSERT OR UPDATE ON employees
FOR EACH ROW
EXECUTE FUNCTION tr_employee_salary_validation();
```

### Complex Trigger with Multiple Tables
```sql
-- Trigger to update department statistics (MySQL)
DELIMITER //
CREATE TRIGGER tr_employee_dept_stats_update
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    -- Update employee count
    UPDATE departments
    SET employee_count = (
        SELECT COUNT(*) 
        FROM employees 
        WHERE department_id = NEW.department_id
    ),
    avg_salary = (
        SELECT AVG(salary) 
        FROM employees 
        WHERE department_id = NEW.department_id
    )
    WHERE department_id = NEW.department_id;
END //
DELIMITER ;

-- Trigger for both insert and delete (PostgreSQL)
CREATE OR REPLACE FUNCTION tr_employee_dept_stats_update()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        -- Update for new employee
        UPDATE departments
        SET employee_count = employee_count + 1,
            avg_salary = (
                SELECT AVG(salary) 
                FROM employees 
                WHERE department_id = NEW.department_id
            )
        WHERE department_id = NEW.department_id;
        
    ELSIF TG_OP = 'DELETE' THEN
        -- Update for deleted employee
        UPDATE departments
        SET employee_count = employee_count - 1,
            avg_salary = (
                SELECT AVG(salary) 
                FROM employees 
                WHERE department_id = OLD.department_id
            )
        WHERE department_id = OLD.department_id;
        
    ELSIF TG_OP = 'UPDATE' THEN
        -- Update for department change
        IF OLD.department_id != NEW.department_id THEN
            -- Decrement old department
            UPDATE departments
            SET employee_count = employee_count - 1,
                avg_salary = (
                    SELECT AVG(salary) 
                    FROM employees 
                    WHERE department_id = OLD.department_id
                )
            WHERE department_id = OLD.department_id;
            
            -- Increment new department
            UPDATE departments
            SET employee_count = employee_count + 1,
                avg_salary = (
                    SELECT AVG(salary) 
                    FROM employees 
                    WHERE department_id = NEW.department_id
                )
            WHERE department_id = NEW.department_id;
        END IF;
    END IF;
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_employee_dept_stats_update
AFTER INSERT OR DELETE OR UPDATE ON employees
FOR EACH ROW
EXECUTE FUNCTION tr_employee_dept_stats_update();
```

## Database-Specific Triggers

### MySQL Triggers
```sql
-- MySQL trigger syntax variations
-- BEFORE/AFTER INSERT/UPDATE/DELETE
-- FOR EACH ROW (row-level trigger)
-- FOR EACH STATEMENT (statement-level trigger)

-- Statement-level trigger
DELIMITER //
CREATE TRIGGER tr_employee_insert_statement
AFTER INSERT ON employees
FOR EACH STATEMENT
BEGIN
    INSERT INTO operation_log (
        operation_type,
        table_name,
        affected_rows,
        operation_time,
        user_name
    ) VALUES (
        'INSERT',
        'employees',
        ROW_COUNT(),
        NOW(),
        CURRENT_USER()
    );
END //
DELIMITER ;

-- Trigger with error handling
DELIMITER //
CREATE TRIGGER tr_employee_business_rules
BEFORE UPDATE ON employees
FOR EACH ROW
BEGIN
    -- Prevent salary decrease
    IF NEW.salary < OLD.salary THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Salary cannot be decreased';
    END IF;
    
    -- Prevent changes to archived employees
    IF OLD.status = 'ARCHIVED' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot modify archived employees';
    END IF;
END //
DELIMITER ;
```

### PostgreSQL Triggers
```sql
-- PostgreSQL trigger with TG variables
CREATE OR REPLACE FUNCTION tr_employee_audit()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO employee_audit (
        operation_type,
        table_name,
        employee_id,
        old_data,
        new_data,
        operation_time,
        user_name
    ) VALUES (
        TG_OP,
        TG_TABLE_NAME,
        COALESCE(NEW.employee_id, OLD.employee_id),
        CASE 
            WHEN TG_OP = 'DELETE' THEN row_to_json(OLD)
            ELSE NULL
        END,
        CASE 
            WHEN TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN row_to_json(NEW)
            ELSE NULL
        END,
        current_timestamp,
        current_user
    );
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_employee_audit
AFTER INSERT OR UPDATE OR DELETE ON employees
FOR EACH ROW
EXECUTE FUNCTION tr_employee_audit();

-- Conditional trigger with WHEN clause
CREATE TRIGGER tr_employee_high_salary_audit
AFTER INSERT OR UPDATE ON employees
FOR EACH ROW
WHEN (NEW.salary > 100000)
EXECUTE FUNCTION tr_employee_audit();
```

### SQL Server Triggers
```sql
-- SQL Server trigger with inserted/deleted tables
CREATE TRIGGER tr_employee_audit
ON employees
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Handle inserts
    IF EXISTS (SELECT 1 FROM inserted)
    BEGIN
        INSERT INTO employee_audit (
            operation_type,
            employee_id,
            old_data,
            new_data,
            operation_time,
            user_name
        )
        SELECT 
            'INSERT',
            i.employee_id,
            NULL,
            (SELECT * FROM inserted WHERE employee_id = i.employee_id FOR JSON PATH),
            GETDATE(),
            SUSER_SNAME()
        FROM inserted i;
    END
    
    -- Handle updates
    IF EXISTS (SELECT 1 FROM inserted) AND EXISTS (SELECT 1 FROM deleted)
    BEGIN
        INSERT INTO employee_audit (
            operation_type,
            employee_id,
            old_data,
            new_data,
            operation_time,
            user_name
        )
        SELECT 
            'UPDATE',
            i.employee_id,
            (SELECT * FROM deleted WHERE employee_id = i.employee_id FOR JSON PATH),
            (SELECT * FROM inserted WHERE employee_id = i.employee_id FOR JSON PATH),
            GETDATE(),
            SUSER_SNAME()
        FROM inserted i
        JOIN deleted d ON i.employee_id = d.employee_id;
    END
    
    -- Handle deletes
    IF EXISTS (SELECT 1 FROM deleted)
    BEGIN
        INSERT INTO employee_audit (
            operation_type,
            employee_id,
            old_data,
            new_data,
            operation_time,
            user_name
        )
        SELECT 
            'DELETE',
            d.employee_id,
            (SELECT * FROM deleted WHERE employee_id = d.employee_id FOR JSON PATH),
            NULL,
            GETDATE(),
            SUSER_SNAME()
        FROM deleted d;
    END
END;

-- Instead of trigger
CREATE TRIGGER tr_employee_view_insert
ON employee_view
INSTEAD OF INSERT
AS
BEGIN
    -- Validation and transformation
    DECLARE @first_name VARCHAR(50);
    DECLARE @last_name VARCHAR(50);
    DECLARE @email VARCHAR(100);
    
    SELECT 
        UPPER(SUBSTRING(first_name, 1, 1)) + LOWER(SUBSTRING(first_name, 2, 49)),
        UPPER(SUBSTRING(last_name, 1, 1)) + LOWER(SUBSTRING(last_name, 2, 49)),
        LOWER(email)
    INTO @first_name, @last_name, @email
    FROM inserted;
    
    -- Insert into base table
    INSERT INTO employees (first_name, last_name, email, hire_date, job_id, salary, department_id)
    VALUES (@first_name, @last_name, @email, GETDATE(), 'IT_PROG', 50000, 60);
END;
```

### Oracle Triggers
```sql
-- Oracle trigger syntax
CREATE OR REPLACE TRIGGER tr_employee_audit
AFTER INSERT OR UPDATE OR DELETE ON employees
FOR EACH ROW
DECLARE
    v_operation VARCHAR(10);
BEGIN
    -- Determine operation type
    IF INSERTING THEN
        v_operation := 'INSERT';
    ELSIF UPDATING THEN
        v_operation := 'UPDATE';
    ELSIF DELETING THEN
        v_operation := 'DELETE';
    END IF;
    
    -- Insert audit record
    INSERT INTO employee_audit (
        operation_type,
        employee_id,
        old_data,
        new_data,
        operation_time,
        user_name
    ) VALUES (
        v_operation,
        :NEW.employee_id,
        CASE 
            WHEN DELETING THEN JSON_OBJECT(
                'employee_id', :OLD.employee_id,
                'first_name', :OLD.first_name,
                'last_name', :OLD.last_name
            )
            ELSE NULL
        END,
        CASE 
            WHEN INSERTING OR UPDATING THEN JSON_OBJECT(
                'employee_id', :NEW.employee_id,
                'first_name', :NEW.first_name,
                'last_name', :NEW.last_name
            )
            ELSE NULL
        END,
        SYSTIMESTAMP,
        USER
    );
END;
/

-- Compound trigger (Oracle 11g+)
CREATE OR REPLACE TRIGGER tr_employee_compound
FOR INSERT OR UPDATE OR DELETE ON employees
COMPOUND TRIGGER
    -- Before statement level
    BEFORE STATEMENT IS
    BEGIN
        INSERT INTO operation_log (
            operation_type,
            table_name,
            operation_time
        ) VALUES (
            CASE 
                WHEN INSERTING THEN 'INSERT'
                WHEN UPDATING THEN 'UPDATE'
                WHEN DELETING THEN 'DELETE'
            END,
            'EMPLOYEES',
            SYSTIMESTAMP
        );
    END BEFORE STATEMENT;
    
    -- After each row
    AFTER EACH ROW IS
    BEGIN
        INSERT INTO employee_audit (
            operation_type,
            employee_id,
            operation_time
        ) VALUES (
            CASE 
                WHEN INSERTING THEN 'INSERT'
                WHEN UPDATING THEN 'UPDATE'
                WHEN DELETING THEN 'DELETE'
            END,
            :NEW.employee_id,
            SYSTIMESTAMP
        );
    END AFTER EACH ROW;
    
    -- After statement level
    AFTER STATEMENT IS
    BEGIN
        INSERT INTO operation_log (
            operation_type,
            table_name,
            rows_affected,
            operation_time
        ) VALUES (
            CASE 
                WHEN INSERTING THEN 'INSERT'
                WHEN UPDATING THEN 'UPDATE'
                WHEN DELETING THEN 'DELETE'
            END,
            'EMPLOYEES',
            SQL%ROWCOUNT,
            SYSTIMESTAMP
        );
    END AFTER STATEMENT;
END tr_employee_compound;
/
```

## Trigger Management

### Create Trigger
```sql
-- Basic trigger creation
CREATE TRIGGER trigger_name
{BEFORE | AFTER}
{INSERT | UPDATE | DELETE}
ON table_name
[FOR EACH ROW]
BEGIN
    -- trigger logic
END;

-- Conditional trigger creation (PostgreSQL)
CREATE TRIGGER trigger_name
{BEFORE | AFTER}
{INSERT | UPDATE | DELETE}
ON table_name
FOR EACH ROW
WHEN (condition)
BEGIN
    -- trigger logic
END;
```

### Modify Trigger
```sql
-- MySQL
ALTER TRIGGER trigger_name
{BEFORE | AFTER}
{INSERT | UPDATE | DELETE}
ON table_name
FOR EACH ROW
BEGIN
    -- modified logic
END;

-- PostgreSQL
CREATE OR REPLACE TRIGGER trigger_name
{BEFORE | AFTER}
{INSERT | UPDATE | DELETE}
ON table_name
FOR EACH ROW
BEGIN
    -- modified logic
END;

-- SQL Server
ALTER TRIGGER trigger_name
ON table_name
{AFTER | INSTEAD OF}
{INSERT | UPDATE | DELETE}
AS
BEGIN
    -- modified logic
END;
```

### Drop Trigger
```sql
-- Drop trigger
DROP TRIGGER trigger_name;

-- Drop trigger if exists (PostgreSQL)
DROP TRIGGER IF EXISTS trigger_name;

-- Drop trigger with schema qualification (SQL Server)
DROP TRIGGER schema_name.trigger_name;
```

### Disable/Enable Triggers
```sql
-- MySQL
ALTER TABLE table_name DISABLE TRIGGER trigger_name;
ALTER TABLE table_name ENABLE TRIGGER trigger_name;

-- Disable all triggers on table
ALTER TABLE table_name DISABLE TRIGGER ALL;
ALTER TABLE table_name ENABLE TRIGGER ALL;

-- SQL Server
DISABLE TRIGGER trigger_name ON table_name;
ENABLE TRIGGER trigger_name ON table_name;

-- Disable all triggers on table
DISABLE TRIGGER ALL ON table_name;
ENABLE TRIGGER ALL ON table_name;
```

## Examples

### Employee Management System
```sql
-- Comprehensive employee management triggers

-- 1. Data validation trigger
DELIMITER //
CREATE TRIGGER tr_employee_validation
BEFORE INSERT ON employees
FOR EACH ROW
BEGIN
    -- Validate email format
    IF NEW.email NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid email format';
    END IF;
    
    -- Validate salary range
    IF NEW.salary < 20000 OR NEW.salary > 200000 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Salary must be between 20000 and 200000';
    END IF;
    
    -- Validate hire date not in future
    IF NEW.hire_date > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Hire date cannot be in the future';
    END IF;
    
    -- Set default values
    IF NEW.status IS NULL THEN
        SET NEW.status = 'ACTIVE';
    END IF;
    
    IF NEW.created_at IS NULL THEN
        SET NEW.created_at = CURRENT_TIMESTAMP;
    END IF;
END //
DELIMITER ;

-- 2. Audit trail trigger
DELIMITER //
CREATE TRIGGER tr_employee_audit
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    INSERT INTO employee_audit (
        employee_id,
        action,
        field_name,
        old_value,
        new_value,
        changed_by,
        changed_at
    ) VALUES (
        NEW.employee_id,
        'INSERT',
        'EMPLOYEE_CREATED',
        NULL,
        JSON_OBJECT(
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
            'email', NEW.email,
            'salary', NEW.salary,
            'department_id', NEW.department_id,
            'status', NEW.status
        ),
        CURRENT_USER(),
        NOW()
    );
END //
DELIMITER ;

-- 3. Business logic trigger
DELIMITER //
CREATE TRIGGER tr_employee_business_logic
BEFORE UPDATE ON employees
FOR EACH ROW
BEGIN
    -- Prevent salary decrease for active employees
    IF OLD.status = 'ACTIVE' AND NEW.salary < OLD.salary THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot decrease salary for active employees';
    END IF;
    
    -- Prevent status change to active if salary is too low
    IF OLD.status != 'ACTIVE' AND NEW.status = 'ACTIVE' AND NEW.salary < 30000 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot activate employee with salary below 30000';
    END IF;
    
    -- Update modified timestamp
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- 4. Cascade update trigger
DELIMITER //
CREATE TRIGGER tr_employee_cascade_update
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    -- Update related records when employee status changes
    IF OLD.status != NEW.status THEN
        UPDATE employee_projects
        SET status = CASE 
            WHEN NEW.status = 'ACTIVE' THEN 'ACTIVE'
            ELSE 'INACTIVE'
        END
        WHERE employee_id = NEW.employee_id;
        
        -- Update access permissions
        IF NEW.status = 'INACTIVE' THEN
            UPDATE user_permissions
            SET is_active = 0
            WHERE employee_id = NEW.employee_id;
        ELSIF NEW.status = 'ACTIVE' THEN
            UPDATE user_permissions
            SET is_active = 1
            WHERE employee_id = NEW.employee_id;
        END IF;
    END IF;
END //
DELIMITER ;
```

### Inventory Management
```sql
-- Inventory management triggers

-- 1. Stock validation trigger
DELIMITER //
CREATE TRIGGER tr_inventory_validation
BEFORE INSERT ON order_items
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    
    -- Check stock availability
    SELECT stock INTO current_stock
    FROM inventory
    WHERE product_id = NEW.product_id;
    
    IF current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = CONCAT('Insufficient stock. Available: ', current_stock, ', Requested: ', NEW.quantity);
    END IF;
    
    -- Reserve stock
    UPDATE inventory
    SET reserved_stock = reserved_stock + NEW.quantity,
        available_stock = available_stock - NEW.quantity
    WHERE product_id = NEW.product_id;
END //
DELIMITER ;

-- 2. Stock release trigger
DELIMITER //
CREATE TRIGGER tr_inventory_release
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    -- Release reserved stock when order is cancelled
    IF OLD.status != 'CANCELLED' AND NEW.status = 'CANCELLED' THEN
        UPDATE inventory i
        JOIN order_items oi ON i.product_id = oi.product_id
        SET i.reserved_stock = i.reserved_stock - oi.quantity,
            i.available_stock = i.available_stock + oi.quantity
        WHERE oi.order_id = NEW.order_id;
    END IF;
    
    -- Consume reserved stock when order is completed
    IF OLD.status != 'COMPLETED' AND NEW.status = 'COMPLETED' THEN
        UPDATE inventory i
        JOIN order_items oi ON i.product_id = oi.product_id
        SET i.reserved_stock = i.reserved_stock - oi.quantity,
            i.stock = i.stock - oi.quantity
        WHERE oi.order_id = NEW.order_id;
        
        -- Update inventory log
        INSERT INTO inventory_log (
            product_id,
            transaction_type,
            quantity,
            reference_id,
            transaction_date
        )
        SELECT 
            oi.product_id,
            'SALE',
            oi.quantity,
            NEW.order_id,
            NOW()
        FROM order_items oi
        WHERE oi.order_id = NEW.order_id;
    END IF;
END //
DELIMITER ;

-- 3. Reorder trigger
DELIMITER //
CREATE TRIGGER tr_inventory_reorder
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    DECLARE reorder_level INT;
    DECLARE current_stock INT;
    
    -- Get reorder level and current stock
    SELECT reorder_level, stock INTO reorder_level, current_stock
    FROM inventory
    WHERE product_id = NEW.product_id;
    
    -- Check if reorder is needed
    IF current_stock <= reorder_level THEN
        INSERT INTO purchase_orders (
            product_id,
            quantity,
            status,
            created_at
        ) VALUES (
            NEW.product_id,
            NEW.reorder_quantity,
            'PENDING',
            NOW()
        );
        
        -- Notify purchasing department
        INSERT INTO notifications (
            message,
            type,
            reference_id,
            created_at
        ) VALUES (
            CONCAT('Reorder needed for product ID: ', NEW.product_id),
            'INVENTORY',
            NEW.product_id,
            NOW()
        );
    END IF;
END //
DELIMITER ;
```

## Best Practices
- Keep triggers simple and focused
- Avoid complex logic in triggers
- Use triggers for data integrity and business rules
- Document trigger purpose and behavior
- Test triggers thoroughly
- Consider performance impact
- Avoid recursive triggers when possible
- Use appropriate trigger timing (BEFORE/AFTER)
- Handle errors gracefully
- Monitor trigger execution
- Use triggers sparingly - don't overuse
- Consider using stored procedures instead for complex logic
