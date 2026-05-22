# Rust 🦀

> *"Rust is a language empowering everyone to build reliable and efficient software."*
> — rust-lang.org

---

## Table of Contents

1. [Overview](#overview)
2. [History & Philosophy](#history--philosophy)
3. [Core Concepts](#core-concepts)
4. [Ownership System](#ownership-system)
5. [Borrowing & References](#borrowing--references)
6. [Lifetimes](#lifetimes)
7. [Types & Data Structures](#types--data-structures)
8. [Pattern Matching](#pattern-matching)
9. [Error Handling](#error-handling)
10. [Traits](#traits)
11. [Generics](#generics)
12. [Concurrency](#concurrency)
13. [Cargo & Ecosystem](#cargo--ecosystem)
14. [Performance](#performance)

---

## Overview

Rust is a **systems programming language** that guarantees **memory safety** without a garbage collector, achieving performance comparable to C and C++. It eliminates entire classes of bugs — null pointer dereferences, dangling pointers, data races — at *compile time*.

```
Languages Spectrum
─────────────────────────────────────────────────────────────────
SAFETY ◄───────────────────────────────────────────► PERFORMANCE
│ Python, Ruby, JavaScript  │  Java, C#  │  Rust  │  C, C++  │
│ (GC, safe, slow)          │  (GC, safe)│(no GC!)│(unsafe)  │
                                            ▲
                                     Rust sits here!
                              Safe AND Fast AND no GC
─────────────────────────────────────────────────────────────────
```

**Key Stats:**
- Created: 2006 by Graydon Hoare, sponsored by Mozilla
- Stable 1.0: May 2015
- 9 consecutive years as "most loved language" in Stack Overflow surveys
- Used by: Microsoft, Google, Meta, Linux kernel, AWS, Cloudflare

---

## History & Philosophy

```
Timeline
────────
2006 ──► Graydon Hoare starts Rust as a personal project
2009 ──► Mozilla sponsors development
2012 ──► First typed-checked compiler (rustc)
2015 ──► Rust 1.0 stable released
2016 ──► First year winning Stack Overflow "Most Loved" award
2021 ──► Rust Foundation formed (AWS, Google, Microsoft, Mozilla, Huawei)
2022 ──► Linux kernel 5.20 — first non-C language accepted into kernel
2023 ──► US gov (NSA, CISA) recommends Rust for memory-safe systems
2024 ──► Rust in Android, Windows, Firefox core components
```

**Design Pillars:**

```
Three Core Promises of Rust
─────────────────────────────────────────────────
🔒 Memory Safety    No undefined behavior, no dangling pointers
⚡ Performance      Zero-cost abstractions, no garbage collector  
🔧 Concurrency      Fearless concurrency — data races impossible
─────────────────────────────────────────────────
```

---

## Core Concepts

### Hello, World!

```rust
fn main() {
    println!("Hello, World! 🦀");
    
    // Variables are immutable by default!
    let x = 5;
    // x = 6;  ❌ Compile error: cannot assign twice to immutable variable
    
    let mut y = 5;  // mut = mutable
    y = 6;          // ✅ Fine
    
    // Shadowing — redeclare with same name (even different type!)
    let x = x + 1;     // x is now 6 (new binding)
    let x = x * 2;     // x is now 12
    let x = "twelve";  // x is now a &str — shadowing allows type change
    
    println!("x = {x}, y = {y}");
}
```

### Basic Types

```rust
// Integers
let a: i8  = -128;       // 8-bit signed
let b: u8  = 255;        // 8-bit unsigned
let c: i32 = 2_147_483_647;  // 32-bit signed (default)
let d: u64 = 18_446_744_073_709_551_615u64;
let e: i128 = -170_141_183_460_469_231_731_687_303_715_884_105_728i128;

// Floats
let f: f32 = 3.14;   // 32-bit float
let g: f64 = 3.14;   // 64-bit float (default)

// Boolean
let is_true: bool = true;

// Char — Unicode scalar value (4 bytes!)
let letter: char = 'A';
let emoji:  char = '🦀';  // valid Rust char!

// Unit type (empty tuple) — represents "nothing"
let unit: () = ();
```

---

