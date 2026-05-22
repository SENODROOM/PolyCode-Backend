<?php
    // Exercise 1: Bank Account System
    
    echo "<h2>Bank Account System with OOP</h2>";
    
    // Abstract base class for all bank accounts
    abstract class BankAccount {
        protected string $accountNumber;
        protected string $accountHolder;
        protected float $balance;
        protected string $accountType;
        protected array $transactionHistory = [];
        
        public function __construct(string $accountNumber, string $accountHolder, float $initialBalance = 0.0) {
            $this->accountNumber = $accountNumber;
            $this->accountHolder = $accountHolder;
            $this->balance = $initialBalance;
            $this->recordTransaction("Account opened", $initialBalance);
        }
        
        // Getters
        public function getAccountNumber(): string {
            return $this->accountNumber;
        }
        
        public function getAccountHolder(): string {
            return $this->accountHolder;
        }
        
        public function getBalance(): float {
            return $this->balance;
        }
        
        public function getAccountType(): string {
            return $this->accountType;
        }
        
        // Common methods
        public function deposit(float $amount): bool {
            if ($amount > 0) {
                $this->balance += $amount;
                $this->recordTransaction("Deposit", $amount);
                return true;
            }
            return false;
        }
        
        public function withdraw(float $amount): bool {
            if ($amount > 0 && $this->canWithdraw($amount)) {
                $this->balance -= $amount;
                $this->recordTransaction("Withdrawal", -$amount);
                return true;
            }
            return false;
        }
        
        protected function recordTransaction(string $type, float $amount): void {
            $this->transactionHistory[] = [
                'date' => date('Y-m-d H:i:s'),
                'type' => $type,
                'amount' => $amount,
                'balance' => $this->balance
            ];
        }
        
        public function getTransactionHistory(): array {
            return $this->transactionHistory;
        }
        
        public function displayTransactionHistory(): void {
            echo "<h3>Transaction History for Account {$this->accountNumber}</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Date</th><th>Type</th><th>Amount</th><th>Balance</th></tr>";
            
            foreach ($this->transactionHistory as $transaction) {
                echo "<tr>";
                echo "<td>{$transaction['date']}</td>";
                echo "<td>{$transaction['type']}</td>";
                echo "<td>$" . number_format(abs($transaction['amount']), 2) . "</td>";
                echo "<td>$" . number_format($transaction['balance'], 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Abstract methods to be implemented by subclasses
        abstract protected function canWithdraw(float $amount): bool;
        abstract public function applyInterest(): void;
        abstract public function getAccountInfo(): string;
    }
    
    // Savings Account class
    class SavingsAccount extends BankAccount {
        private float $interestRate;
        private int $withdrawalLimit;
        private int $withdrawalCount;
        
        public function __construct(string $accountNumber, string $accountHolder, float $initialBalance = 0.0, float $interestRate = 0.02) {
            parent::__construct($accountNumber, $accountHolder, $initialBalance);
            $this->accountType = "Savings";
            $this->interestRate = $interestRate;
            $this->withdrawalLimit = 6;  // 6 withdrawals per month
            $this->withdrawalCount = 0;
        }
        
        protected function canWithdraw(float $amount): bool {
            if ($this->withdrawalCount >= $this->withdrawalLimit) {
                echo "Withdrawal limit exceeded for this month.<br>";
                return false;
            }
            if ($amount > $this->balance) {
                echo "Insufficient funds.<br>";
                return false;
            }
            return true;
        }
        
        public function withdraw(float $amount): bool {
            if (parent::withdraw($amount)) {
                $this->withdrawalCount++;
                return true;
            }
            return false;
        }
        
        public function applyInterest(): void {
            $interest = $this->balance * $this->interestRate;
            $this->balance += $interest;
            $this->recordTransaction("Interest Applied", $interest);
            echo "Interest of $" . number_format($interest, 2) . " applied to account {$this->accountNumber}<br>";
        }
        
        public function getAccountInfo(): string {
            return "Savings Account #{$this->accountNumber} - {$this->accountHolder} - Balance: $" . 
                   number_format($this->balance, 2) . " - Rate: " . ($this->interestRate * 100) . "%";
        }
        
        public function getWithdrawalCount(): int {
            return $this->withdrawalCount;
        }
        
        public function resetWithdrawalCount(): void {
            $this->withdrawalCount = 0;
        }
    }
    
    // Checking Account class
    class CheckingAccount extends BankAccount {
        private float $overdraftLimit;
        private float $monthlyFee;
        private bool $hasOverdraftProtection;
        
        public function __construct(string $accountNumber, string $accountHolder, float $initialBalance = 0.0, float $overdraftLimit = 500.0, bool $hasOverdraftProtection = true) {
            parent::__construct($accountNumber, $accountHolder, $initialBalance);
            $this->accountType = "Checking";
            $this->overdraftLimit = $overdraftLimit;
            $this->monthlyFee = 10.0;
            $this->hasOverdraftProtection = $hasOverdraftProtection;
        }
        
        protected function canWithdraw(float $amount): bool {
            if ($this->hasOverdraftProtection) {
                return ($this->balance + $this->overdraftLimit) >= $amount;
            } else {
                return $this->balance >= $amount;
            }
        }
        
        public function applyInterest(): void {
            // Checking accounts typically don't earn interest
            echo "Checking accounts do not earn interest.<br>";
        }
        
        public function getAccountInfo(): string {
            $overdraftInfo = $this->hasOverdraftProtection ? "Yes ($" . number_format($this->overdraftLimit, 2) . ")" : "No";
            return "Checking Account #{$this->accountNumber} - {$this->accountHolder} - Balance: $" . 
                   number_format($this->balance, 2) . " - Overdraft: $overdraftInfo";
        }
        
        public function applyMonthlyFee(): void {
            if ($this->balance >= $this->monthlyFee) {
                $this->balance -= $this->monthlyFee;
                $this->recordTransaction("Monthly Fee", -$this->monthlyFee);
                echo "Monthly fee of $" . number_format($this->monthlyFee, 2) . " applied<br>";
            } else {
                echo "Insufficient balance for monthly fee<br>";
            }
        }
        
        public function setOverdraftProtection(bool $enabled, float $limit = 500.0): void {
            $this->hasOverdraftProtection = $enabled;
            $this->overdraftLimit = $limit;
        }
    }
    
    // Bank class to manage multiple accounts
    class Bank {
        private static array $accounts = [];
        private static string $bankName = "PHP Bank";
        
        public static function createSavingsAccount(string $accountHolder, float $initialBalance = 0.0, float $interestRate = 0.02): SavingsAccount {
            $accountNumber = "SAV" . str_pad(count(self::$accounts) + 1, 4, "0", STR_PAD_LEFT);
            $account = new SavingsAccount($accountNumber, $accountHolder, $initialBalance, $interestRate);
            self::$accounts[$accountNumber] = $account;
            return $account;
        }
        
        public static function createCheckingAccount(string $accountHolder, float $initialBalance = 0.0, float $overdraftLimit = 500.0): CheckingAccount {
            $accountNumber = "CHK" . str_pad(count(self::$accounts) + 1, 4, "0", STR_PAD_LEFT);
            $account = new CheckingAccount($accountNumber, $accountHolder, $initialBalance, $overdraftLimit);
            self::$accounts[$accountNumber] = $account;
            return $account;
        }
        
        public static function getAccount(string $accountNumber): ?BankAccount {
            return self::$accounts[$accountNumber] ?? null;
        }
        
        public static function getAllAccounts(): array {
            return self::$accounts;
        }
        
        public static function displayAllAccounts(): void {
            echo "<h3>" . self::$bankName . " - All Accounts</h3>";
            foreach (self::$accounts as $account) {
                echo $account->getAccountInfo() . "<br>";
            }
        }
        
        public static function getTotalDeposits(): float {
            $total = 0;
            foreach (self::$accounts as $account) {
                $total += $account->getBalance();
            }
            return $total;
        }
        
        public static function getBankSummary(): string {
            return self::$bankName . " - Total Accounts: " . count(self::$accounts) . 
                   " - Total Deposits: $" . number_format(self::getTotalDeposits(), 2);
        }
    }
    
    // Demonstrate the banking system
    echo "<h3>Creating Accounts:</h3>";
    
    $savings1 = Bank::createSavingsAccount("John Doe", 1000.0, 0.03);
    $savings2 = Bank::createSavingsAccount("Jane Smith", 500.0);
    $checking1 = Bank::createCheckingAccount("Bob Johnson", 2000.0, 1000.0);
    $checking2 = Bank::createCheckingAccount("Alice Brown", 0.0, 0.0, false);
    
    Bank::displayAllAccounts();
    echo "<br>";
    
    echo "<h3>Account Transactions:</h3>";
    
    // Savings account transactions
    echo "<strong>John's Savings Account:</strong><br>";
    echo $savings1->getAccountInfo() . "<br>";
    $savings1->deposit(500.0);
    $savings1->withdraw(200.0);
    $savings1->withdraw(100.0);
    echo "Withdrawals this month: " . $savings1->getWithdrawalCount() . "<br>";
    $savings1->applyInterest();
    echo "Current balance: $" . number_format($savings1->getBalance(), 2) . "<br><br>";
    
    // Checking account transactions
    echo "<strong>Bob's Checking Account:</strong><br>";
    echo $checking1->getAccountInfo() . "<br>";
    $checking1->withdraw(2500.0);  // Should work with overdraft
    $checking1->withdraw(500.0);
    $checking1->applyMonthlyFee();
    echo "Current balance: $" . number_format($checking1->getBalance(), 2) . "<br><br>";
    
    // Test withdrawal limits
    echo "<strong>Testing Withdrawal Limits:</strong><br>";
    $savings2->withdraw(100.0);
    $savings2->withdraw(100.0);
    $savings2->withdraw(100.0);
    $savings2->withdraw(100.0);
    $savings2->withdraw(100.0);
    $savings2->withdraw(100.0);  // Should work (6th withdrawal)
    $savings2->withdraw(100.0);  // Should fail (limit exceeded)
    
    echo "<br>";
    
    // Test no overdraft protection
    echo "<strong>Testing No Overdraft Protection:</strong><br>";
    echo $checking2->getAccountInfo() . "<br>";
    $checking2->withdraw(100.0);  // Should fail - no overdraft protection
    $checking2->deposit(50.0);
    $checking2->withdraw(30.0);    // Should work
    echo "Current balance: $" . number_format($checking2->getBalance(), 2) . "<br><br>";
    
    // Display transaction history for one account
    $savings1->displayTransactionHistory();
    
    // Bank summary
    echo "<h3>Bank Summary:</h3>";
    echo Bank::getBankSummary() . "<br>";
    
    // Polymorphism demonstration
    echo "<h3>Polymorphism Demonstration:</h3>";
    $accounts = Bank::getAllAccounts();
    
    echo "Applying monthly operations to all accounts:<br>";
    foreach ($accounts as $account) {
        echo $account->getAccountInfo() . "<br>";
        $account->applyInterest();  // Different behavior for each account type
        if ($account instanceof CheckingAccount) {
            $account->applyMonthlyFee();
        }
    }
    
    echo "<br><strong>Final Bank Status:</strong><br>";
    Bank::displayAllAccounts();
    echo "<br>" . Bank::getBankSummary();
?>
