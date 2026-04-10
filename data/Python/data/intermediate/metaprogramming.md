# Python Metaprogramming

## Metaprogramming Basics

### What is Metaprogramming?
```python
# Metaprogramming is writing code that manipulates other code
# It treats code as data, allowing programs to modify themselves

# Simple example: function that creates functions
def create_multiplier(factor):
    """Create a function that multiplies by the given factor."""
    def multiplier(x):
        return x * factor
    return multiplier

# Create specialized functions
doubler = create_multiplier(2)
tripler = create_multiplier(3)

print(f"Doubler of 5: {doubler(5)}")
print(f"Tripler of 5: {tripler(5)}")

# This is metaprogramming - we're creating code at runtime
```

### Introspection
```python
# Introspection - examining objects and their properties
class MyClass:
    """A simple class for demonstration."""
    
    def __init__(self, value):
        self.value = value
    
    def get_value(self):
        return self.value
    
    def set_value(self, new_value):
        self.value = new_value

obj = MyClass(42)

# Introspection functions
print(f"Type: {type(obj)}")
print(f"Class: {obj.__class__}")
print(f"Module: {obj.__module__}")
print(f"Dict: {obj.__dict__}")
print(f"Doc: {obj.__doc__}")

# Introspecting methods and attributes
print(f"Methods: {[method for method in dir(obj) if not method.startswith('_')]}")
print(f"All attributes: {dir(obj)}")

# Using inspect module
import inspect

print(f"Is function: {inspect.isfunction(obj.get_value)}")
print(f"Is method: {inspect.ismethod(obj.get_value)}")
print(f"Is class: {inspect.isclass(MyClass)}")
print(f"Signature: {inspect.signature(obj.get_value)}")
```

## Metaclasses

### Understanding Metaclasses
```python
# A metaclass is a class whose instances are classes
# In Python, type is the default metaclass

class MyMeta(type):
    """Custom metaclass."""
    
    def __new__(cls, name, bases, attrs):
        print(f"Creating class: {name}")
        print(f"Bases: {bases}")
        print(f"Attributes: {list(attrs.keys())}")
        
        # Add a new attribute to all classes using this metaclass
        attrs['created_by'] = 'MyMeta'
        
        return super().__new__(cls, name, bases, attrs)

class MyClass(metaclass=MyMeta):
    """Class using custom metaclass."""
    
    def __init__(self, value):
        self.value = value

# When MyClass is defined, MyMeta.__new__ is called
obj = MyClass(10)
print(f"Created by: {obj.created_by}")
```

### Practical Metaclass Examples
```python
class SingletonMeta(type):
    """Metaclass that implements the singleton pattern."""
    
    _instances = {}
    
    def __call__(cls, *args, **kwargs):
        if cls not in cls._instances:
            cls._instances[cls] = super().__call__(*args, **kwargs)
        return cls._instances[cls]

class Singleton(metaclass=SingletonMeta):
    """Singleton class."""
    
    def __init__(self):
        self.value = 0

# Test singleton behavior
s1 = Singleton()
s2 = Singleton()

print(f"Same instance: {s1 is s2}")
s1.value = 42
print(f"s2.value: {s2.value}")  # Both reference the same instance

class ValidationMeta(type):
    """Metaclass that validates class attributes."""
    
    def __new__(cls, name, bases, attrs):
        # Validate that all classes have a 'version' attribute
        if 'version' not in attrs:
            raise TypeError(f"Class {name} must have a 'version' attribute")
        
        # Validate that version is a string
        if not isinstance(attrs['version'], str):
            raise TypeError(f"Version must be a string in class {name}")
        
        return super().__new__(cls, name, bases, attrs)

class ValidatedClass(metaclass=ValidationMeta):
    """Class with validation metaclass."""
    
    version = "1.0.0"
    
    def __init__(self):
        self.data = "Valid data"

# This would fail validation
try:
    class InvalidClass(metaclass=ValidationMeta):
        data = "No version attribute"
except TypeError as e:
    print(f"Validation error: {e}")
```

