#include <iostream>
using namespace std;
class Box
{

    int length;
    int width;

public:
    Box(int a = 0, int b = 0)
    {
        length = a;
        width = b;
    }
    void display()
    {
        cout << length << ", " << width << endl;
    }
    Box operator++()
    {
        length++;
        width++;
        return *this;
    }
    void operator++(int)
    {
        length++;
        width++;
    }
    void operator--()
    {
        length--;
        width--;
    }
    void operator--(int)
    {
        length--;
        width--;
    }
    void operator-()
    {
        length = -length;
        width = -width;
    }
    Box operator+(Box obj)
    {
        length = length + obj.length;
        width = width + obj.width;
        return *this;
    }
    inline void setLength(int);
};
void Box::setLength(int x)
{
    length = x;
}
int main()
{
    Box b1;
    Box b2;
    b2 = ++b1;
    b1.display();
    b1++;
    cout << endl;
    b1.display();
    -b1;
    Box b3 = b1 + b2;
    b3.display();
    b3 = b3 + 2;
    // b3 = 2 + b3;
    b3.display();
    b1.setLength(7);
    /*enum week
    {saturday,sunday

    };
    week w1 = sunday;

    cout << w1;*/
    return 0;
}