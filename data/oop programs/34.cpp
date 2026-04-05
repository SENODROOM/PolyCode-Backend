#include <iostream>
#include <string>
using namespace std;

//------------Aggregation --------------
class Employee
{
private:
    string name;
    int age;

public:
    Employee(string name = "XYZ", int age = 20)
    {
        this->name = name;
        this->age = age;
    }

    void EmployeePrint()
    {
        cout << "Name: " << this->name << endl;
        cout << "Age: " << this->age << endl;
    }
};

class Organization
{
private:
    string orgName;
    string orgType;
    Employee *emp;

public:
    Organization(string orgName = "ABC", string orgType = "Health", Employee *emp = NULL)
    {
        this->orgName = orgName;
        this->orgType = orgType;
        this->emp = emp;
    }

    void OrgPrint()
    {
        cout << "Org. Name: " << orgName << endl;
        cout << "Org. Type: " << orgType << endl;
        emp->EmployeePrint();
    }
};

int main()
{
    Employee emp1("Ahmad", 25);
    Organization org1("ABC College", "Health", &emp1);
    org1.OrgPrint();

    return 0;
}