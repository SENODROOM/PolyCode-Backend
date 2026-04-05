# Classes and Objects

C++ is object-oriented.

## Example
```cpp
class Student {
public:
    string name;
    int age;
};

int main() {
    Student s1;
    s1.name = "Alice";
    s1.age = 20;
    cout << s1.name << " " << s1.age << endl;
    return 0;
}
Practice

Create a class Book with title, author, price and create 2 objects.
