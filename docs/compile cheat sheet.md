# Compile Cheat Sheet

## Single File
```bash
gcc "file.c" -o program
./program
```

## With Warnings
```bash
gcc -Wall -Wextra "file.c" -o program
```

## With Math Library
```bash
gcc "file.c" -o program -lm
```

## Debug Build
```bash
gcc -g "file.c" -o program
```

## Multi-file Build
```bash
gcc "main.c" "utils.c" -o app
```
