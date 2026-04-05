#include <iostream>
using namespace std;
class Count
{

public:
    static constexpr float rateofinterest = 7.5;
    static int count;
    int a;
    int b;
    Count()
    {
        cout << "Default constructor\n";
        count++;
    }

    Count(int x)
    {
        cout << "Parameterized constructor\n";
        a = x;
        b = x;
        count++;
    }
    /* static void show() {
         cout << "Static method";
     }*/
    static void show();
    inline void display() const
    {
        cout << "Display : " << a << " , " << b << endl;
    }
    Count operator++()
    {
        a++;
        b++;
        return *this;
    }
    void operator++(int)
    {
        a++;
        b++;
    }
    void operator--()
    {
        a--;
        b--;
    }
    void operator--(int)
    {
        a--;
        b--;
    }
    Count operator+(Count obj)
    {
        this->a = this->a + obj.a;
        this->b = this->b + obj.b;
        return *this;
    }
};
int Count::count = 0;
void Count::show()
{
    cout << "Static method";
}
int main()
{
    cout << Count::count << " , " << Count::rateofinterest;
    Count c;
    c.a = c.b = 5;
    cout << Count::count;
    Count::show();
    Count c2(7);
    cout << c2.count;
    Count c3 = ++c;
    c++;
    c.display();
    c3 = c + c2;
    c3 = c + 2;
    // c3 = 2+c ;//non member function will discuss in next class
    // cout << c;
    return 0;
}