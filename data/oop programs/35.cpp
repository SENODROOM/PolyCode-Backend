#include <iostream>
#include <string>
using namespace std;

//------------composition --------------

class Engine
{
private:
    double power;
    int cylinder;

public:
    Engine()
    {
        this->power = 0;
        this->cylinder = 0;
    }

    void setEngn(double p, int c)
    {
        power = p;
        cylinder = c;
    }
    void EnginePrint()
    {
        cout << "Power: " << power << endl;
        cout << "No. of cylinders: " << cylinder << endl;
    }
};

class Car
{
private:
    string model;
    int year;
    Engine engn;

public:
    Car(string model, int year, double power, int cylinder)
    {
        this->model = model;
        this->year = year;
        engn.setEngn(power, cylinder);
    }

    void CarPrint()
    {
        cout << "Model: " << this->model << endl;
        cout << "Year: " << this->year << endl;
        engn.EnginePrint();
    }
};

int main()
{
    Car car1("Rolls-Royce Dawn ", 2016, 90.0, 4);
    car1.CarPrint();

    return 0;
}