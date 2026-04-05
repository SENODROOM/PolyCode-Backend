#include <iostream>
#include <memory> // for smart pointers

using namespace std;

int main()
{
    // auto_ptr<int> u(new int(10)); // deprecated in C++11 and removed in C++17
    // cout << *u << endl;
    // auto_ptr<int> u1 = u;
    // cout << *u1 << endl;
    //  cout << *u << endl;

    // 1) unique_ptr: owns the object exclusively
    unique_ptr<int> uptr = make_unique<int>(10);
    unique_ptr<int> uptr5(new int(33));
    cout << *uptr5 << endl;
    cout << "unique_ptr value: " << *uptr << endl;

    // Transfer ownership (move)
    // unique_ptr<int> uptr2 = uptr;
    unique_ptr<int> uptr2 = move(uptr);
    if (!uptr)
    {
        cout << "uptr is now null after move." << endl;
    }
    cout << "uptr2 value: " << *uptr2 << endl;

    cout << "-----------------------------" << endl;

    // 2) shared_ptr: multiple owners can share the same object
    shared_ptr<int> sptr1 = make_shared<int>(20);
    cout << "sptr1 value: " << *sptr1 << endl;
    cout << "sptr1 use_count: " << sptr1.use_count() << endl;

    shared_ptr<int> sptr2 = sptr1; // share
    shared_ptr<int> sptr3 = make_shared<int>(56);
    sptr1 = sptr3;
    cout << "sptr3\n"
         << sptr3.use_count() << endl;

    cout << "After copying to sptr2:" << endl;
    cout << "sptr1 use_count: " << sptr1.use_count() << endl;
    cout << "sptr2 use_count: " << sptr2.use_count() << endl;
    cout << *sptr1 << " , " << sptr1.use_count() << endl;
    cout << *sptr2 << " , " << sptr2.use_count() << endl;
    cout << "-----------------------------" << endl;

    // 3) weak_ptr: non-owning reference to an object managed by shared_ptr
    weak_ptr<int> wptr = sptr1;

    cout << "weak_ptr use_count (from sptr1): " << sptr1.use_count() << endl;
    // cout << *wptr;
    if (auto locked = wptr.lock())
    { // try to get shared_ptr
        *locked = 60;
        cout << *sptr2;
        cout << "weak_ptr locked value: " << *locked << endl;
    }
    else
    {
        cout << "Object no longer exists." << endl;
    }
    cout << *sptr1 << endl;
    // Reset shared_ptrs, object will be deleted when last shared_ptr goes away
    sptr1.reset();
    sptr2.reset();
    if (auto locked = wptr.lock())
    {

        cout << "weak_ptr locked value: " << *locked << endl;
    }
    else
    {
        cout << "After reset, object is deleted. weak_ptr expired." << endl;
    }

    return 0;
}