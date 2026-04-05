// Static Data member and member function
#include <iostream>
using namespace std;
class Count
{

public:
    static const int rateofinterest = 6;
    static constexpr float rot = 6.5;
    static int noofobj;
    int a;
    int b;
    Count()
    {
        cout << "Default constructor\n";
        noofobj++;
    }

    Count(int x)
    {
        cout << "Parameterized constructor\n";
        a = x;
        b = x;
        noofobj++;
    }
    static void func();
    /* {
        cout << "Static method;";
    }*/
};
void Count::func() { cout << "hello"; }
int Count::noofobj = 0;
// static void main(string arg[])//in java
int main()
{
    Count::func();
    cout << Count::noofobj;
    Count c1;
    c1.a = 5;
    c1.b = 4;
    cout << c1.noofobj;

    Count c2(7);
    cout << Count::noofobj;
    cout << endl
         << Count::rateofinterest;
    return 0;
}