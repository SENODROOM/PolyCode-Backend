/*
 * File: coding_standards.c
 * Description: Example demonstrating C coding best practices
 * Author: PolyCode
 * Date: 2026-03-23
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

// Constants - Use uppercase for constants
#define MAX_NAME_LENGTH 50
#define MAX_STUDENTS 100
#define MIN_AGE 0
#define MAX_AGE 120

// Type definitions - Use descriptive names
typedef struct {
    int id;
    char name[MAX_NAME_LENGTH];
    int age;
    double gpa;
} Student;

// Function prototypes - Include in header files for larger projects
void initializeStudent(Student* student, int id, const char* name, int age, double gpa);
void printStudent(const Student* student);
int validateStudent(const Student* student);
double calculateAverageGPA(const Student* students, int count);

// Function implementations
void initializeStudent(Student* student, int id, const char* name, int age, double gpa) {
    // Input validation
    if (student == NULL || name == NULL) {
        fprintf(stderr, "Error: NULL pointer passed to initializeStudent\n");
        return;
    }
    
    if (age < MIN_AGE || age > MAX_AGE) {
        fprintf(stderr, "Error: Invalid age %d for student\n", age);
        return;
    }
    
    if (gpa < 0.0 || gpa > 4.0) {
        fprintf(stderr, "Error: Invalid GPA %.2f for student\n", gpa);
        return;
    }
    
    // Initialize struct fields
    student->id = id;
    strncpy(student->name, name, MAX_NAME_LENGTH - 1);
    student->name[MAX_NAME_LENGTH - 1] = '\0'; // Ensure null termination
    student->age = age;
    student->gpa = gpa;
}

void printStudent(const Student* student) {
    if (student == NULL) {
        fprintf(stderr, "Error: NULL pointer passed to printStudent\n");
        return;
    }
    
    printf("Student ID: %d\n", student->id);
    printf("Name: %s\n", student->name);
    printf("Age: %d\n", student->age);
    printf("GPA: %.2f\n", student->gpa);
    printf("-------------------\n");
}

int validateStudent(const Student* student) {
    if (student == NULL) {
        return 0; // Invalid
    }
    
    if (student->age < MIN_AGE || student->age > MAX_AGE) {
        return 0; // Invalid age
    }
    
    if (student->gpa < 0.0 || student->gpa > 4.0) {
        return 0; // Invalid GPA
    }
    
    if (strlen(student->name) == 0) {
        return 0; // Empty name
    }
    
    return 1; // Valid
}

double calculateAverageGPA(const Student* students, int count) {
    if (students == NULL || count <= 0) {
        return 0.0;
    }
    
    double total = 0.0;
    int valid_students = 0;
    
    for (int i = 0; i < count; i++) {
        if (validateStudent(&students[i])) {
            total += students[i].gpa;
            valid_students++;
        }
    }
    
    return valid_students > 0 ? total / valid_students : 0.0;
}

int main() {
    // Array initialization
    Student students[MAX_STUDENTS];
    int student_count = 3;
    
    // Initialize students with validation
    initializeStudent(&students[0], 1, "Alice Johnson", 20, 3.8);
    initializeStudent(&students[1], 2, "Bob Smith", 21, 3.5);
    initializeStudent(&students[2], 3, "Charlie Brown", 19, 3.9);
    
    // Print student information
    printf("Student Information:\n");
    printf("===================\n");
    
    for (int i = 0; i < student_count; i++) {
        if (validateStudent(&students[i])) {
            printStudent(&students[i]);
        } else {
            printf("Invalid student data at index %d\n", i);
        }
    }
    
    // Calculate and display average GPA
    double avg_gpa = calculateAverageGPA(students, student_count);
    printf("Average GPA: %.2f\n", avg_gpa);
    
    return EXIT_SUCCESS; // Use EXIT_SUCCESS instead of 0
}
