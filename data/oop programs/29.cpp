#include <iostream>
using namespace std;
char **getTeamCompetitions(char *records, char *teamName, int &count)
{
    // ---- Step 1: Find length of teamName ----
    int teamLen = 0;
    while (*(teamName + teamLen) != '\0')
        teamLen++;

    char *ptr = records;
    count = 0;

    // ---- Step 2: Count occurrences ----
    char *temp = ptr;
    while (*temp != '\0')
    {
        char *start = temp;

        while (*temp != '_' && *temp != '\0')
            temp++;

        bool match = true;
        for (int i = 0; i < teamLen; i++)
        {
            if (*(start + i) != *(teamName + i))
            {
                match = false;
                break;
            }
        }

        if (match && *(start + teamLen) == '_')
            count++;

        while (*temp != ',' && *temp != '\0')
            temp++;

        if (*temp == ',')
            temp++;
    }

    if (count == 0)
        return nullptr;

    // ---- Step 3: Allocate Jagged Array ----
    char **result = new char *[count];

    ptr = records;
    int index = 0;

    while (*ptr != '\0')
    {
        char *start = ptr;

        while (*ptr != '_' && *ptr != '\0')
            ptr++;

        bool match = true;
        for (int i = 0; i < teamLen; i++)
        {
            if (*(start + i) != *(teamName + i))
            {
                match = false;
                break;
            }
        }

        if (match && *(start + teamLen) == '_')
        {
            ptr++; // move after '_'
            char *compStart = ptr;

            // find length between '_' and ','
            while (*ptr != ',' && *ptr != '\0')
                ptr++;

            int compLen = ptr - compStart;

            // allocate exact length
            *(result + index) = new char[compLen + 1];

            for (int k = 0; k < compLen; k++)
                *(*(result + index) + k) = *(compStart + k);

            *(*(result + index) + compLen) = '\0';

            index++;
        }
        else
        {
            ptr++; // move after '_'

            // find length between '_' and ','
            while (*ptr != ',' && *ptr != '\0')
                ptr++;
        }
        if (*ptr == ',')
            ptr++;
    }

    return result;
}
int main()
{
    // Sample input
    char records[] = "hollows_ICPC24Bronze,segmentationfault_ICPC25Silver,"
                     "hollows_Techverse25Gold,segmentationfault_SPC25Gold,"
                     "enigmaCoders_CyberHackathon24Gold,enigmaCoders_AppDev25Silver,"
                     "hollows_AIHackathon25Gold,enigmaCoders_ICPC25Silver";

    char teamName[] = "hollows";
    int count = 0;
    // Call function
    char **competitions = getTeamCompetitions(records, teamName, count);

    if (competitions == nullptr)
    {
        cout << "No records found for team " << teamName << endl;
    }
    else
    {
        cout << "Competitions won by " << teamName << ":\n";

        // Print all competition details
        for (int i = 0; i < count; i++)
        {
            if (*(competitions + i) == nullptr)
                break; // stop at nullptr (optional if last record not set)
            char *comp = *(competitions + i);
            if (*comp == '\0')
                break; // safety check
            cout << comp << endl;
        }
        // Free allocated memory
        for (int i = 0; i < count; i++)
            delete[] competitions[i];
        delete[] competitions;
    }

    return 0;
}