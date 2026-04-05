#include <iostream>
using namespace std;
class Test
{
    int &t;

public:
    Test(int &x) : t(x)
    {
        cout << "Test constructor ";
        t = 20;
    }
    void get()
    {
        cout << t;
    }
};
int main()
{
    int x = 5;
    Test t1(x);
    cout << x << endl;
    x = 10;
    t1.get();
    return 0;
}