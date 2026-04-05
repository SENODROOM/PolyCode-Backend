#include <iostream>
using namespace std;
class A
{
    int &t;
    int *z;

public:
    A(int &x) : t(x)
    {

        z = new int(5);
    }
    void get() const
    {
        cout << t << " , " << *z << endl;
        t = 7;
        *z = 22;
        // z = new int(33);
        cout << t << " , " << *z << endl;
    }
    ~A() {}
};
int main()
{
    int a = 10;
    A obj(a);
    obj.get();
    A obj2(a);

    return 0;
}