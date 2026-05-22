#include <stdio.h>
#include <stdlib.h>
#include <string.h>

typedef struct {
    int id;
    char name[50];
    double salary;
} Employee;

void writeBinaryFile(const char* filename) {
    FILE* file = fopen(filename, "wb");
    if (file == NULL) {
        printf("Error: Could not create file %s\n", filename);
        return;
    }
    
    Employee employees[] = {
        {1, "John Doe", 50000.0},
        {2, "Jane Smith", 60000.0},
        {3, "Bob Johnson", 55000.0},
        {4, "Alice Brown", 65000.0}
    };
    
    int num_employees = sizeof(employees) / sizeof(employees[0]);
    
    // Write the number of employees first
    fwrite(&num_employees, sizeof(int), 1, file);
    
    // Write all employees
    fwrite(employees, sizeof(Employee), num_employees, file);
    
    fclose(file);
    printf("Successfully wrote %d employees to %s\n", num_employees, filename);
}

void readBinaryFile(const char* filename) {
    FILE* file = fopen(filename, "rb");
    if (file == NULL) {
        printf("Error: Could not open file %s\n", filename);
        return;
    }
    
    int num_employees;
    
    // Read the number of employees
    fread(&num_employees, sizeof(int), 1, file);
    
    Employee* employees = (Employee*)malloc(num_employees * sizeof(Employee));
    
    // Read all employees
    fread(employees, sizeof(Employee), num_employees, file);
    
    fclose(file);
    
    printf("Employee Records:\n");
    printf("================\n");
    for (int i = 0; i < num_employees; i++) {
        printf("ID: %d\n", employees[i].id);
        printf("Name: %s\n", employees[i].name);
        printf("Salary: %.2f\n", employees[i].salary);
        printf("----------------\n");
    }
    
    free(employees);
}

void findEmployeeById(const char* filename, int search_id) {
    FILE* file = fopen(filename, "rb");
    if (file == NULL) {
        printf("Error: Could not open file %s\n", filename);
        return;
    }
    
    int num_employees;
    fread(&num_employees, sizeof(int), 1, file);
    
    Employee emp;
    int found = 0;
    
    for (int i = 0; i < num_employees; i++) {
        fread(&emp, sizeof(Employee), 1, file);
        if (emp.id == search_id) {
            printf("Employee Found:\n");
            printf("ID: %d\n", emp.id);
            printf("Name: %s\n", emp.name);
            printf("Salary: %.2f\n", emp.salary);
            found = 1;
            break;
        }
    }
    
    if (!found) {
        printf("Employee with ID %d not found\n", search_id);
    }
    
    fclose(file);
}

void appendEmployee(const char* filename, Employee new_emp) {
    FILE* file = fopen(filename, "rb+");
    if (file == NULL) {
        printf("Error: Could not open file %s\n", filename);
        return;
    }
    
    int num_employees;
    fread(&num_employees, sizeof(int), 1, file);
    
    // Check if employee ID already exists
    Employee emp;
    int id_exists = 0;
    
    for (int i = 0; i < num_employees; i++) {
        fread(&emp, sizeof(Employee), 1, file);
        if (emp.id == new_emp.id) {
            id_exists = 1;
            break;
        }
    }
    
    if (id_exists) {
        printf("Employee with ID %d already exists\n", new_emp.id);
        fclose(file);
        return;
    }
    
    // Go to end of file
    fseek(file, 0, SEEK_END);
    
    // Write new employee
    fwrite(&new_emp, sizeof(Employee), 1, file);
    
    // Update the count at the beginning
    num_employees++;
    fseek(file, 0, SEEK_SET);
    fwrite(&num_employees, sizeof(int), 1, file);
    
    fclose(file);
    printf("Successfully added employee %s\n", new_emp.name);
}

int main() {
    const char* filename = "employees.dat";
    
    // Create and write binary file
    writeBinaryFile(filename);
    
    // Read and display binary file
    readBinaryFile(filename);
    
    // Search for specific employee
    printf("\nSearching for employee with ID 3:\n");
    findEmployeeById(filename, 3);
    
    // Add new employee
    printf("\nAdding new employee:\n");
    Employee new_emp = {5, "Charlie Wilson", 70000.0};
    appendEmployee(filename, new_emp);
    
    // Display updated records
    printf("\nUpdated Employee Records:\n");
    readBinaryFile(filename);
    
    return 0;
}
