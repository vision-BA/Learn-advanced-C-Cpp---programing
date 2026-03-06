#include<stdio.h>
#include<string.h>
int main()
{
    char str[100];
    printf("Enter a string: ");
    fgets(str, sizeof(str), stdin);
    
    // Remove newline character if present
    str[strcspn(str, "\n")] = 0;

    char mnemonic[100] = "";
    char *token = strtok(str, " ");
    
    int index = 0;
    while (token != NULL) {
        mnemonic[index++] = token[0]; // Append the first character of each word
        token = strtok(NULL, " ");
    }
    mnemonic[index] = '\0';  // Add null terminator at the end
    
    printf("Mnemonic: %s\n", mnemonic);
    
    return 0;
}