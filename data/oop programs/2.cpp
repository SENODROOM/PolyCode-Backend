#include <iostream>
using namespace std;
int &f() // & MEANS THAT THIS FUNCTION RETURNS A REFERENCE TO AN INTEGER VARIABLE.
{
    static int a; // STATIC VARIABLE MEANS THAT THE VALUE OF THIS VARIABLE WILL BE RETAINED BETWEEN FUNCTION CALLS AND IT WILL BE INITIALIZED ONLY ONCE.
    a = 33;
    cout << "function : " << &a << endl;
    return a; // WITHOUT STATIC VARIABLE, THIS FUNCTION WOULD RETURN A REFERENCE TO A LOCAL VARIABLE, WHICH WOULD BE DESTROYED ONCE THE FUNCTION EXITS, LEADING TO UNDEFINED BEHAVIOR. BY DECLARING 'a' AS STATIC, IT ENSURES THAT 'a' PERSISTS FOR THE LIFETIME OF THE PROGRAM, ALLOWING THE FUNCTION TO RETURN A REFERENCE TO IT SAFELY.
}
int main()
{
    int &b = f();
    cout << b << endl;
    cout << &b << endl;
    b = 50;
    cout << b << endl;
    b = f();
    cout << b << endl;
    cout << &b << endl;
    // system("pause");
    return 0;
}