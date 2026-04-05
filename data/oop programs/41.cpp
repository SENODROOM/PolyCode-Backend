#include <iostream>
using namespace std;
class A
{
    int a;

protected:
    int b;

public:
    int c;
    A()
    {
        cout << "A's constructor\n";
    }
    ~A()
    {
        cout << "A's destructor\n";
    }
};
class B : private A
{
    int d;

protected:
    int e;

public:
    int f;
    B() : A()
    {
        cout << b << c;
        cout << "B's constructor\n";
    }
    ~B()
    {
        cout << "B's destructor\n";
    }
};
class C : protected B, A
{
    int x;

protected:
    int y;

public:
    int z;
    C() : B(), A()
    {
        cout //<< b << c
            << e << f;
        cout << "C's constructor\n";
    }
    ~C()
    {
        cout << "C's destructor\n";
    }
};
int main()
{
    /*A Aobj;
    B Bobj2;*/
    C Cobj3;
    // cout << Aobj.c << Bobj2.f << Cobj3.z;
    return 0;
}