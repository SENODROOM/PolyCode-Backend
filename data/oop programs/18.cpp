#include <iostream>
using namespace std;
class Square
{
    int a = 12;

public:
    int *p = &a;
    Square()
    {
        cout << "\nDefault: ";
        cout << *p;
    }
    Square(int a)
    {
        cout << "\nParameterized: ";
        // a = w;
        cout << a;
        cout << this->a;
        this->a = a;
        cout << *p;
    }
    void display() const
    {
        cout << " values " << a << " , " << *p << " , " << p << endl;
    }

    ~Square()
    {
        cout << "\nDestructor: ";
    }
};

int main()
{
    Square s1;
    Square *s = &s1;
    int x = 25;
    s1.p = &x;
    *(s1.p) = 20;

    cout << x << " ," << &x << endl;
    s1.display();
    s->display();
    (*s).display();
    Square s2(30);
    cout << *(s2.p) << endl;
    return 0;
}