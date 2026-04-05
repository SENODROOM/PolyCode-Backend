#include <iostream>
using namespace std;

class A; // forward declaration of A

class B
{
public:
    void show(A obj); // declare function first
};

class A
{
private:
    int x;

public:
    A(int val)
    {
        x = val;
    }

    friend void B::show(A);
};

void B::show(A obj)
{
    cout << "Value of x: " << obj.x << endl;
}

int main()
{
    A objA(25);
    B objB;

    objB.show(objA);

    return 0;
}