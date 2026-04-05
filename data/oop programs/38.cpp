#include <iostream>
using namespace std;
class A
{
    int x;

public:
    /*A() {
        x = 6;
        cout << "\nA's Constructor";
    }*/
    A(int a)
    {
        x = a;
        cout << "\nA's Constructor";
    }
    ~A()
    {
        cout << "\nA Destructor" << x << endl;
    }
};
class Test
{
private:
    int a, b;
    const int co; //=5;
    A obj;        // (5);

public:
    Test(int x = 0, int b = 0) : a(x), b(b), co(a), obj(5)
    {
        display();
        // obj(5);
        // co = 4;
        cout << "\nTest constructor\n";
        a = x + 3;
        this->b = b - 1;
    }
    void display()
    {

        cout << "const= " << co << ",a = " << a << ", b = " << b << endl;
    }
    ~Test()
    {
        cout << a << b << "Test Destructor\n";
    }
};
int main()
{
    Test obj1(2, 5);
    Test obj2(3, 7);
    obj1.display();
    return 0;
}