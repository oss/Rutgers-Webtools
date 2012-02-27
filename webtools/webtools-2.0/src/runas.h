// Everything must run out of here.
#define SAFE_DIRECTORY "/usr/local/lib64/webtools/webbin/"
// User IDs below this number won't be using the web.
#define MIN_UID 100

#include <pwd.h>
#include <string.h>
#include <strings.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <stdlib.h>
#include <unistd.h>
#include <syslog.h>

#define GOOD 0
#define bitset(bit, word) (((word) & (bit)) != 0)

int check_ifsafe(char *d);
int doerror(int errnum, const char *text);
size_t mystrlcat(char *dst, const char *src, size_t siz);


#define E_ARGC 1 // not enough args
#define E_PATH 2 // relative pathnames only
#define E_NOUSER 3 // user doesn't exist
#define E_MINUID 4 // target below MIN_UID
#define E_NODIR 5 // SAFE_DIRECTORY doesn't exist
#define E_DIRWRITE 6 // SAFE_DIRECTORY writable (mode 510?)
#define E_CALLOC 7 // calloc() failed
#define E_STRCATDIR 8 // strlcat() failed
#define E_STRCATCMD 9 // strlcat() failed
#define E_SETGID 10 // setgid() failed
#define E_SETUID 11 // setuid() failed
#define E_EXEC 12 // exec failed
#define E_INSTALL 13 // self-destruct if I'm writable

#define E_FILEEXIST 32 // executable does not exist
#define E_NOTFILE 33 // not a file nor a link
#define E_FILEWRITE 34 // file is writable
#define E_TSLASH 35 // trailing slash -- impossible error
#define E_SYMOWN 36 // symlink owners don't match



