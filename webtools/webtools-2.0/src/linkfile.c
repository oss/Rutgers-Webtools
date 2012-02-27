#include <unistd.h>
#include <pwd.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <stdlib.h>

void usage() {
	fprintf(stderr,"usage: linkfile source_file target\n");
	exit(41);
}

int main (int argc, char **argv) {
	struct passwd *home;
	char *pgm,*s_fname, *t_fname;
	int i,j,k;
	
	pgm = argv[0];
	if (argc != 3){
		usage();
	}

	home = getpwuid(getuid());
	if (home == NULL) {
		fprintf(stderr, "%s: getpwuid failed\n", pgm);
		exit(42);
	}

	/* sanity check the source and target filenames a little */
	i = strlen(argv[1]);
	j = strlen(argv[2]);
	if (i > 128 || j > 128) {
		fprintf(stderr, "%s: a filename is too long\n", pgm);
		exit(43);
	}

	/* cobble the full output filename together */
	k = strlen(home->pw_dir);
	i += k + 3;                               /* +3 for the / and padding */
	j += k + 3;                               /* +3 for the / and padding */
	s_fname = (char *)calloc(i, sizeof(char));
	t_fname = (char *)calloc(j, sizeof(char));

	if (s_fname == NULL || t_fname == NULL) {
		fprintf(stderr, "%s: calloc failed\n", pgm);
		exit(44);
	}

	if (snprintf(s_fname, i-1,"%s/%s", home->pw_dir, argv[1]) < 0 \
	  || snprintf(t_fname, j-1,"%s/%s", home->pw_dir, argv[2]) < 0) {
		fprintf(stderr, "%s: snprintf failed\n", pgm);
		exit(45);
	}

	if (symlink(s_fname,t_fname) != 0){
		fprintf(stderr, "%s: Symlink %s <- %s failed\n",pgm,s_fname,t_fname);
		exit(46);
	}

	return 0;
}
