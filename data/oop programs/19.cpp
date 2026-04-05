#include <iostream>
using namespace std;
class Rectangle
{
public:
    int length;
    int width;
    int area;
    int *ptr;
    Rectangle(int l = 0, int width = 0)
    {
        length = l;
        this->width = width;
        (*this).width = width;
        ptr = &length;
    }
    Rectangle(Rectangle &obj)
    {
    }
    void display()
    {
        area = length * width;
        cout << length << width << area << " , " << *ptr;
    }
};

int main()
{
    Rectangle r1(5, 7);
    *(r1.ptr) = 6;
    Rectangle *r2 = &r1;
    r2->display();
    cout << endl;
    (*r2).display();
    return 0;
}