### Dynamic Class Creation
```python
# Creating classes dynamically using type
def create_class(name, bases, attrs):
    """Create a class dynamically."""
    return type(name, bases, attrs)

# Create a class dynamically
DynamicClass = create_class('DynamicClass', (), {
    'value': 42,
    'get_value': lambda self: self.value,
    'set_value': lambda self, val: setattr(self, 'value', val)
})

obj = DynamicClass()
print(f"Dynamic class value: {obj.get_value()}")
obj.set_value(100)
print(f"Updated value: {obj.get_value()}")

# Factory function for creating classes
def class_factory(class_name, methods):
    """Factory function for creating classes with custom methods."""
    attrs = {}
    
    for method_name, method_code in methods.items():
        exec(method_code, attrs)
    
    return type(class_name, (), attrs)

# Create a class with custom methods
methods = {
    'add': 'def add(self, a, b): return a + b',
    'multiply': 'def multiply(self, a, b): return a * b',
    'subtract': 'def subtract(self, a, b): return a - b'
}

MathClass = class_factory('MathClass', methods)
math_obj = MathClass()

print(f"Add: {math_obj.add(5, 3)}")
print(f"Multiply: {math_obj.multiply(5, 3)}")
print(f"Subtract: {math_obj.subtract(5, 3)}")
```

## Decorators as Metaprogramming

### Function Decorators
```python
import functools
import time

def timer(func):
    """Decorator that times function execution."""
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        start = time.time()
        result = func(*args, **kwargs)
        end = time.time()
        print(f"{func.__name__} took {end - start:.4f} seconds")
        return result
    return wrapper

def cache(func):
    """Decorator that caches function results."""
    cache = {}
    
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        key = str(args) + str(sorted(kwargs.items()))
        if key not in cache:
            cache[key] = func(*args, **kwargs)
            print(f"Caching result for {key}")
        else:
            print(f"Using cached result for {key}")
        return cache[key]
    return wrapper

# Apply decorators
@timer
@cache
def fibonacci(n):
    """Calculate Fibonacci number."""
    if n <= 1:
        return n
    return fibonacci(n-1) + fibonacci(n-2)

print(f"Fibonacci(10): {fibonacci(10)}")
print(f"Fibonacci(15): {fibonacci(15)}")
```

### Class Decorators
```python
def add_methods(*methods):
    """Class decorator that adds methods to a class."""
    def decorator(cls):
        for method_name, method_func in methods:
            setattr(cls, method_name, method_func)
        return cls
    return decorator

def validate_attributes(**validators):
    """Class decorator that validates attributes."""
    def decorator(cls):
        original_init = cls.__init__
        
        def __init__(self, *args, **kwargs):
            original_init(self, *args, **kwargs)
            
            for attr_name, validator in validators.items():
                if hasattr(self, attr_name):
                    value = getattr(self, attr_name)
                    if not validator(value):
                        raise ValueError(f"Invalid {attr_name}: {value}")
        
        cls.__init__ = __init__
        return cls
    return decorator

# Define additional methods
def greet_method(self):
    return f"Hello, {self.name}!"

def age_in_months(self):
    return self.age * 12

# Apply class decorators
@add_methods(greet=greet_method, age_in_months=age_in_months)
@validate_attributes(name=lambda x: isinstance(x, str) and len(x) > 0,
                     age=lambda x: isinstance(x, int) and x >= 0)
class Person:
    """Person class with added methods and validation."""
    
    def __init__(self, name, age):
        self.name = name
        self.age = age

# Test the decorated class
person = Person("Alice", 25)
print(f"Greeting: {person.greet()}")
print(f"Age in months: {person.age_in_months()}")

try:
    invalid_person = Person("", -5)
except ValueError as e:
    print(f"Validation error: {e}")
```

## Dynamic Code Generation

### Code Generation with exec
```python
def generate_function(name, operation):
    """Generate a function dynamically."""
    code = f"""
def {name}(a, b):
    return a {operation} b
"""
    
    # Execute the code in the current namespace
    exec(code, globals())
    
    # Return the generated function
    return globals()[name]

# Generate functions dynamically
add_func = generate_function('dynamic_add', '+')
multiply_func = generate_function('dynamic_multiply', '*')
subtract_func = generate_function('dynamic_subtract', '-')

print(f"Dynamic add: {add_func(5, 3)}")
print(f"Dynamic multiply: {multiply_func(5, 3)}")
print(f"Dynamic subtract: {subtract_func(5, 3)}")
```

