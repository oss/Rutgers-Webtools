#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <pwd.h>
#include <sys/types.h>
#include <sys/stat.h>

void usage() {
   fprintf(stderr,"usage: statfile [-a] [-m] [-n] [-s] [-u] [-g] [-t] [-A] <file>\n");
   printf("Options:\n");
   printf("  -a  All options\n");
   printf("  -m  File mode\n");
   printf("  -n  Number of links\n");
   printf("  -s  File size in bytes\n");
   printf("  -u  User ID of the file's owner, and User Name\n");
   printf("  -g  Group ID of the file's group\n");
   printf("  -t  Time of last access, modification, and status change\n");
   printf("  -A  Use absolute path (i.e., NOT relative path from home directory)\n");
   exit(41);
}

int main(int argc, char **argv) {
   struct passwd *pabuf, *pafilebuf;
   struct stat stbuf;
   char *pgm, *fname, *tmpfname;
   short mode;
   int c, i, allflag, mflag, nflag, sflag, uflag, gflag, tflag, nlink, size, uid, gid, atime, mtime, ctime, Aflag;
  	
   pabuf = NULL;
   pgm = argv[0];
   allflag = 0;
   mflag = 0;
   nflag = 0;
   sflag = 0;
   uflag = 0;
   gflag = 0;
   tflag = 0;
   Aflag = 0;

   /* Do we have the correct # of cmd line args? */
   if (argc < 2) {
      usage();
   }

   /* Get all the cmd line args */
   while(1) {
      c = getopt(argc, argv, "amnsugtA");

      if (c == -1) {
         break;
      }

      switch(c) {
         case 'a':
            allflag = 1;
            break;
         case 'm':
            mflag = 1;
            break;
         case 'n':
            nflag = 1;
            break;
         case 's':
            sflag = 1;
            break;
         case 'u':
            uflag = 1;
            break;
         case 'g':
            gflag = 1;
            break;
         case 't':
            tflag = 1;
            break;
         case 'A':
            Aflag = 1;
            break;
         case '?':
            usage();
         default:
            usage();
      }
   }

   /* Make sure we get the filename for which to stat! */
   if (optind >= argc) {
      usage();
   }

   tmpfname = argv[optind];

   /* sanity check the filename alittle */
   i = strlen(tmpfname);
   if (i > 128) {
      fprintf(stderr, "%s: filename is too long\n", pgm);
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
      i += 1;                               /* for the / */
   }

   i += 2;                               /* for padding */
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
   }
   else {
      if (snprintf(fname, i-1,"%s", tmpfname) < 0) {
         fprintf(stderr, "%s: snprintf failed\n", pgm);
         exit(45);
      }
   }


   /* Lets stat the file (if allowed!) and retrieve the mode */
   if (stat(fname, &stbuf) == -1) {
      fprintf(stderr, "%s: can't access %s\n", pgm, fname);
      exit(46);
   }

   /* Is this a file or directory or ...? */
   mode = stbuf.st_mode;
   if ((mode & S_IFMT) == S_IFDIR) {
      printf("type=directory\n");
   }
   else if ((mode & S_IFMT) == S_IFREG) {
      printf("type=regular\n");
   }
   else {
      printf("type=other\n");
   }

   /* Retrieve the mode */
   if (mflag || allflag) {
      /* Only return the last 12 bits of the mode */
      mode = mode & 07777;
      printf("mode=%ho\n",mode); 
   }

   /* Retrieve the number of links */
   if (nflag || allflag) {
      nlink = stbuf.st_nlink;
      printf("nlink=%d\n",nlink); 
   }

   /* Retrieve the file size in bytes */
   if (sflag || allflag) {
      size = stbuf.st_size;
      printf("size=%d\n",size); 
   }

   /* Retrieve the User ID and Name of the file's owner */
   if (uflag || allflag) {
      uid = stbuf.st_uid;
      printf("uid=%d\n",uid); 

      pafilebuf = getpwuid(uid);
      if (pafilebuf == NULL) {
         fprintf(stderr, "%s: getpwuid of file failed\n", pgm);
         exit(47);
      }
      printf("username=%s\n", pafilebuf->pw_name);
   }

   /* Retrieve the Group ID of the file's group */
   if (gflag || allflag) {
      gid = stbuf.st_gid;
      printf("gid=%d\n",gid); 
   }

   /* Retrieve the Times access, modification, status change */
   if (tflag || allflag) {
      atime = stbuf.st_atime;
      printf("atime=%d\n",atime); 
      mtime = stbuf.st_mtime;
      printf("mtime=%d\n",mtime); 
      ctime = stbuf.st_ctime;
      printf("ctime=%d\n",ctime); 
   }

   return 0;
}
