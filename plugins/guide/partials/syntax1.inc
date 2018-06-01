// 
// Sort command line arguments (please fix any bugs).
// 

#include <stdio.h>
#include <stdlib.h>
#include <strings.h>

int main(int argc, char **argv)
{
        int i;
        
        qsort(&argv[1], argc - 1, sizeof(char), strcasecmp);
        for (i = 1; i < argc; ++i) {
                printf("%s\n", argv[i]);
        }
        
        return 0;
}