### Template-based Code Generation
```python
class CodeTemplate:
    """Template-based code generator."""
    
    def __init__(self, template):
        self.template = template
    
    def generate(self, **kwargs):
        """Generate code from template with substitutions."""
        return self.template.format(**kwargs)

# Define templates
class_template = """
class {class_name}:
    def __init__(self{init_params}):
        {init_body}
    
    def __str__(self):
        return f"{class_name}({str_params})"
"""

method_template = """
    def {method_name}(self{method_params}):
        {method_body}
"""

def generate_class(class_name, attributes):
    """Generate a complete class from attributes."""
    
    # Generate __init__ method
    init_params = ', '.join([f"self, {attr}" for attr in attributes])
    init_body = '\n        '.join([f"self.{attr} = {attr}" for attr in attributes])
    
    # Generate __str__ method
    str_params = ', '.join([f"self.{attr}" for attr in attributes])
    
    # Generate class code
    template = CodeTemplate(class_template)
    class_code = template.generate(
        class_name=class_name,
        init_params=(', ' + init_params) if init_params else '',
        init_body='\n        ' + init_body if init_body else 'pass',
        str_params=str_params
    )
    
    # Execute the generated code
    exec(class_code, globals())
    
    return globals()[class_name]

# Generate classes dynamically
Person = generate_class('Person', ['name', 'age'])
Product = generate_class('Product', ['id', 'name', 'price'])

# Test generated classes
person = Person("Alice", 25)
product = Product(1, "Laptop", 999.99)

print(f"Person: {person}")
print(f"Product: {product}")
```

## Reflection and Modification

### Runtime Class Modification
```python
class DynamicClass:
    """A class that can be modified at runtime."""
    
    def __init__(self):
        self.data = {}

def add_method_to_class(cls, method_name, method):
    """Add a method to a class at runtime."""
    setattr(cls, method_name, method)

def add_property_to_class(cls, prop_name, getter, setter=None):
    """Add a property to a class at runtime."""
    if setter:
        setattr(cls, prop_name, property(getter, setter))
    else:
        setattr(cls, prop_name, property(getter))

# Add methods dynamically
def new_method(self, value):
    """Dynamically added method."""
    self.data['new'] = value
    return f"Added: {value}"

def get_data(self, key):
    """Dynamically added getter method."""
    return self.data.get(key, "Not found")

add_method_to_class(DynamicClass, 'add', new_method)
add_method_to_class(DynamicClass, 'get', get_data)

# Add properties dynamically
def size_getter(self):
    return len(self.data)

def size_setter(self, value):
    raise AttributeError("Can't set size directly")

add_property_to_class(DynamicClass, 'size', size_getter, size_setter)

# Test the modified class
obj = DynamicClass()
obj.add("test", "value")
print(f"Get 'test': {obj.get('test')}")
print(f"Size: {obj.size}")

try:
    obj.size = 10
except AttributeError as e:
    print(f"Expected error: {e}")
```

### Monkey Patching
```python
import math

# Original function
original_sqrt = math.sqrt

# Monkey patch the sqrt function
def patched_sqrt(x):
    """Patched sqrt function with validation."""
    if x < 0:
        raise ValueError("Cannot calculate square root of negative number")
    return original_sqrt(x)

math.sqrt = patched_sqrt

# Test the patched function
print(f"sqrt(16): {math.sqrt(16)}")

try:
    math.sqrt(-4)
except ValueError as e:
    print(f"Expected error: {e}")

# Restore original function
math.sqrt = original_sqrt
print(f"sqrt(9): {math.sqrt(9)}")

# Monkey patching classes
class OriginalClass:
    def method(self):
        return "Original method"

def patched_method(self):
    return "Patched method"

# Patch the method
OriginalClass.method = patched_method

obj = OriginalClass()
print(f"Method result: {obj.method()}")
```

