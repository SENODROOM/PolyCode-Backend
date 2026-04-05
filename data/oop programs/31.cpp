#include <iostream>
using namespace std;
class Number
{

    int a;
    int b;

public:
    Number(int a = 1, int b = 1)
    {

        this->a = a;
        this->b = b;
    }
    void operator++()
    {
        a++;
        b++;
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
    Number operator-()
    {
        a = -a;
        b = -b;
        return *this;
    }
    Number operator+(Number obj)
    {
        Number temp;
        temp.a = a + obj.a;
        temp.b = b + obj.b;
        return temp;
    }
    bool operator>(Number obj)
    {
        if (a > obj.a && b > obj.b)
        {
            return true;
        }
        else
            return false;
    }

    Number operator=(Number obj)
    {

        a = obj.a;
        (*this).b = obj.b;
        return *this;
    }
    friend Number operator+(int, Number);
    friend void operator<<(ostream &out, Number obj);
};
void operator<<(ostream &out, Number obj)
{
    out << obj.a << obj.b;
    // return out;
}
Number operator+(int a, Number obj)
{
    Number temp;
    temp.a = a + obj.a;
    temp.b = a + obj.b;
    return temp;
}

int main()
{
    Number n1(4, 3);
    /*++n1;
    n1++;*/
    Number n2; // = -n1;
    Number n3 = n2 + n1;
    n3 = n3 + 3;
    // n3.operator+(5);
    n2 = 2 + n1;
    if (n1 > n3)
    {
        cout << "n1 is greater";
    }
    cout << n1; // << endl
    cout << n3;
    // system("pause");
    return 0;
}