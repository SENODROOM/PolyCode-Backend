# Memory Management in Python

## How Python Manages Memory

Python uses:
- Reference Counting
- Garbage Collector

---

## Example

```python
a = []
b = a
del a
```

---

## Garbage Collection

Cycles are handled by GC.

---

## Pitfalls

- Circular references
- Large unused objects

---

## Key Takeaways

- Use gc module if needed