## Advanced Metaprogramming

### Descriptor Protocol
```python
class Descriptor:
    """Custom descriptor for managed attributes."""
    
    def __init__(self, name, validator=None):
        self.name = name
        self.validator = validator
        self.value = None
    
    def __get__(self, obj, objtype=None):
        if obj is None:
            return self
        return self.value
    
    def __set__(self, obj, value):
        if self.validator and not self.validator(value):
            raise ValueError(f"Invalid value for {self.name}: {value}")
        self.value = value

class ValidatedPerson:
    """Class using descriptors for validation."""
    
    name = Descriptor('name', lambda x: isinstance(x, str) and len(x) > 0)
    age = Descriptor('age', lambda x: isinstance(x, int) and x >= 0)
    
    def __init__(self, name, age):
        self.name = name
        self.age = age

# Test descriptor validation
person = ValidatedPerson("Alice", 25)
print(f"Person: {person.name}, {person.age}")

try:
    invalid_person = ValidatedPerson("", -5)
except ValueError as e:
    print(f"Descriptor validation error: {e}")
```

### Property Metaprogramming
```python
class PropertyMeta(type):
    """Metaclass that automatically creates properties."""
    
    def __new__(cls, name, bases, attrs):
        # Find all private attributes and create public properties
        for attr_name, attr_value in attrs.items():
            if attr_name.startswith('_') and not attr_name.startswith('__'):
                public_name = attr_name[1:]  # Remove underscore
                
                # Create getter
                def make_getter(private_name):
                    def getter(self):
                        return getattr(self, private_name)
                    return getter
                
                # Create setter
                def make_setter(private_name):
                    def setter(self, value):
                        setattr(self, private_name, value)
                    return setter
                
                # Create property
                getter = make_getter(attr_name)
                setter = make_setter(attr_name)
                
                attrs[public_name] = property(getter, setter)
        
        return super().__new__(cls, name, bases, attrs)

class AutoProperties(metaclass=PropertyMeta):
    """Class with automatically generated properties."""
    
    def __init__(self):
        self._name = "Default"
        self._value = 0

# Test auto-generated properties
obj = AutoProperties()
obj.name = "Custom"
obj.value = 42

print(f"Name: {obj.name}")
print(f"Value: {obj.value}")
```

## Practical Applications

### API Client Generator
```python
def generate_api_client(base_url, endpoints):
    """Generate an API client class from endpoint definitions."""
    
    class_template = """
class APIClient:
    def __init__(self, base_url):
        self.base_url = base_url
        self.session = None
    
    def _request(self, method, endpoint, **kwargs):
        import requests
        url = f"{{self.base_url}}/{{endpoint}}"
        response = requests.request(method, url, **kwargs)
        response.raise_for_status()
        return response.json()
    
{methods}
"""
    
    methods = []
    for endpoint_name, endpoint_config in endpoints.items():
        method_name = endpoint_name
        http_method = endpoint_config.get('method', 'GET')
        endpoint_path = endpoint_config.get('path', endpoint_name)
        
        method_code = f"""
    def {method_name}(self, **kwargs):
        return self._request('{http_method}', '{endpoint_path}', **kwargs)
"""
        methods.append(method_code)
    
    template = CodeTemplate(class_template)
    class_code = template.generate(methods='\n'.join(methods))
    
    exec(class_code, globals())
    return globals()['APIClient']

# Define API endpoints
endpoints = {
    'get_user': {'method': 'GET', 'path': 'users/{{user_id}}'},
    'create_user': {'method': 'POST', 'path': 'users'},
    'update_user': {'method': 'PUT', 'path': 'users/{{user_id}}'},
    'delete_user': {'method': 'DELETE', 'path': 'users/{{user_id}}'}
}

# Generate API client
APIClient = generate_api_client('https://api.example.com', endpoints)

# Test the generated client
client = APIClient('https://api.example.com')
print(f"Generated methods: {[method for method in dir(client) if not method.startswith('_')]}")
```

