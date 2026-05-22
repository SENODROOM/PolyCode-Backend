# Error Handling

Handles runtime errors.

## Example
```php
<?php
try {
    throw new Exception("Error");
} catch(Exception $e) {
    echo $e->getMessage();
}
?>
Practice

Handle an exception.
