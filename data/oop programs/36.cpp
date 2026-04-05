#include <iostream>
using namespace std;
class A
{
    int a;

public:
    // A(){ this->a = 1; }
    A(int a) : a(a)
    {
        cout << "\nA called";
        // this->a = a;
    }
    int show()
    {
        return a;
    }
    ~A()
    {
        cout << "A dest\n";
    }
};
class Point
{
    int x;
    int y;
    A obj;
    const int t;

public:
    Point(int i = 0, int j = 0) : x(i), y(j), t(j), obj(i)
    {
        x = i + 5;
        y = j + 2;
        // t = 10;
        display();
        cout << " Default parameterized constructor";
    }
    void display()
    {
        cout << "\n x = " << x << " , y= " << y << ", t= " << t;
        cout << "obj a= " << obj.show();
    }
    ~Point()
    {
        cout << endl
             << x << " P dest\n";
    }
};
int main()
{
    Point p(2, 3), p2(4, 5);
    // p.display();
    return 0;
}