### Configuration Manager
```python
class ConfigMeta(type):
    """Metaclass for configuration management."""
    
    def __new__(cls, name, bases, namespace):
        # Process configuration class
        config_values = {}
        
        for key, value in namespace.items():
            if not key.startswith('_') and not callable(value):
                config_values[key] = value
        
        # Store configuration in class
        namespace['_config'] = config_values
        namespace['_original_config'] = config_values.copy()
        
        return super().__new__(cls, name, bases, namespace)

class BaseConfig(metaclass=ConfigMeta):
    """Base configuration class."""
    
    @classmethod
    def get_config(cls):
        """Get current configuration."""
        return cls._config.copy()
    
    @classmethod
    def set_config(cls, **kwargs):
        """Set configuration values."""
        cls._config.update(kwargs)
    
    @classmethod
    def reset_config(cls):
        """Reset configuration to original values."""
        cls._config = cls._original_config.copy()
    
    @classmethod
    def from_dict(cls, config_dict):
        """Create configuration from dictionary."""
        cls.set_config(**config_dict)
        return cls()

# Application-specific configuration
class AppConfig(BaseConfig):
    """Application configuration."""
    
    DEBUG = False
    LOG_LEVEL = "INFO"
    MAX_CONNECTIONS = 10
    TIMEOUT = 30

# Use the configuration manager
print(f"Initial config: {AppConfig.get_config()}")
AppConfig.set_config(DEBUG=True, LOG_LEVEL="DEBUG")
print(f"Updated config: {AppConfig.get_config()}")
AppConfig.reset_config()
print(f"Reset config: {AppConfig.get_config()}")
```

## Performance and Security

### Performance Considerations
```python
import timeit

# Compare metaclass vs regular class creation
def regular_class_creation():
    """Create regular class."""
    class RegularClass:
        def __init__(self):
            self.value = 42
    return RegularClass()

def metaclass_class_creation():
    """Create class with metaclass."""
    class SimpleMeta(type):
        pass
    
    class MetaClass(metaclass=SimpleMeta):
        def __init__(self):
            self.value = 42
    return MetaClass()

# Time comparison
regular_time = timeit.timeit(regular_class_creation, number=1000)
meta_time = timeit.timeit(metaclass_class_creation, number=1000)

print(f"Regular class creation: {regular_time:.4f} seconds")
print(f"Metaclass class creation: {meta_time:.4f} seconds")
print(f"Overhead: {((meta_time - regular_time) / regular_time * 100):.2f}%")
```

### Security Considerations
```python
import ast
import inspect

def safe_exec(code_string, namespace=None):
    """Safely execute code string with limited namespace."""
    if namespace is None:
        namespace = {}
    
    # Parse the code to check for dangerous operations
    try:
        tree = ast.parse(code_string)
    except SyntaxError as e:
        raise ValueError(f"Invalid syntax: {e}")
    
    # Check for dangerous nodes
    dangerous_nodes = (ast.Import, ast.ImportFrom, ast.Exec, ast.Eval)
    
    for node in ast.walk(tree):
        if isinstance(node, dangerous_nodes):
            raise SecurityError(f"Dangerous operation detected: {type(node).__name__}")
    
    # Execute with limited builtins
    safe_builtins = {
        'print': print,
        'len': len,
        'range': range,
        'list': list,
        'dict': dict,
        'set': set,
        'tuple': tuple,
    }
    
    exec(code_string, {'__builtins__': safe_builtins}, namespace)
    return namespace

class SecurityError(Exception):
    """Security-related exception."""
    pass

# Test safe execution
try:
    result = safe_exec("""
result = [x * 2 for x in range(5)]
print(f"Result: {result}")
""")
    print("Safe execution successful")
except (ValueError, SecurityError) as e:
    print(f"Security error: {e}")

try:
    result = safe_exec("import os")
except SecurityError as e:
    print(f"Expected security error: {e}")
```

## Best Practices

