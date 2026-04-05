#include <iostream>
using namespace std;
class A
{
private:
    int a;

protected:
    int b;

public:
    int c;
    A()
    {
        a = 10;
        b = 11;
        c = 12;
        cout << "A's constructor\n";
    }
    void display()
    {
        cout << "A's members\n";
        cout << "A= " << a << " , B= " << b << ", C= " << c;
    }
    ~A()
    {
        cout << "A's destructor\n";
    }
};
class B : public A
{
private:
    int d;

protected:
    int e;

public:
    int f;
    B()
    {
        cout << b << c;
        cout << "B's constructor\n";
    }
    B(int i, int j)
    {
        b = i;
        c = j;
        // cout << b << c;
        cout << "B's parameterized constructor\n";
        //    display();
    }
    ~B()
    {
        cout << "B's destructor\n";
    }
};
class C : public B
{ // public A,
private:
    int x;

protected:
    int y;

public:
    int z;
    C() : B(2, 3)
    {
        // cout << B::b << A::c;
        cout << "C's constructor\n";
        // B::display();
    }
    ~C()
    {
        cout << "C's destructor\n";
    }
};
int main()
{
    C obj;
    obj.display(); // obj.z; obj.f;obj.c;
    // A obj2; //obj2.c;
    // B obj3; //cout<<obj3.f<< obj3.c;
    return 0;
}