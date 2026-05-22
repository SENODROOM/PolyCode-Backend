# Recursion

Function calling itself.

## Example
```php
<?php
function factorial($n) {
    if($n == 0) return 1;
    return $n * factorial($n - 1);
}
?>
Practice

Write recursive sum function.
