#include <unistd.h>
#include <pwd.h>
#include <string.h>
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <stdlib.h>

#define MAXLINE 1024

void usage()
{
    fprintf(stderr,"usage: appendfile [-A] <file> [line ...]\n");
    printf("Options:\n");
    printf("  -A  Use absolute path (i.e., NOT relative path from HOME)\n");
    exit(41);
}

void filecp(FILE *from, FILE *to)
{
    int c;
    while ((c = getc(from)) != EOF) {
        putc(c, to);
    }
}

int main(int argc, char **argv) {
    struct passwd *pabuf;
    char *pgm, *fname, *tfname, *tmpfname;
    FILE *old, *new;
    char buf[MAXLINE];
    int c, i, Aflag;

    pabuf = NULL;
    pgm = argv[0];
    Aflag = 0;

    /* Do we have the correct # of cmd line args? */
    if (argc < 2) {
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

    /* for the new extension */
    i += 4;

    tfname = (char *)calloc(i, sizeof(char));
    if (tfname == NULL) {
        fprintf(stderr, "%s: calloc failed\n", pgm);
        exit(46);
    }
    if (snprintf(tfname, i-1, "%s.New", fname) < 0) {
        fprintf(stderr, "%s: snprintf failed\n", pgm);
        exit(47);
    }

    /* only mode 644 files */
    umask(033);

    /* prevent the temp file from being linked elsewhere */
    unlink(tfname);

    old = fopen(fname, "r");
    new = fopen(tfname, "w");
    if(old == NULL || new == NULL) {
        fprintf(stderr, "%s: fopen failed\n", pgm);
        exit(48);
    }

    /* Lets copy the old file into the new one */
    filecp(old, new);

    while (fgets(buf, sizeof(buf), stdin)) {
        if(fputs(buf, new) < 0) {
            fprintf(stderr, "%s: fputs buf failed\n", pgm);
            unlink(tfname);
            exit(49);
        }
    }

    if(fclose(old) < 0 || fclose(new) < 0) {
        fprintf(stderr, "%s: fclose failed\n", pgm);
        unlink(tfname);
        exit(50);
    }

    if(rename(tfname, fname) < 0){
        fprintf(stderr, "%s: rename failed\n", pgm);
        unlink(tfname);
        exit(51);
    }

    return 0;
}
