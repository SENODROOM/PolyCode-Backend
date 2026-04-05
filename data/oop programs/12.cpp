#include <iostream>
using namespace std;
class Car
{
private:
    int regno;
    string chasis_no = "1000";

public:
    ~Car()
    {
        cout << endl;
        cout << color;
        cout << " destructor called\n";
    }
    Car()
    {
        cout << "Constructor called\n";
    }
    Car(const Car &b)
    {

        cout << "copy Constructor called\n";
        color = b.color;
        price = b.price;
    }
    Car(string c, int y, float p = 9)
    {
        color = c;
        year = y;
        price = p;
        cout << "Parametized contructor called\n";
    }
    string color;
    string make;
    string model;
    float price;
    int year;
    void getdetails();
    void changecolor(string c)
    {
        color = c;
    }

} c10("Red", 23, 675);
void Car::getdetails()
{
    cout << color << make << model << price << chasis_no;
}
int main()
{
    Car c1;
    c1.color = "Black";
    c1.make = "1999";
    c1.model = "Mehran";
    c1.price = 7890000;

    c1.getdetails();
    c1.changecolor("White");
    cout << endl;
    c1.getdetails();
    Car c2("Brown", 2016, 200000);
    Car c3("Blue", 2026);
    c2.getdetails();
    c3.getdetails();
    Car c4(c3);
    // Car c4 = c3;
    c4.changecolor("pink");
    c4.getdetails();
    return 0;
}