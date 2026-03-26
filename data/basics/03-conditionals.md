# Conditional Statements in C#

Conditional statements allow your program to make decisions and execute different code based on conditions.

## if Statement

The `if` statement executes a block of code only if a condition is true.

```csharp
if (condition)
{
    // Code to execute if condition is true
}
```

## if-else Statement

The `if-else` statement provides an alternative block of code to execute if the condition is false.

```csharp
if (condition)
{
    // Code to execute if condition is true
}
else
{
    // Code to execute if condition is false
}
```

## if-else if-else Statement

For multiple conditions, you can chain `else if` statements:

```csharp
if (condition1)
{
    // Code to execute if condition1 is true
}
else if (condition2)
{
    // Code to execute if condition1 is false and condition2 is true
}
else
{
    // Code to execute if all conditions are false
}
```

## Comparison Operators

- `==` Equal to
- `!=` Not equal to
- `>` Greater than
- `<` Less than
- `>=` Greater than or equal to
- `<=` Less than or equal to

## Logical Operators

- `&&` Logical AND (both conditions must be true)
- `||` Logical OR (at least one condition must be true)
- `!` Logical NOT (reverses the condition)

## switch Statement

The `switch` statement is useful when you have multiple possible values for a single variable:

```csharp
switch (variable)
{
    case value1:
        // Code for value1
        break;
    case value2:
        // Code for value2
        break;
    default:
        // Code if no case matches
        break;
}
```

## Ternary Operator

A compact way to write simple if-else statements:

```csharp
result = condition ? value_if_true : value_if_false;
```

## Best Practices

- Use clear and descriptive condition names
- Keep conditions simple and readable
- Use `switch` when comparing against multiple constant values
- Use ternary operator for simple assignments
- Always use braces `{}` even for single statements to improve readability
