#include <iostream>
using namespace std;
int &f()
{
    static int a;
    a = 5;
    cout << &a << "function\n";
    return a;
}
int main()
{
    int &b = f();
    cout << &b << endl;
    b = 20;
    cout << endl
         << b << ", " << &b << endl;
    f();
    int c = f();
    cout << endl
         << c << ", " << &c;
    // system("pause");
    return 0;
}