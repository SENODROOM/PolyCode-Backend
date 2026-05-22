// ============================================================
//  Project 02: Bank System
//
//  Features:
//  - Create accounts (Checking, Savings, Investment)
//  - Deposit, withdraw, transfer
//  - Transaction history
//  - Interest calculation
//  - Account statements
//  - Custom error types
//
//  Run: cargo run
// ============================================================

use std::collections::HashMap;
use std::fmt;

// ─────────────────────────────────────────────
// ERROR TYPES
// ─────────────────────────────────────────────

#[derive(Debug, PartialEq)]
enum BankError {
    AccountNotFound(String),
    InsufficientFunds { available: f64, requested: f64 },
    InvalidAmount(String),
    SameAccount,
    AccountFrozen(String),
}

impl fmt::Display for BankError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            BankError::AccountNotFound(id)          => write!(f, "Account '{}' not found", id),
            BankError::InsufficientFunds { available, requested }
                => write!(f, "Insufficient funds: requested ${:.2}, available ${:.2}", requested, available),
            BankError::InvalidAmount(msg)           => write!(f, "Invalid amount: {}", msg),
            BankError::SameAccount                  => write!(f, "Cannot transfer to same account"),
            BankError::AccountFrozen(id)            => write!(f, "Account '{}' is frozen", id),
        }
    }
}

type BankResult<T> = Result<T, BankError>;

// ─────────────────────────────────────────────
// DOMAIN TYPES
// ─────────────────────────────────────────────

#[derive(Debug, Clone, PartialEq)]
enum AccountType { Checking, Savings, Investment }

impl fmt::Display for AccountType {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result { write!(f, "{:?}", self) }
}

#[derive(Debug, Clone)]
enum TransactionKind {
    Deposit,
    Withdrawal,
    Transfer { to: String },
    Received { from: String },
    Interest,
}

impl fmt::Display for TransactionKind {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            TransactionKind::Deposit         => write!(f, "Deposit"),
            TransactionKind::Withdrawal      => write!(f, "Withdrawal"),
            TransactionKind::Transfer { to } => write!(f, "Transfer → {}", to),
            TransactionKind::Received { from }=> write!(f, "Received ← {}", from),
            TransactionKind::Interest        => write!(f, "Interest"),
        }
    }
}

#[derive(Debug, Clone)]
struct Transaction {
    kind:    TransactionKind,
    amount:  f64,
    balance: f64,
    note:    String,
}

#[derive(Debug, Clone)]
struct Account {
    id:           String,
    owner:        String,
    account_type: AccountType,
    balance:      f64,
    frozen:       bool,
    transactions: Vec<Transaction>,
}

impl Account {
    fn new(id: String, owner: String, account_type: AccountType, initial_deposit: f64) -> Self {
        let mut acct = Self { id: id.clone(), owner, account_type, balance: 0.0, frozen: false, transactions: vec![] };
        if initial_deposit > 0.0 {
            acct.balance = initial_deposit;
            acct.transactions.push(Transaction {
                kind: TransactionKind::Deposit,
                amount: initial_deposit,
                balance: initial_deposit,
                note: "Initial deposit".into(),
            });
        }
        acct
    }

    fn apply_interest(&mut self, rate_pct: f64) {
        let interest = self.balance * rate_pct / 100.0;
        self.balance += interest;
        self.transactions.push(Transaction {
            kind: TransactionKind::Interest,
            amount: interest,
            balance: self.balance,
            note: format!("{:.2}% annual interest", rate_pct),
        });
    }

    fn interest_rate(&self) -> f64 {
        match self.account_type {
            AccountType::Savings    => 2.5,
            AccountType::Investment => 7.0,
            AccountType::Checking   => 0.1,
        }
    }

    fn print_statement(&self) {
        println!("\n{'Account Statement':-^60}");
        println!("  Account:  {} ({})", self.id, self.account_type);
        println!("  Owner:    {}", self.owner);
        println!("  Balance:  ${:.2}", self.balance);
        if self.frozen { println!("  Status:   ⚠ FROZEN"); }
        println!("  {:-<56}", "");
        println!("  {:<25} {:>10} {:>12}", "Transaction", "Amount", "Balance");
        println!("  {:-<56}", "");
        for t in &self.transactions {
            let sign = match t.kind { TransactionKind::Withdrawal | TransactionKind::Transfer { .. } => "-", _ => "+" };
            println!("  {:<25} {:>+10.2} {:>12.2}", format!("{}", t.kind), t.amount * if sign == "-" { -1.0 } else { 1.0 }, t.balance);
        }
        println!("  {:-<56}", "");
    }
}

