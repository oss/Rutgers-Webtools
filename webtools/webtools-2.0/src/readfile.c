#include <unistd.h>
#include <pwd.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <stdlib.h>

#define BUFSIZE 1024

void usage() {
	fprintf(stderr,"usage: readfile file\n");
	exit(41);
}

int main (int argc, char **argv) {
	struct passwd *home;
	char *pgm,*fname,buf[BUFSIZE];
	int i;
	FILE *in;
	
	pgm = argv[0];
	if (argc != 2){
		usage();
	}

	home = getpwuid(getuid());
	if (home == NULL) {
		fprintf(stderr, "%s: getpwuid failed\n", pgm);
		exit(42);
	}

	/* sanity check the filename alittle */
	i = strlen(argv[1]);
	if (i > 128) {
		fprintf(stderr, "%s: filename is too long\n", pgm);
		exit(43);
	}

	/* cobble the full output filename together */
	i += strlen(home->pw_dir);
	i += 3;                               /* for the / and padding */
	fname = (char *)calloc(i, sizeof(char));
	if (fname == NULL) {
		fprintf(stderr, "%s: calloc failed\n", pgm);
		exit(44);
	}
	if (snprintf(fname, i-1,"%s/%s", home->pw_dir, argv[1]) < 0) {
		fprintf(stderr, "%s: snprintf failed\n", pgm);
		exit(45);
	}

	in = fopen(fname,"r");
	if (in == NULL){
		fprintf(stderr,"%s: error opening file %s\n", pgm,fname);
		exit(46);
	} 

	while(fgets(buf,BUFSIZE,in)){ 
		write(1,buf,strlen(buf));
	}

	if (fclose(in) < 0) {
		fprintf(stderr,"%s: fclose failed\n", pgm);
		exit(47);
	}
	return 0;
}
