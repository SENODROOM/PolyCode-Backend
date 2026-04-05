#include <iostream>
using namespace std;
class Test
{
private:
    int x;
    int y;

public:
    Test(int x = 0, int y = 0)
    {
        this->x = x;
        this->y = y;
    }

    Test *setX(int a)
    {
        x = a;
        return this;
    }

    Test &setY(int b)
    {
        y = b;
        return *this;
    }
    void print()
    {
        cout << "x = " << x << " y = " << y << endl;
    }
};
int main()
{
    Test obj1(5, 5), *ptrObj = &obj1;

    obj1.setX(4)->setY(7).setX(8)->setY(9);
    (*(*obj1.setX(4)).setY(7).setX(8)).setY(9);
    obj1.print();
    return 0;
}