#include <iostream>
using namespace std;
class Truck
{
private:
    int no_of_wheel = 4;
    string owner_name;
    string engine_no;

public:
    Truck()
    {
        cout << "Default constructor\n";
    }
    Truck(bool b)
    {
        new_old = b;
        cout << "Parameterized constructor\n";
    }
    Truck(bool b, string owner, string c = "Black")
    {
        new_old = b;
        owner_name = owner;
        color = c;
        cout << "Default Parameterized constructor\n";
    }
    Truck(const Truck &obj)
    {
        color = obj.color;
        engine_no = obj.engine_no;
        no_of_wheel = obj.no_of_wheel;
    }
    ~Truck()
    {
        cout << color << " object destroyed\n";
    }
    string color;
    float petrol_avg;
    bool new_old;
    void get() const;
    void setdetails(string c, bool no, string owner)
    {
        color = c;
        new_old = no;
        owner_name = owner;
    }
    void getdetail(string s)
    {
        cout << "hello from single parameter\n";
    }
    void getdetail(string s, int a)
    {
        cout << "hello from 2 parameter\n";
    }
} t6(1, "Hamees", "black");
void Truck::get() const
{
    int x = 10;
    x = 11;
    cout << "Truck Details\n";
    // petrol_avg = 8;
    cout << color << " , " << petrol_avg << " , "
         << new_old << " , " << owner_name << " , " << no_of_wheel << endl;
}
int main()
{
    Truck t1, t9, y10;
    t1.getdetail("");
    t1.getdetail("", 1);
    t1.setdetails("Red", 1, "Ali");
    t1.get();
    t1.petrol_avg = 5;
    t1.get();
    Truck T2(1);
    T2.get();
    Truck t3(t1);
    t3.get();
    t3.color = "Golden";
    t3.get();
    return 0;
}