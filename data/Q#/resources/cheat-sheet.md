# Q# Quick Reference Cheat Sheet

## Standard Gates

| Gate | Q# | Matrix | Effect on |0⟩ | Effect on |1⟩ |
|------|----|--------|------------|------------|
| Identity | `I(q)` | [[1,0],[0,1]] | → \|0⟩ | → \|1⟩ |
| Pauli-X (NOT) | `X(q)` | [[0,1],[1,0]] | → \|1⟩ | → \|0⟩ |
| Pauli-Y | `Y(q)` | [[0,-i],[i,0]] | → i\|1⟩ | → -i\|0⟩ |
| Pauli-Z | `Z(q)` | [[1,0],[0,-1]] | → \|0⟩ | → -\|1⟩ |
| Hadamard | `H(q)` | 1/√2[[1,1],[1,-1]] | → \|+⟩ | → \|-⟩ |
| S (Phase) | `S(q)` | [[1,0],[0,i]] | → \|0⟩ | → i\|1⟩ |
| T (π/8) | `T(q)` | [[1,0],[0,e^iπ/4]] | → \|0⟩ | → e^iπ/4\|1⟩ |
| S† | `Adjoint S(q)` | [[1,0],[0,-i]] | → \|0⟩ | → -i\|1⟩ |
| T† | `Adjoint T(q)` | [[1,0],[0,e^-iπ/4]] | → \|0⟩ | → e^-iπ/4\|1⟩ |
| Rx(θ) | `Rx(θ, q)` | rotation | cos(θ/2)\|0⟩-i·sin(θ/2)\|1⟩ | ... |
| Ry(θ) | `Ry(θ, q)` | rotation | cos(θ/2)\|0⟩+sin(θ/2)\|1⟩ | ... |
| Rz(θ) | `Rz(θ, q)` | rotation | e^-iθ/2\|0⟩ | e^iθ/2\|1⟩ |
| R1(θ) | `R1(θ, q)` | [[1,0],[0,e^iθ]] | → \|0⟩ | → e^iθ\|1⟩ |

## Two-Qubit Gates

| Gate | Q# | Description |
|------|----|-------------|
| CNOT | `CNOT(ctrl, tgt)` | Flip target if control=1 |
| CZ | `CZ(ctrl, tgt)` | Phase flip if both=1 |
| SWAP | `SWAP(q1, q2)` | Exchange qubit states |
| CCNOT (Toffoli) | `CCNOT(c1, c2, t)` | Flip target if both controls=1 |
| Controlled-U | `Controlled U([ctrl], args)` | Any gate controlled |

## Qubit Allocation

```qsharp
use q = Qubit();                    // Single qubit
use qs = Qubit[n];                  // Array of n qubits
use (q1, q2) = (Qubit(), Qubit()); // Tuple
use (q, qs) = (Qubit(), Qubit[3]); // Mixed

// Block scoping
use q = Qubit() {
    // q only available here
    H(q);
} // q auto-released
```

## Measurement

```qsharp
let r = M(q);                          // Measure single qubit (Z basis)
let r = Measure([PauliX], [q]);        // Measure in X basis
let r = Measure([PauliZ, PauliZ], [q1, q2]); // Parity measurement
let rs = ForEach(M, qs);              // Measure all (returns Result[])
let rs = MultiM(qs);                   // Measure all (same)
```

## Reset

```qsharp
Reset(q);            // Reset single qubit to |0⟩
ResetAll(qs);        // Reset all qubits in array
```

## Common Operations

```qsharp
ApplyToEach(H, qs);       // Apply H to each qubit
ApplyToEachA(H, qs);      // Adjointable version
ApplyToEachC(H, qs);      // Controlled version
ApplyToEachCA(H, qs);     // Controlled + Adjointable
```

## Type Conversions

```qsharp
IntAsDouble(n)         // Int → Double
RoundD(d)              // Double → Int
ResultAsBool(r)        // Result → Bool
BoolAsResult(b)        // Bool → Result  
ResultArrayAsInt(rs)   // Result[] → Int
IntAsBoolArray(n, bits) // Int → Bool[]
```

## Diagnostics

```qsharp
open Microsoft.Quantum.Diagnostics;

DumpMachine();              // Print full quantum state
DumpRegister(qubits);       // Print state of subset
Message($"Value: {x}");    // Print to output
Fact(condition, "message"); // Assert (test)
EqualityFactI(a, b, "msg"); // Assert integers equal
```

## Math Constants

```qsharp
open Microsoft.Quantum.Math;

PI()       // π ≈ 3.14159...
E()        // e ≈ 2.71828...
Sqrt(x)    // Square root
Log(x)     // Natural logarithm
Sin(x)     // Sine
Cos(x)     // Cosine
Tan(x)     // Tangent
ArcTan2(y, x)  // atan2
AbsD(x)    // Absolute value (Double)
AbsI(x)    // Absolute value (Int)
MaxD(a, b) // Max of two Doubles
MinI(a, b) // Min of two Ints
GCD(a, b)  // Greatest common divisor
```

## Bell States

```qsharp
// |Φ+⟩ = (|00⟩ + |11⟩)/√2
H(q1); CNOT(q1, q2);

// |Φ-⟩ = (|00⟩ - |11⟩)/√2
H(q1); CNOT(q1, q2); Z(q1);

// |Ψ+⟩ = (|01⟩ + |10⟩)/√2
H(q1); CNOT(q1, q2); X(q2);

// |Ψ-⟩ = (|01⟩ - |10⟩)/√2
H(q1); CNOT(q1, q2); Z(q1); X(q2);
```

## Useful Identities

```
H·X·H = Z         H·Z·H = X
X = iY·Z          Y = iZ·X = iX·Z
S = T·T            Z = S·S = T·T·T·T
H = (X+Z)/√2      CNOT = (I⊗H)·CZ·(I⊗H)
```

## Control Flow Quick Reference

```qsharp
// Repeat until success
repeat {
    // ...
} until (condition)
fixup {
    // cleanup on failure
}

// Apply with auto-undo
within {
    // setup
} apply {
    // main operation
}
// setup is automatically undone here
```

## Namespaces to Know

```qsharp
open Microsoft.Quantum.Intrinsic;     // Basic gates
open Microsoft.Quantum.Canon;          // Library ops
open Microsoft.Quantum.Measurement;   // Measurement ops
open Microsoft.Quantum.Diagnostics;   // Debug tools
open Microsoft.Quantum.Math;          // Math functions
open Microsoft.Quantum.Arrays;        // Array utilities
open Microsoft.Quantum.Convert;       // Type conversions
open Microsoft.Quantum.Arithmetic;    // Quantum arithmetic
```
