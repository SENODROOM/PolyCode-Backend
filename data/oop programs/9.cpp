#include <iostream>
#include <cctype> // for toupper
using namespace std;

// Function to print the 2D char array
void printArray(char **arr, int rows)
{
    cout << "\nStored strings:\n";
    for (int i = 0; i < rows; i++)
    {
        cout << *(arr + i) << endl; // same as arr[i]
    }
}

// Function to count vowels in the 2D char array
int countVowels(char **arr, int rows)
{
    int count = 0;

    for (int i = 0; i < rows; i++)
    {
        for (int j = 0; *(*(arr + i) + j) != '\0'; j++)
        { // pointer notation
            char c = *(*(arr + i) + j);
            if (c == 'a' || c == 'e' || c == 'i' || c == 'o' || c == 'u' ||
                c == 'A' || c == 'E' || c == 'I' || c == 'O' || c == 'U')
            {
                count++;
            }
        }
    }
    return count;
}

// Function to convert all characters to uppercase
void toUpperCase(char **arr, int rows)
{
    for (int i = 0; i < rows; i++)
    {
        for (int j = 0; *(*(arr + i) + j) != '\0'; j++)
        {
            *(*(arr + i) + j) = toupper(*(*(arr + i) + j));
        }
    }
}

int main()
{
    int rows = 3;
    int cols = 20; // max length of each string

    // Step 1: Allocate memory for array of pointers
    char **words = new char *[rows];

    // Step 2: Allocate memory for each row
    for (int i = 0; i < rows; i++)
    {
        words[i] = new char[cols];
    }

    // Input
    cout << "Enter 3 words:\n";
    for (int i = 0; i < rows; i++)
    {
        cout << "Word " << i + 1 << ": ";
        cin >> words[i]; // reads one word (no spaces)
    }

    // Print
    printArray(words, rows);

    // Count vowels
    int v = countVowels(words, rows);
    cout << "\nTotal vowels = " << v << endl;

    // Convert to uppercase
    toUpperCase(words, rows);

    // Print again
    cout << "\nAfter converting to UPPERCASE:\n";
    printArray(words, rows);

    // Step 3: Free memory
    for (int i = 0; i < rows; i++)
    {
        delete[] words[i];
    }
    delete[] words;
    words = nullptr;
    return 0;
}