### Metaprogramming Best Practices
```python
# 1. Use metaclasses sparingly and for clear purposes
class SingletonMeta(type):
    """Singleton metaclass with clear purpose."""
    _instances = {}
    
    def __call__(cls, *args, **kwargs):
        if cls not in cls._instances:
            cls._instances[cls] = super().__call__(*args, **kwargs)
        return cls._instances[cls]

# 2. Document metaclass behavior clearly
class DocumentedMeta(type):
    """
    Metaclass that adds documentation to classes.
    
    Automatically adds __doc__ if not provided.
    """
    
    def __new__(cls, name, bases, attrs):
        if '__doc__' not in attrs:
            attrs['__doc__'] = f"Auto-generated documentation for {name}"
        return super().__new__(cls, name, bases, attrs)

# 3. Handle exceptions properly in metaclasses
class SafeMeta(type):
    """Metaclass with proper error handling."""
    
    def __new__(cls, name, bases, attrs):
        try:
            return super().__new__(cls, name, bases, attrs)
        except Exception as e:
            raise TypeError(f"Failed to create class {name}: {e}")

# 4. Use functools.wraps with decorators
import functools

def proper_decorator(func):
    """Decorator with proper metadata preservation."""
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        return func(*args, **kwargs)
    return wrapper

# 5. Validate inputs in metaprogramming code
class ValidatingMeta(type):
    """Metaclass that validates class creation."""
    
    def __new__(cls, name, bases, attrs):
        # Validate class name
        if not name.isidentifier():
            raise ValueError(f"Invalid class name: {name}")
        
        # Validate required attributes
        if 'required_method' not in attrs:
            raise TypeError(f"Class {name} must implement required_method")
        
        return super().__new__(cls, name, bases, attrs)

# 6. Consider performance implications
def performance_aware_decorator(func):
    """Decorator that considers performance."""
    cache = {}
    
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        key = str(args) + str(sorted(kwargs.items()))
        if key not in cache:
            cache[key] = func(*args, **kwargs)
        return cache[key]
    
    return wrapper

# 7. Use type hints for clarity
from typing import Type, Any, Dict

def typed_metaclass(name: str, bases: tuple, attrs: Dict[str, Any]) -> Type:
    """Metaclass function with type hints."""
    return type(name, bases, attrs)

# 8. Test metaprogramming code thoroughly
def test_metaclass_behavior():
    """Test metaclass behavior comprehensively."""
    
    class TestMeta(type):
        def __new__(cls, name, bases, attrs):
            attrs['meta_created'] = True
            return super().__new__(cls, name, bases, attrs)
    
    class TestClass(metaclass=TestMeta):
        pass
    
    assert hasattr(TestClass, 'meta_created')
    print("Metaclass test passed")

# 9. Avoid excessive magic
class SimpleMeta(type):
    """Simple metaclass without excessive magic."""
    
    def __new__(cls, name, bases, attrs):
        # Only add simple, predictable behavior
        attrs['creation_time'] = time.time()
        return super().__new__(cls, name, bases, attrs)

# 10. Consider alternatives first
# Use decorators instead of metaclasses when possible
def simple_decorator(cls):
    """Decorator alternative to metaclass."""
    cls.decorated = True
    return cls

@simple_decorator
class DecoratedClass:
    pass

print(f"Decorated: {DecoratedClass.decorated}")
```

## Summary

Python metaprogramming provides powerful capabilities:

**Core Concepts:**
- Code that manipulates other code
- Introspection and reflection
- Dynamic code generation and modification
- Runtime behavior alteration

**Key Techniques:**
- Metaclasses for class creation control
- Decorators for function/class modification
- Dynamic class and function creation
- Runtime attribute and method manipulation

**Common Use Cases:**
- Singleton pattern implementation
- API client generation
- Configuration management
- Validation and type checking
- Framework and library development

**Advanced Features:**
- Descriptor protocol for managed attributes
- Property metaprogramming
- Template-based code generation
- Safe code execution
- Performance optimization

**Best Practices:**
- Use metaprogramming sparingly and clearly
- Document behavior and intentions
- Handle errors and exceptions properly
- Consider performance implications
- Test thoroughly
- Validate inputs and ensure security
- Prefer simpler alternatives when possible

Metaprogramming enables flexible, dynamic Python code but should be used judiciously to maintain readability and maintainability.
