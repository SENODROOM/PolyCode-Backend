#include <iostream>
using namespace std;
int main()
{
    int arr[3][4] = {
        {10, 11, 12, 13},
        {20, 21, 22, 23},
        {30, 31, 32, 33}};
    int i, j;
    for (i = 0; i < 3; i++)
    {
        cout << i << " " << arr[i] << " " << *(arr + i) << endl;

        for (j = 0; j < 4; j++)
            // cout << arr[i+j][0] << " " << *(*(arr + i + j)) << endl;
            cout << arr[i][j] << " " << *(*(arr + i) + j) << endl;
        cout << "\n";
    }
    return 0;
}