#include <unistd.h>
#include <pwd.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <stdlib.h>

void usage() {
    fprintf(stderr,"usage: removefile [-A] <file>\n");
    printf("Options:\n");
    printf("  -A  Use absolute path (i.e., NOT relative path from HOME)\n");
    exit(41);
}

int main(int argc, char **argv) {
    struct passwd *pabuf;
    char *pgm, *fname, *tmpfname;
    int c, i, Aflag;

    pabuf = NULL;
    pgm = argv[0];
    Aflag = 0;

    /* Do we have the correct # of cmd line args? */
    if (argc < 2){
        usage();
    }

    /* Get all the cmd line args */
    while (1) {
        c = getopt(argc, argv, "A");
        if (c == -1) {
            break;
        }
        switch (c) {
            case 'A':
                Aflag = 1;
                break;
            case '?':
                usage();
            default:
                usage();
        }
    }

    /* Make sure we get the filename to write to! */
    if (optind >= argc) {
        usage();
    }

    tmpfname = argv[optind];

    /* sanity check the filename a little */
    i = strlen(tmpfname);
    if (i > 128) {
        fprintf(stderr,"%s: filename is too long\n", pgm);
        exit(42);
    }

    if (! Aflag) {
        pabuf = getpwuid(getuid());
        if (pabuf == NULL) {
            fprintf(stderr, "%s: getpwuid failed\n", pgm);
            exit(43);
        }
        /* cobble the full output filename together */
        i += strlen(pabuf->pw_dir);
        i += 1;    /* for the / */
    }

    i += 2;    /* for padding */
    fname = (char *)calloc(i, sizeof(char));
    if (fname == NULL) {
        fprintf(stderr, "%s: calloc failed\n", pgm);
        exit(44);
    }

    if (! Aflag) {
        if (snprintf(fname, i-1, "%s/%s", pabuf->pw_dir, tmpfname) < 0) {
            fprintf(stderr, "%s: snprintf failed\n", pgm);
            exit(45);
        }
    } else {
        if (snprintf(fname, i-1,"%s", tmpfname) < 0) {
            fprintf(stderr, "%s: snprintf failed\n", pgm);
            exit(45);
        }
    }

    if (remove(fname) != 0){
        fprintf(stderr, "%s: Removing file %s failed\n", pgm, fname);
        exit(46);
    }

    return 0;
}
