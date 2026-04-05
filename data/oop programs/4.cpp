// CORRECT THE FOLLOWING CODE:
// #include <iostream>
// using namespace std;
// int &f()
// {
//     int a = 5;
//     cout << &a << "function\n";
//     return a;
// }
// int main()
// {
//     int &b = f();
//     cout << &b << endl;
//     b = 20;
//     cout << endl
//          << b << &b << endl;
//     int c = f();
//     cout << endl
//          << c << &c;
//     system("pause");
//     return 0;
// }
#include <iostream>
using namespace std;

int &f()
{
    static int a = 5;
    cout << "Address of a inside function: " << &a << endl;
    return a;
}

int main()
{
    int &b = f();
    cout << "Address of b: " << &b << endl;

    b = 20;

    cout << "\nValue of b: " << b << endl;
    cout << "Address of b: " << &b << endl;

    int c = f();

    cout << "\nValue of c: " << c << endl;
    cout << "Address of c: " << &c << endl;

    return 0;
}