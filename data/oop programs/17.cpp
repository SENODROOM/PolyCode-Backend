#include <iostream>
using namespace std;
class Rectangle
{

    // float no_vertices;
    float length;
    float width;
    float area;

public:
    int global;
    Rectangle()
    {
        length = 0;
        width = 0;
        cout << "\nDefault constructor";
    }
    Rectangle(float a)
    {
        length = a;
        width = a;
        cout << "\nParameterized constructor";
    }
    Rectangle(float a, float b)
    {
        length = a;
        width = b;
        cout << "\nDefault Parameterized constructor";
        Displayperimeter();
    }
    Rectangle(const Rectangle &obj)
    // const is required in order for obj to not change value
    // Obj should be passed by reference as by value new object will be created
    //  and copies the value indirectly calling copy constructor and will end in an infinite loop.
    {
        // obj.length=2;restricted due to use of const
        length = obj.length;
        width = obj.width;
        cout << "\nCopy constructor";
    }
    void calulateArea()
    {
        area = length * width;
    }

private:
    void Displayperimeter()
    { // const {

        cout << "Perimeter is =" << (2 * (length + width));
    }

public:
    void setValues(float, float);
    void DisplayArea() const
    {
        // Displayperimeter();const in const
        int x = 10;
        x = 20;
        // area = length * width; this shouldn't be allowed in accessor function
        cout << "Area : " << area << endl;
    }
    ~Rectangle()
    {
        cout << "\n Destructor called\n";
    }

} r10(5, 9); // r10 will be called when the class is created and destroyed after main returns, accessible in main
void Rectangle::setValues(float l, float w)
{
    length = l;
    width = w;
    //    cin >> length;
}
int main()
{
    Rectangle r1;
    {
        Rectangle r2(4);
    }
    Rectangle r3(3, 5);
    cin >> r1.global;
    cout << r1.global;
    /*cin >> r1;
    cout << r1;*/
    // built-in functions not applicable on objects direct +,-, == etc also
    r1.setValues(10, 20);
    r1.calulateArea();
    r1.DisplayArea();
    r3.calulateArea();
    r3.DisplayArea();
    return 0;
}