// ─────────────────────────────────────────────
// BANK
// ─────────────────────────────────────────────

struct Bank {
    name:     String,
    accounts: HashMap<String, Account>,
}

impl Bank {
    fn new(name: &str) -> Self { Self { name: name.to_string(), accounts: HashMap::new() } }

    fn open_account(&mut self, owner: &str, kind: AccountType, initial: f64) -> BankResult<String> {
        if initial < 0.0 { return Err(BankError::InvalidAmount("Initial deposit cannot be negative".into())); }
        let id = format!("ACC{:04}", self.accounts.len() + 1);
        self.accounts.insert(id.clone(), Account::new(id.clone(), owner.to_string(), kind, initial));
        Ok(id)
    }

    fn get(&self, id: &str) -> BankResult<&Account> {
        self.accounts.get(id).ok_or_else(|| BankError::AccountNotFound(id.to_string()))
    }

    fn get_mut(&mut self, id: &str) -> BankResult<&mut Account> {
        self.accounts.get_mut(id).ok_or_else(|| BankError::AccountNotFound(id.to_string()))
    }

    fn deposit(&mut self, id: &str, amount: f64, note: &str) -> BankResult<f64> {
        if amount <= 0.0 { return Err(BankError::InvalidAmount("Deposit must be positive".into())); }
        let acct = self.get_mut(id)?;
        if acct.frozen { return Err(BankError::AccountFrozen(id.to_string())); }
        acct.balance += amount;
        let balance = acct.balance;
        acct.transactions.push(Transaction { kind: TransactionKind::Deposit, amount, balance, note: note.to_string() });
        Ok(balance)
    }

    fn withdraw(&mut self, id: &str, amount: f64, note: &str) -> BankResult<f64> {
        if amount <= 0.0 { return Err(BankError::InvalidAmount("Withdrawal must be positive".into())); }
        let acct = self.get_mut(id)?;
        if acct.frozen { return Err(BankError::AccountFrozen(id.to_string())); }
        if acct.balance < amount { return Err(BankError::InsufficientFunds { available: acct.balance, requested: amount }); }
        acct.balance -= amount;
        let balance = acct.balance;
        acct.transactions.push(Transaction { kind: TransactionKind::Withdrawal, amount, balance, note: note.to_string() });
        Ok(balance)
    }

    fn transfer(&mut self, from_id: &str, to_id: &str, amount: f64) -> BankResult<()> {
        if from_id == to_id { return Err(BankError::SameAccount); }
        if amount <= 0.0    { return Err(BankError::InvalidAmount("Transfer must be positive".into())); }
        // Validate both exist first
        if !self.accounts.contains_key(from_id) { return Err(BankError::AccountNotFound(from_id.to_string())); }
        if !self.accounts.contains_key(to_id)   { return Err(BankError::AccountNotFound(to_id.to_string())); }

        let from_balance = self.accounts[from_id].balance;
        if from_balance < amount { return Err(BankError::InsufficientFunds { available: from_balance, requested: amount }); }
        if self.accounts[from_id].frozen { return Err(BankError::AccountFrozen(from_id.to_string())); }

        let from = self.accounts.get_mut(from_id).unwrap();
        from.balance -= amount;
        let fb = from.balance;
        from.transactions.push(Transaction { kind: TransactionKind::Transfer { to: to_id.to_string() }, amount, balance: fb, note: String::new() });

        let to = self.accounts.get_mut(to_id).unwrap();
        to.balance += amount;
        let tb = to.balance;
        to.transactions.push(Transaction { kind: TransactionKind::Received { from: from_id.to_string() }, amount, balance: tb, note: String::new() });
        Ok(())
    }

    fn apply_monthly_interest(&mut self) {
        let ids: Vec<String> = self.accounts.keys().cloned().collect();
        for id in ids {
            let rate = self.accounts[&id].interest_rate();
            if rate > 0.0 {
                let monthly_rate = rate / 12.0;
                self.accounts.get_mut(&id).unwrap().apply_interest(monthly_rate);
            }
        }
    }

    fn freeze(&mut self, id: &str) -> BankResult<()> { self.get_mut(id)?.frozen = true; Ok(()) }
    fn unfreeze(&mut self, id: &str) -> BankResult<()> { self.get_mut(id)?.frozen = false; Ok(()) }

    fn total_assets(&self) -> f64 { self.accounts.values().map(|a| a.balance).sum() }

