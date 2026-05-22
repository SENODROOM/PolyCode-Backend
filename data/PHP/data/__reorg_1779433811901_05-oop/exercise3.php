<?php
    // Exercise 3: Employee Management System
    
    echo "<h2>Employee Management System with OOP</h2>";
    
    // Abstract base class for all employees
    abstract class Employee {
        protected int $id;
        protected string $firstName;
        protected string $lastName;
        protected string $email;
        protected DateTime $hireDate;
        protected float $baseSalary;
        protected static int $nextId = 1000;
        protected static array $allEmployees = [];
        
        public function __construct(string $firstName, string $lastName, string $email, float $baseSalary) {
            $this->id = self::$nextId++;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->email = $email;
            $this->baseSalary = $baseSalary;
            $this->hireDate = new DateTime();
            
            self::$allEmployees[$this->id] = $this;
        }
        
        // Getters
        public function getId(): int {
            return $this->id;
        }
        
        public function getFirstName(): string {
            return $this->firstName;
        }
        
        public function getLastName(): string {
            return $this->lastName;
        }
        
        public function getFullName(): string {
            return $this->firstName . " " . $this->lastName;
        }
        
        public function getEmail(): string {
            return $this->email;
        }
        
        public function getHireDate(): DateTime {
            return $this->hireDate;
        }
        
        public function getBaseSalary(): float {
            return $this->baseSalary;
        }
        
        // Setters
        public function setEmail(string $email): void {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->email = $email;
            } else {
                throw new InvalidArgumentException("Invalid email format");
            }
        }
        
        public function setBaseSalary(float $salary): void {
            if ($salary >= 0) {
                $this->baseSalary = $salary;
            } else {
                throw new InvalidArgumentException("Salary cannot be negative");
            }
        }
        
        // Abstract methods
        abstract public function getJobTitle(): string;
        abstract public function calculateMonthlySalary(): float;
        abstract public function getBenefits(): array;
        
        // Concrete methods
        public function getYearsOfService(): int {
            $today = new DateTime();
            return $today->diff($this->hireDate)->y;
        }
        
        public function getEmployeeInfo(): string {
            return "ID: {$this->id}, Name: {$this->getFullName()}, Email: {$this->email}, " .
                   "Title: {$this->getJobTitle()}, Years of Service: {$this->getYearsOfService()}";
        }
        
        public function giveRaise(float $percentage): void {
            if ($percentage > 0 && $percentage <= 50) {
                $this->baseSalary *= (1 + $percentage / 100);
                echo "{$this->getFullName()} received a {$percentage}% raise. New salary: $" . 
                     number_format($this->baseSalary, 2) . "<br>";
            } else {
                echo "Invalid raise percentage<br>";
            }
        }
        
        // Static methods
        public static function getTotalEmployees(): int {
            return count(self::$allEmployees);
        }
        
        public static function getEmployeeById(int $id): ?Employee {
            return self::$allEmployees[$id] ?? null;
        }
        
        public static function getAllEmployees(): array {
            return self::$allEmployees;
        }
        
        public static function getTotalPayroll(): float {
            $total = 0;
            foreach (self::$allEmployees as $employee) {
                $total += $employee->calculateMonthlySalary();
            }
            return $total;
        }
        
        public static function getAverageSalary(): float {
            $count = self::getTotalEmployees();
            return $count > 0 ? self::getTotalPayroll() / $count : 0;
        }
        
        public static function findEmployeesByName(string $name): array {
            $results = [];
            foreach (self::$allEmployees as $employee) {
                if (stripos($employee->getFullName(), $name) !== false) {
                    $results[] = $employee;
                }
            }
            return $results;
        }
    }
    
    // Full-time Employee class
    class FullTimeEmployee extends Employee {
        private string $department;
        private array $benefits;
        private float $bonusPercentage;
        
        public function __construct(string $firstName, string $lastName, string $email, 
                                  float $baseSalary, string $department, float $bonusPercentage = 0.10) {
            parent::__construct($firstName, $lastName, $email, $baseSalary);
            $this->department = $department;
            $this->bonusPercentage = $bonusPercentage;
            $this->benefits = [
                'Health Insurance',
                'Dental Insurance',
                '401(k) Matching',
                'Paid Time Off',
                'Stock Options'
            ];
        }
        
        public function getDepartment(): string {
            return $this->department;
        }
        
        public function getBonusPercentage(): float {
            return $this->bonusPercentage;
        }
        
        public function setBonusPercentage(float $percentage): void {
            if ($percentage >= 0 && $percentage <= 1) {
                $this->bonusPercentage = $percentage;
            }
        }
        
        public function getJobTitle(): string {
            return "Full-time {$this->department} Employee";
        }
        
        public function calculateMonthlySalary(): float {
            $monthlyBase = $this->baseSalary / 12;
            $monthlyBonus = $monthlyBase * $this->bonusPercentage;
            return $monthlyBase + $monthlyBonus;
        }
        
        public function getBenefits(): array {
            return $this->benefits;
        }
        
        public function addBenefit(string $benefit): void {
            if (!in_array($benefit, $this->benefits)) {
                $this->benefits[] = $benefit;
            }
        }
        
        public function removeBenefit(string $benefit): void {
            $key = array_search($benefit, $this->benefits);
            if ($key !== false) {
                unset($this->benefits[$key]);
                $this->benefits = array_values($this->benefits);
            }
        }
        
        public function getAnnualCompensation(): float {
            return $this->baseSalary + ($this->baseSalary * $this->bonusPercentage);
        }
    }
    
    // Part-time Employee class
    class PartTimeEmployee extends Employee {
        private int $hoursPerWeek;
        private float $hourlyRate;
        private bool $eligibleForBenefits;
        
        public function __construct(string $firstName, string $lastName, string $email, 
                                  float $hourlyRate, int $hoursPerWeek = 20, bool $eligibleForBenefits = false) {
            // Calculate annual salary from hourly rate
            $annualSalary = $hourlyRate * $hoursPerWeek * 52;
            parent::__construct($firstName, $lastName, $email, $annualSalary);
            $this->hourlyRate = $hourlyRate;
            $this->hoursPerWeek = $hoursPerWeek;
            $this->eligibleForBenefits = $eligibleForBenefits;
        }
        
        public function getHoursPerWeek(): int {
            return $this->hoursPerWeek;
        }
        
        public function getHourlyRate(): float {
            return $this->hourlyRate;
        }
        
        public function setHoursPerWeek(int $hours): void {
            if ($hours >= 1 && $hours <= 40) {
                $this->hoursPerWeek = $hours;
                // Recalculate base salary
                $this->baseSalary = $this->hourlyRate * $hours * 52;
            }
        }
        
        public function setHourlyRate(float $rate): void {
            if ($rate >= 0) {
                $this->hourlyRate = $rate;
                $this->baseSalary = $rate * $this->hoursPerWeek * 52;
            }
        }
        
        public function getJobTitle(): string {
            return "Part-time Employee ({$this->hoursPerWeek} hrs/week)";
        }
        
        public function calculateMonthlySalary(): float {
            return $this->hourlyRate * $this->hoursPerWeek * 4.33; // 4.33 weeks per month
        }
        
        public function getBenefits(): array {
            if ($this->eligibleForBenefits) {
                return ['Limited Health Insurance', '401(k) (no matching)'];
            }
            return [];
        }
        
        public function setBenefitsEligibility(bool $eligible): void {
            $this->eligibleForBenefits = $eligible;
        }
        
        public function getWeeklyPay(): float {
            return $this->hourlyRate * $this->hoursPerWeek;
        }
    }
    
    // Manager class (inherits from FullTimeEmployee)
    class Manager extends FullTimeEmployee {
        private array $managedEmployees = [];
        private float $managementBonus;
        
        public function __construct(string $firstName, string $lastName, string $email, 
                                  float $baseSalary, string $department, float $managementBonus = 0.20) {
            parent::__construct($firstName, $lastName, $email, $baseSalary, $department, 0.15);
            $this->managementBonus = $managementBonus;
            $this->addBenefit('Executive Health Plan');
            $this->addBenefit('Company Car');
        }
        
        public function getJobTitle(): string {
            return "Manager, {$this->department}";
        }
        
        public function calculateMonthlySalary(): float {
            $baseMonthly = parent::calculateMonthlySalary();
            $managementBonus = $baseMonthly * $this->managementBonus;
            return $baseMonthly + $managementBonus;
        }
        
        public function getBenefits(): array {
            $parentBenefits = parent::getBenefits();
            $parentBenefits[] = 'Executive Health Plan';
            $parentBenefits[] = 'Company Car';
            $parentBenefits[] = 'Performance Bonus';
            return $parentBenefits;
        }
        
        public function addManagedEmployee(Employee $employee): void {
            $this->managedEmployees[] = $employee;
        }
        
        public function getManagedEmployees(): array {
            return $this->managedEmployees;
        }
        
        public function getTeamSize(): int {
            return count($this->managedEmployees);
        }
        
        public function getTeamTotalSalary(): float {
            $total = 0;
            foreach ($this->managedEmployees as $employee) {
                $total += $employee->calculateMonthlySalary();
            }
            return $total;
        }
        
        public function getManagementBonus(): float {
            return $this->managementBonus;
        }
        
        public function setManagementBonus(float $bonus): void {
            if ($bonus >= 0 && $bonus <= 1) {
                $this->managementBonus = $bonus;
            }
        }
    }
    
    // Contractor class
    class Contractor extends Employee {
        private DateTime $contractEndDate;
        private string $company;
        private float $hourlyRate;
        private int $contractHours;
        
        public function __construct(string $firstName, string $lastName, string $email, 
                                  float $hourlyRate, int $contractHours, string $company, 
                                  DateTime $contractEndDate) {
            $contractValue = $hourlyRate * $contractHours;
            parent::__construct($firstName, $lastName, $email, $contractValue);
            $this->hourlyRate = $hourlyRate;
            $this->contractHours = $contractHours;
            $this->company = $company;
            $this->contractEndDate = $contractEndDate;
        }
        
        public function getCompany(): string {
            return $this->company;
        }
        
        public function getContractEndDate(): DateTime {
            return $this->contractEndDate;
        }
        
        public function getHourlyRate(): float {
            return $this->hourlyRate;
        }
        
        public function getContractHours(): int {
            return $this->contractHours;
        }
        
        public function getJobTitle(): string {
            return "Contractor from {$this->company}";
        }
        
        public function calculateMonthlySalary(): float {
            // Contractors are paid based on contract completion
            $monthsRemaining = $this->contractEndDate->diff(new DateTime())->m + 1;
            return $this->baseSalary / max($monthsRemaining, 1);
        }
        
        public function getBenefits(): array {
            return [];  // Contractors typically don't get benefits
        }
        
        public function isContractExpired(): bool {
            return new DateTime() > $this->contractEndDate;
        }
        
        public function getDaysUntilExpiration(): int {
            return (new DateTime())->diff($this->contractEndDate)->days;
        }
    }
    
    // Employee Management System
    class EmployeeManagementSystem {
        public function createFullTimeEmployee(string $firstName, string $lastName, string $email, 
                                             float $baseSalary, string $department): FullTimeEmployee {
            return new FullTimeEmployee($firstName, $lastName, $email, $baseSalary, $department);
        }
        
        public function createPartTimeEmployee(string $firstName, string $lastName, string $email, 
                                             float $hourlyRate, int $hoursPerWeek): PartTimeEmployee {
            return new PartTimeEmployee($firstName, $lastName, $email, $hourlyRate, $hoursPerWeek);
        }
        
        public function createManager(string $firstName, string $lastName, string $email, 
                                    float $baseSalary, string $department): Manager {
            return new Manager($firstName, $lastName, $email, $baseSalary, $department);
        }
        
        public function createContractor(string $firstName, string $lastName, string $email, 
                                        float $hourlyRate, int $contractHours, string $company, 
                                        DateTime $endDate): Contractor {
            return new Contractor($firstName, $lastName, $email, $hourlyRate, $contractHours, $company, $endDate);
        }
        
        public function getDepartmentSummary(): array {
            $departments = [];
            $allEmployees = Employee::getAllEmployees();
            
            foreach ($allEmployees as $employee) {
                if ($employee instanceof FullTimeEmployee) {
                    $dept = $employee->getDepartment();
                    if (!isset($departments[$dept])) {
                        $departments[$dept] = [
                            'employees' => [],
                            'total_salary' => 0,
                            'count' => 0
                        ];
                    }
                    $departments[$dept]['employees'][] = $employee;
                    $departments[$dept]['total_salary'] += $employee->calculateMonthlySalary();
                    $departments[$dept]['count']++;
                }
            }
            
            return $departments;
        }
        
        public function getSalaryReport(): array {
            $allEmployees = Employee::getAllEmployees();
            $salaries = [];
            
            foreach ($allEmployees as $employee) {
                $salaries[] = [
                    'name' => $employee->getFullName(),
                    'title' => $employee->getJobTitle(),
                    'monthly_salary' => $employee->calculateMonthlySalary(),
                    'annual_salary' => $employee->getBaseSalary()
                ];
            }
            
            // Sort by monthly salary (descending)
            usort($salaries, function($a, $b) {
                return $b['monthly_salary'] <=> $a['monthly_salary'];
            });
            
            return $salaries;
        }
        
        public function getEmployeeTypeBreakdown(): array {
            $allEmployees = Employee::getAllEmployees();
            $breakdown = [];
            
            foreach ($allEmployees as $employee) {
                $type = get_class($employee);
                if (!isset($breakdown[$type])) {
                    $breakdown[$type] = [
                        'count' => 0,
                        'total_salary' => 0,
                        'employees' => []
                    ];
                }
                $breakdown[$type]['count']++;
                $breakdown[$type]['total_salary'] += $employee->calculateMonthlySalary();
                $breakdown[$type]['employees'][] = $employee;
            }
            
            return $breakdown;
        }
    }
    
    // Demonstration
    echo "<h3>Creating Employees:</h3>";
    
    $ems = new EmployeeManagementSystem();
    
    // Create different types of employees
    $ft1 = $ems->createFullTimeEmployee("John", "Doe", "john@company.com", 75000, "Engineering");
    $ft2 = $ems->createFullTimeEmployee("Jane", "Smith", "jane@company.com", 65000, "Marketing");
    $pt1 = $ems->createPartTimeEmployee("Bob", "Johnson", "bob@company.com", 25.0, 20);
    $pt2 = $ems->createPartTimeEmployee("Alice", "Brown", "alice@company.com", 30.0, 15);
    $manager1 = $ems->createManager("Carol", "White", "carol@company.com", 95000, "Engineering");
    $contractor1 = $ems->createContractor("David", "Green", "david@contractor.com", 50.0, 160, 
                                         "TechSolutions", new DateTime('2024-12-31'));
    
    // Assign employees to manager
    $manager1->addManagedEmployee($ft1);
    $manager1->addManagedEmployee($pt1);
    
    echo "<h3>Employee Information:</h3>";
    $allEmployees = Employee::getAllEmployees();
    foreach ($allEmployees as $employee) {
        echo $employee->getEmployeeInfo() . "<br>";
        echo "Monthly Salary: $" . number_format($employee->calculateMonthlySalary(), 2) . "<br>";
        echo "Benefits: " . implode(", ", $employee->getBenefits()) . "<br><br>";
    }
    
    echo "<h3>Manager Information:</h3>";
    echo $manager1->getEmployeeInfo() . "<br>";
    echo "Team Size: " . $manager1->getTeamSize() . "<br>";
    echo "Team Total Monthly Salary: $" . number_format($manager1->getTeamTotalSalary(), 2) . "<br>";
    echo "Management Bonus: " . ($manager1->getManagementBonus() * 100) . "%<br><br>";
    
    echo "<h3>Salary Report (Highest Paid First):</h3>";
    $salaryReport = $ems->getSalaryReport();
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Name</th><th>Title</th><th>Monthly Salary</th><th>Annual Salary</th></tr>";
    foreach ($salaryReport as $employee) {
        echo "<tr>";
        echo "<td>{$employee['name']}</td>";
        echo "<td>{$employee['title']}</td>";
        echo "<td>$" . number_format($employee['monthly_salary'], 2) . "</td>";
        echo "<td>$" . number_format($employee['annual_salary'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Department Summary:</h3>";
    $deptSummary = $ems->getDepartmentSummary();
    foreach ($deptSummary as $dept => $data) {
        echo "<strong>$dept Department:</strong><br>";
        echo "Employees: {$data['count']}<br>";
        echo "Total Monthly Payroll: $" . number_format($data['total_salary'], 2) . "<br>";
        echo "Average Monthly Salary: $" . number_format($data['total_salary'] / $data['count'], 2) . "<br><br>";
    }
    
    echo "<h3>Employee Type Breakdown:</h3>";
    $breakdown = $ems->getEmployeeTypeBreakdown();
    foreach ($breakdown as $type => $data) {
        $typeName = substr($type, strrpos($type, '\\') + 1);
        echo "<strong>$typeName:</strong><br>";
        echo "Count: {$data['count']}<br>";
        echo "Total Monthly Payroll: $" . number_format($data['total_salary'], 2) . "<br>";
        echo "Average Monthly Salary: $" . number_format($data['total_salary'] / $data['count'], 2) . "<br><br>";
    }
    
    echo "<h3>Company Statistics:</h3>";
    echo "Total Employees: " . Employee::getTotalEmployees() . "<br>";
    echo "Total Monthly Payroll: $" . number_format(Employee::getTotalPayroll(), 2) . "<br>";
    echo "Average Monthly Salary: $" . number_format(Employee::getAverageSalary(), 2) . "<br>";
    echo "Total Annual Payroll: $" . number_format(Employee::getTotalPayroll() * 12, 2) . "<br><br>";
    
    echo "<h3>Testing Raises:</h3>";
    $ft1->giveRaise(10);
    $pt1->giveRaise(5);
    $manager1->giveRaise(15);
    
    echo "<h3>Updated Salary Report:</h3>";
    echo "John Doe (Full-time): $" . number_format($ft1->calculateMonthlySalary(), 2) . "/month<br>";
    echo "Bob Johnson (Part-time): $" . number_format($pt1->calculateMonthlySalary(), 2) . "/month<br>";
    echo "Carol White (Manager): $" . number_format($manager1->calculateMonthlySalary(), 2) . "/month<br><br>";
    
    echo "<h3>Contractor Information:</h3>";
    echo $contractor1->getEmployeeInfo() . "<br>";
    echo "Hourly Rate: $" . number_format($contractor1->getHourlyRate(), 2) . "<br>";
    echo "Contract Hours: " . $contractor1->getContractHours() . "<br>";
    echo "Contract End Date: " . $contractor1->getContractEndDate()->format('Y-m-d') . "<br>";
    echo "Days Until Expiration: " . $contractor1->getDaysUntilExpiration() . "<br>";
    echo "Contract Expired: " . ($contractor1->isContractExpired() ? "Yes" : "No") . "<br><br>";
    
    echo "<h3>Search Functionality:</h3>";
    $searchResults = Employee::findEmployeesByName("john");
    echo "Employees with 'john' in name:<br>";
    foreach ($searchResults as $employee) {
        echo "- " . $employee->getFullName() . " (" . $employee->getJobTitle() . ")<br>";
    }
    
    echo "<br><h3>Static Members Demonstration:</h3>";
    echo "Next Employee ID: " . Employee::$nextId . "<br>";
    echo "Total Employees Created: " . Employee::getTotalEmployees() . "<br>";
    
    // Show all employee IDs
    echo "All Employee IDs: ";
    foreach (Employee::getAllEmployees() as $employee) {
        echo $employee->getId() . " ";
    }
    echo "<br>";
?>
