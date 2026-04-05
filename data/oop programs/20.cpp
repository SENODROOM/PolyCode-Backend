#include <iostream>
using namespace std;
class Data
{
public:
    int a;
    int *ptr;
    int b;
    int *bptr = &b;
    Data(int a = 0)
    {
        cout << "Default parameterized Constructor\n";
        ptr = new int(a);
        this->a = a;
    }
    Data(const Data &d)
    {
        cout << "Copy constructor\n";
        a = d.a;
        ptr = new int(*(d.ptr));
    }
    // A move constructor in C++ is a special type of constructor
    //  that transfers the ownership of resources from a source
    //  object (which is typically a temporary, soon-to-be-destroyed
    //  object) to a new object, instead of performing a deep
    //  copy of the data.
    Data(Data &&objMove)
    {
        cout << "move constructor\n";
        ptr = objMove.ptr;
        bptr = objMove.ptr;
        objMove.ptr = nullptr;
        objMove.bptr = nullptr;
    }
    void display() const
    {
        cout << a << " , " << b << " , " << *ptr << ", " << *bptr << endl;
    }
    void func(int a, int b)
    {
        this->a = a;
        (*this).b = b;
    }
    ~Data()
    {
        cout << "Destructor :" << a << endl;
        delete ptr;
    }
};
int main()
{
    Data d1(5);
    *(d1.bptr) = 4;
    d1.display();
    Data *dptr = &d1;
    *dptr->ptr = 8;
    dptr->display();
    *(*dptr).ptr = 10;
    (*dptr).display();
    // pointer to data member
    cout << "pointer to data member\n";
    int Data::*x = &Data::a;
    d1.*x = 33;
    d1.display();
    // pointer to member function
    cout << "pointer to member function\n";
    void (Data::*fptr)(int, int) = &Data::func;
    (d1.*fptr)(7, 8);

    d1.display();
    cout << "Move constructor\n";
    Data d3 = move(d1);
    d3.display();
    // d1.display();//now generate error msg

    return 0;
}