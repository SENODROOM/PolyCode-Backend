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
    void operator++(int);
    /*{
        length++; width++;
    }*/
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
    Box operator+=(Box obj)
    {
        length = length + obj.length;
        width = width + obj.width;
        return *this;
    }
    bool operator>(Box obj)
    {
        bool b = false;
        if (length > obj.length && width > obj.width)
            b = true;
        return b;
    }
    friend Box operator+(int, Box);
    friend ostream &operator<<(ostream &o, Box b);
    inline void setLength(int);
};
ostream &operator<<(ostream &o, Box b)
{
    o << b.length << ", ";

    o << b.width;
    return o;
}
Box operator+(int z, Box b)
{
    Box x;
    x.length = z + b.length;
    x.width = z + b.width;
    return x;
}
void Box::operator++(int)
{
    length++;
    width++;
}
void Box::setLength(int x)
{
    length = x;
}

int main()
{
    Box b1;
    Box b2;
    b2 = ++b1;
    if (b1 > b2)
    {
        cout << "B1 is greater";
    }
    b2.operator--();
    // b1++;
    b1.display();
    b1++;
    cout << endl;
    b1.display();
    -b1;
    Box b3 = b1 + b2;
    b3.display();
    b3 = b3 + 2;
    b3 += b1;
    b3 = b3 + b1;
    b3 = 2 + b3;
    b3.display();
    cout << b1 << endl
         << b3;
    b1.setLength(7);

    return 0;
}