// A PROGRAM THAT DEMONSTRATES DYNAMIC ARRAY ALLOCATION AND RESIZING USING POINTERS IN C++. THE PROGRAM STARTS WITH AN INITIAL SIZE FOR THE ARRAY AND DOUBLES THE SIZE WHEN THE NUMBER OF ELEMENTS EXCEEDS THE CURRENT SIZE. IT ALSO ENSURES PROPER MEMORY MANAGEMENT BY DEALLOCATING OLD ARRAYS TO PREVENT MEMORY LEAKS.

#include <iostream>
#include <iomanip>
using namespace std;
int main()
{
    int max = 5;
    int *a = new int[max]; // allocated on heap
    int n = 0;
    //--- Read into the array
    while (cin >> a[n])
    {
        n++;
        if (n >= max)
        {
            max = max * 2;            // double the previous size
            int *temp = new int[max]; // create new bigger array.
            for (int i = 0; i < n; i++)
            {
                temp[i] = a[i]; // copy values to new array.
            }
            delete[] a; // free old array memory.
                        // a = temp;     //shallow copy                                  // now a points to new array.
            a = new int[max];
            for (int i = 0; i < n; i++)
            {
                a[i] = temp[i]; // copy values to new array.
            }
            delete[] temp;
            temp = nullptr;
        }
        if (max > 10)
            break;
    }
    for (int i = 0; i < n; i++)
    {
        cout << a[i] << endl;
    }
    delete[] a;
    a = nullptr;
    // system("pause");
    return 0;
}