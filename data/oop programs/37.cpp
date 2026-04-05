#include <iostream>
using namespace std;
class A
{
    int &t;

public:
    A(int &t2) : t(t2)
    {
    }
    int getT()
    {
        return t;
    }
};
int main()
{
    int x = 20;
    A obj(x);
    cout << x << " , " << obj.getT() << endl;
    x = 30;
    cout << x << " , " << obj.getT() << endl;
    int a = 99;
    A obj2(a);
    cout << a << " , " << obj2.getT() << endl;
    a = 30;
    cout << a << " , " << obj2.getT() << endl;
    return 0;
}