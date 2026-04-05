#include <iostream>
using namespace std;
class Student
{
    string std_name;

public:
    int rollno = 4000;

public:
    void display()
    {
        cout << "Student name" << std_name << endl;
        cout << "roll no: " << rollno << endl;
        cout << "address: " << address << endl;
        cout << "Section: " << section << endl;
    }
    void get()
    {
        cin >> std_name;
    }
    string address = "ff";
    char section = 'A';
};

int main()
{
    Student s1;
    s1.get();
    s1.display();
    return 0;
}