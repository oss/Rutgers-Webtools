#include <unistd.h>
#include <pwd.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <stdlib.h>
#include <dirent.h>
#include <sys/types.h>

#define MAXLINE 1024


/* strlcpy based on OpenBSDs strlcpy 
 * Copy src to string dst of size siz.  At most siz-1 characters
 * will be copied.  Always NUL terminates (unless siz == 0).
 * Returns strlen(src); if retval >= siz, truncation occurred.
 */
size_t strlcpy(char *dst, const char *src, size_t siz)
{
        char *d = dst;
        const char *s = src;
        size_t n = siz;

        /* Copy as many bytes as will fit */
        if (n != 0 && --n != 0) {
                do {
                        if ((*d++ = *s++) == 0)
                                break;
                } while (--n != 0);
        }

        /* Not enough room in dst, add NUL and traverse rest of src */
        if (n == 0) {
                if (siz != 0)
                        *d = '\0';                /* NUL-terminate dst */
                while (*s++)
                        ;
        }

        return(s - src - 1);        /* count does not include NUL */
}


/* strlcat based on OpenBSDs strlcat 
 * Appends src to string dst of size siz (unlike strncat, siz is the
 * full size of dst, not space left).  At most siz-1 characters
 * will be copied.  Always NUL terminates (unless siz <= strlen(dst)).
 * Returns strlen(src) + MIN(siz, strlen(initial dst)).
 * If retval >= siz, truncation occurred.
 */
size_t strlcat(char *dst, const char *src, size_t siz)
{
        char *d = dst;
        const char *s = src;
        size_t n = siz;
        size_t dlen;

        /* Find the end of dst and adjust bytes left but don't go past end */
        while (n-- != 0 && *d != '\0')
                d++;
        dlen = d - dst;
        n = siz - dlen;

        if (n == 0)
                return(dlen + strlen(s));
        while (*s != '\0') {
                if (n != 1) {
                        *d++ = *s;
                        n--;
                }
                s++;
        }
        *d = '\0';

        return(dlen + (s - src));        /* count does not include NUL */
}

void usage()
{
	fprintf(stderr,"usage: listfile [-A] <file>\n");
	printf("Options:\n");
	printf("  -A  Use absolute path (i.e., NOT relative path from HOME)\n");
	exit(41);
}

char * joinargs(int argc, char **argv)
{
    int i, dstsize;
    char *fname;

    // Determine how big a chunk of memory we need
    dstsize = 0;
    i = 1;
    while (i < argc){
        dstsize += strlen(argv[i]);
        i++;
    }

    // Room for spaces & null byte
    dstsize = dstsize + i;

    // Allocate some memory for the new file name
    fname = (char *)calloc(dstsize, sizeof(char));

    // Copy in the first argument
    if (strlcpy(fname, argv[1], dstsize) >= dstsize){
        fprintf(stderr, "terminated attempt of buffer overflow\n");
        exit(48);
    }

    // Now concat the rest of the args to the first one
    i = 2;
    while (i < argc){
      if (strlcat(fname, " ", dstsize) >= dstsize){
          fprintf(stderr, "terminated attempt of buffer overflow\n");
          exit(49);
      }

      if (strlcat(fname, argv[i], dstsize) >= dstsize){
          fprintf(stderr, "terminated attempt of buffer overflow\n");
          exit(49);
      }
      i++;
    }
    return fname;
}

int main(int argc, char **argv){
    struct passwd *pabuf;
    char *pgm, *fname, *tmpfname;
    char buf[MAXLINE];
    struct dirent *dp;
    DIR *dfd;
    int c, i, Aflag;

    pabuf = NULL;
    pgm = argv[0];
    Aflag = 0;

    /* Do we have the correct # of cmd line args? */
    if (argc < 2){
        usage();
    }

    /* Get all the cmd line args */
    while (1){
        c = getopt(argc, argv, "A");
        if (c == -1){
            break;
        }
        switch (c){
            case 'A':
                Aflag = 1;
                break;
            case '?':
                usage();
            default:
                usage();
        }
    }

    /* Make sure we get the dirname to act on! */
    if (optind >= argc){
        usage();
    }

    tmpfname = argv[optind];
	
    /* sanity check the filename a little */
    i = strlen(tmpfname);
    if (i > 128){
        fprintf(stderr, "%s: filename is too long\n", pgm);
        exit(42);
    }

    if (!Aflag){
        pabuf = getpwuid(getuid());
        if (pabuf == NULL) {
            fprintf(stderr, "%s: getpwuid failed\n", pgm);
            exit(43);
        }
        /* cobble the full output filename together */
        i += strlen(pabuf->pw_dir);
        i += 1;    /* for the */
    }

    // Join the args together space seperated, to create 1 arg
/*
    if (argc > 2){
        fname = joinargs(argc, argv);
    } else{
        i += 2;
        fname = (char *)calloc(i, sizeof(char));
        if (fname == NULL){
            fprintf(stderr, "%s: calloc failed\n", pgm);
            exit(44);
        }
    }
*/
    i += 2;    /* for padding */
    fname = (char *)calloc(i, sizeof(char));
    if (fname == NULL){
        fprintf(stderr, "%s: calloc failed\n", pgm);
        exit(44);
    }

    if (!Aflag){
        if (snprintf(fname, i-1, "%s/%s", pabuf->pw_dir, tmpfname) < 0) {
            fprintf(stderr, "%s: snprintf failed\n", pgm);
            exit(45);
        }
    } else{
        if (snprintf(fname, i-1, "%s", tmpfname) < 0) {
            fprintf(stderr, "%s: snprintf failed\n", pgm);
            exit(46);
        }
    }

    if ((dfd = opendir(fname)) == NULL){
        fprintf(stderr, "%s: can't open %s\n", pgm, fname);
        exit(47);
    } 

    while ((dp = readdir(dfd)) != NULL){
        if(strlen(fname)+strlen(dp->d_name) > sizeof(buf)){
            fprintf(stderr, "%s: name %s/%s too long\n", pgm, fname, dp->d_name);
            exit(48);
        } else{
            printf("%s\n", dp->d_name);
        }
    }
    closedir(dfd);

    return 0;
}
