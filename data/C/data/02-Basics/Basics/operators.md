# C Operators

## Arithmetic Operators
```c
+   Addition
-   Subtraction
*   Multiplication
/   Division
%   Modulo (remainder)
```

### Example
```c
int a = 10, b = 3;
int sum = a + b;        // 13
int diff = a - b;       // 7
int product = a * b;    // 30
int quotient = a / b;   // 3 (integer division)
int remainder = a % b;  // 1
```

## Assignment Operators
```c
=   Simple assignment
+=  Add and assign
-=  Subtract and assign
*=  Multiply and assign
/=  Divide and assign
%=  Modulo and assign
```

### Example
```c
int x = 10;
x += 5;   // x = 15
x -= 3;   // x = 12
x *= 2;   // x = 24
x /= 4;   // x = 6
x %= 4;   // x = 2
```

## Comparison Operators
```c
==  Equal to
!=  Not equal to
>   Greater than
<   Less than
>=  Greater than or equal to
<=  Less than or equal to
```

### Example
```c
int a = 10, b = 20;
if (a == b) printf("Equal\n");
if (a != b) printf("Not equal\n");
if (a < b) printf("Less than\n");
```

## Logical Operators
```c
&&  Logical AND
||  Logical OR
!   Logical NOT
```

### Example
```c
int age = 25;
int has_license = 1;

if (age >= 18 && has_license) {
    printf("Can drive\n");
}

if (age < 18 || !has_license) {
    printf("Cannot drive\n");
}
```

## Increment and Decrement Operators
```c
++  Increment (prefix or postfix)
--  Decrement (prefix or postfix)
```

### Example
```c
int x = 5;
int y = ++x;  // x = 6, y = 6 (prefix)
int z = x++;  // x = 7, z = 6 (postfix)
```

## Bitwise Operators
```c
&   Bitwise AND
|   Bitwise OR
^   Bitwise XOR
~   Bitwise NOT
<<  Left shift
>>  Right shift
```

### Example
```c
unsigned int a = 5;  // 0101
unsigned int b = 3;  // 0011

unsigned int and_result = a & b;    // 0001 (1)
unsigned int or_result = a | b;     // 0111 (7)
unsigned int xor_result = a ^ b;    // 0110 (6)
unsigned int not_result = ~a;       // Complement
unsigned int left_shift = a << 1;   // 1010 (10)
unsigned int right_shift = a >> 1;  // 0010 (2)
```

## Ternary Operator
```c
condition ? value_if_true : value_if_false
```

### Example
```c
int max = (a > b) ? a : b;
int abs = (x < 0) ? -x : x;
```
