#include <iostream>
using namespace std;

bool checkExistence(int *arr, int num, int size)
{
    bool temp = false;
    for (int i = 0; i < size; i++)
    {
        if (num == arr[i])
        {
            temp = true;
            break;
        }
    }
    return temp;
}

int *Union(int *set1, int *set2, int size)
{
    int *unionArr = new int[2 * size];

    // initialize the unionArr with -1;
    for (int i = 0; i < 2 * size; i++)
        unionArr[i] = -1;

    for (int i = 0; i < size; i++)
        unionArr[i] = set1[i];
    int k = size;
    for (int j = 0; j < size; j++)
    {
        if (!checkExistence(unionArr, set2[j], size))
        {
            unionArr[k] = set2[j];
            k++;
        };
    }
    return unionArr;
}

int *intersection(int *set1, int *set2, int size)
{
    int *intersectionArr = new int[size]; // choose the minimum size here
    // initialize the intersectionArr with -1;
    for (int i = 0; i < size; i++)
        intersectionArr[i] = -1;

    int k = 0;
    for (int j = 0; j < size; j++)
    {
        if (checkExistence(set1, set2[j], size)) // pass here the maximum size
        {
            intersectionArr[k] = set2[j];
            k++;
        }
    }
    return intersectionArr;
}

int main()
{
    int size = 10;
    int *set1 = new int[size];
    int *set2 = new int[size];
    for (int i = 0; i < size; i++)
    {
        set1[i] = i + 1;
        set2[i] = 2 * i + 1;
    }

    cout << endl
         << "Set1{";
    for (int i = 0; i < size; i++)
        cout << set1[i] << ", ";
    cout << "}" << endl;

    cout << endl
         << "Set2 {";
    for (int i = 0; i < size; i++)
        cout << set2[i] << ", ";
    cout << "}";

    int *p = Union(set1, set2, size);

    cout << endl
         << "Union is: {";
    for (int i = 0; i < 2 * size; i++)
    {
        if (p[i] != -1)
            cout << p[i] << ", ";
        else
            break;
    }
    cout << "}" << endl;

    int *q = intersection(set1, set2, size);

    cout << endl
         << "Intersection is: {";
    for (int i = 0; i < size; i++)
    {
        if (q[i] != -1)
            cout << q[i] << ", ";
        else
            break;
    }
    cout << "}";
    cout << endl
         << endl;

    delete[] set1;
    delete[] set2;
    delete[] p;
    delete[] q;
    return 0;
}