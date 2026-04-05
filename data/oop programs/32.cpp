#include <iostream>
using namespace std;
class Number
{

    int a;
    int b;
    int arr[5];

public:
    Number(int a = 1, int b = 1)
    {

        this->a = a;
        this->b = b;
        for (int i = 0; i < 5; i++)
        {
            arr[i] = i * 2; // 0,2,4,6,8
        }
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
    int &operator[](int index)
    {
        return arr[index];
    }
    Number operator=(Number obj)
    {

        a = obj.a;
        (*this).b = obj.b;
        return *this;
    }
    friend Number operator+(int, Number);
    friend void operator<<(ostream &out, Number obj);
    friend istream &operator>>(istream &in, Number obj);
};
void operator<<(ostream &out, Number obj)
{
    out << obj.a << obj.b;
    // return out;
}
istream &operator>>(istream &out, Number obj)
{
    out >> obj.a >> obj.b;
    return out;
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
    Number n1[3];
    Number n2, n3;
    cout << n1[1];
    cout << endl;
    cout << n2[4];
    n2[4] = 34;
    cout << endl;
    cout << "N3 " << n3[4];
    cout << endl
         << n2[4];
    system("pause");
    return 0;
}