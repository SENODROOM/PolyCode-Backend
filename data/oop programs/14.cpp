#include <iostream>
#include <memory> // for smart pointers
using namespace std;
int main()
{
    shared_ptr<int> p1(new int(10));
    weak_ptr<int> p2 = p1;
    if (auto a = p2.lock())
    {
        cout << *a << " , " << a.use_count() << endl;
        *a = 20;
        cout << p1.use_count() << endl;
    }
    cout << *p1 << ", " << p1.use_count() << endl;
    p1.reset();
    cout << p1.use_count() << endl;
    return 0;
}