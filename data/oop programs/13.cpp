#include <iostream>
using namespace std;
class Car
{
public:
    int no_wheel = 4;

private:
    string liscence_plate;
    int power = 0;

public:
    double price;
    float petrol_avg;

public:
    Car()
    {
        cout << "\nConstr called\n;";
    }
    Car(int a)
    {
        cout << "\nparameterized Constr called\n;";
    }
    Car(const Car &obj)
    {
        cout << "\nCopy Constr called\n;";
        no_wheel = obj.no_wheel;
        // obj.color = "pink";
        power = obj.power;
    }
    ~Car()
    {
        cout << color << "\ndestructor called\n";
    }
    void displayinfo()
    {
        cout << "\nWheel " << no_wheel << " ";
        cout << liscence_plate << ", "
             << power << " , " << price << " , "
             << petrol_avg << color;
    }
    // void setinfo(int p) {
    //     liscence_plate = "lxl20000";
    //     power=p;
    // }
    void setinfo(string ls, int po, string c)
    {
        liscence_plate = ls;
        power = po;
        color = c;
    }
    /*void setinfo(string ls, int po, string c="Silver") {
        liscence_plate = ls;
        power = po;
        color = c;
    }*/
    string color;
};
int main()
{
    Car c1;
    c1.color = "Black";
    c1.no_wheel = 4;
    c1.petrol_avg = 19;
    c1.price = 20000000;
    c1.displayinfo();
    // c1.setinfo(20);
    c1.displayinfo();
    Car obj2;
    obj2.setinfo("lz2001", 43, "red");
    obj2.displayinfo();
    Car obj3;
    obj3.setinfo("lcd2489", 12, "blue");
    obj3.displayinfo();

    Car c4(obj3);
    c4.displayinfo();
    obj3.displayinfo();
    return 0;
}