    fn print_summary(&self) {
        println!("\n{'Bank Summary':-^60}");
        println!("  Bank: {}", self.name);
        println!("  Accounts: {}", self.accounts.len());
        println!("  Total Assets: ${:.2}", self.total_assets());
        println!("  {:-<56}", "");
        let mut accounts: Vec<&Account> = self.accounts.values().collect();
        accounts.sort_by(|a, b| a.id.cmp(&b.id));
        for acct in accounts {
            let status = if acct.frozen { " [FROZEN]" } else { "" };
            println!("  {} ({:12}) {:8} ${:>12.2}{}", acct.id, acct.owner, acct.account_type, acct.balance, status);
        }
        println!();
    }
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Project 02: Bank System =====\n");

    let mut bank = Bank::new("PolyCode National Bank");

    // Open accounts
    let alice_check  = bank.open_account("Alice",   AccountType::Checking,   1_000.0).unwrap();
    let alice_saving = bank.open_account("Alice",   AccountType::Savings,    5_000.0).unwrap();
    let bob_check    = bank.open_account("Bob",     AccountType::Checking,   500.0).unwrap();
    let bob_invest   = bank.open_account("Bob",     AccountType::Investment, 10_000.0).unwrap();
    let carol_saving = bank.open_account("Carol",   AccountType::Savings,    2_500.0).unwrap();

    bank.print_summary();

    // Transactions
    println!("--- Transactions ---");
    let bal = bank.deposit(&alice_check, 2_500.0, "Salary").unwrap();
    println!("✅ Alice deposited $2500 → balance ${:.2}", bal);

    let bal = bank.withdraw(&alice_check, 300.0, "Groceries").unwrap();
    println!("✅ Alice withdrew $300 → balance ${:.2}", bal);

    bank.transfer(&alice_check, &bob_check, 500.0).unwrap();
    println!("✅ Alice transferred $500 to Bob");

    // Error handling
    println!("\n--- Error Handling ---");
    let err = bank.withdraw(&bob_check, 999_999.0, "Too much").unwrap_err();
    println!("❌ {}", err);

    let err = bank.transfer(&alice_check, &alice_check, 100.0).unwrap_err();
    println!("❌ {}", err);

    // Freeze test
    bank.freeze(&carol_saving).unwrap();
    let err = bank.deposit(&carol_saving, 100.0, "Attempt").unwrap_err();
    println!("❌ {}", err);
    bank.unfreeze(&carol_saving).unwrap();
    bank.deposit(&carol_saving, 100.0, "After unfreeze").unwrap();
    println!("✅ Carol's account unfrozen and deposit succeeded");

    // Apply interest
    println!("\n--- Monthly Interest ---");
    let before = bank.accounts[&bob_invest].balance;
    bank.apply_monthly_interest();
    let after = bank.accounts[&bob_invest].balance;
    println!("Bob Investment: ${:.2} → ${:.2} (+${:.2})", before, after, after - before);

    // Print statements
    bank.get(&alice_check).unwrap().print_statement();
    bank.print_summary();

    println!("✅ Project 02 complete!");
}

#[cfg(test)]
mod tests {
    use super::*;

    fn test_bank() -> (Bank, String, String) {
        let mut b = Bank::new("Test");
        let a1 = b.open_account("Alice", AccountType::Checking, 1000.0).unwrap();
        let a2 = b.open_account("Bob",   AccountType::Savings,  500.0).unwrap();
        (b, a1, a2)
    }

    #[test] fn deposit_increases_balance() {
        let (mut b, a1, _) = test_bank();
        let bal = b.deposit(&a1, 200.0, "test").unwrap();
        assert!((bal - 1200.0).abs() < 0.01);
    }
    #[test] fn withdraw_decreases_balance() {
        let (mut b, a1, _) = test_bank();
        b.withdraw(&a1, 300.0, "test").unwrap();
        assert!((b.get(&a1).unwrap().balance - 700.0).abs() < 0.01);
    }
    #[test] fn overdraft_rejected() {
        let (mut b, a1, _) = test_bank();
        assert!(matches!(b.withdraw(&a1, 9999.0, ""), Err(BankError::InsufficientFunds { .. })));
    }
    #[test] fn transfer_moves_money() {
        let (mut b, a1, a2) = test_bank();
        b.transfer(&a1, &a2, 300.0).unwrap();
        assert!((b.get(&a1).unwrap().balance - 700.0).abs() < 0.01);
        assert!((b.get(&a2).unwrap().balance - 800.0).abs() < 0.01);
    }
    #[test] fn frozen_account_rejected() {
        let (mut b, a1, _) = test_bank();
        b.freeze(&a1).unwrap();
        assert!(matches!(b.deposit(&a1, 100.0, ""), Err(BankError::AccountFrozen(_))));
    }
}
