#include "runas.h"

// usage: runas (username) (command) [args]

int main(int argc, char **argv) {
  char *username, *command;
  char **args;
  char *fullcmd; // SAFE_DIRECTORY+command
  int result;
  size_t dstsize;
  uid_t uid;
  gid_t gid;
  struct passwd *pwent;
  struct stat buf;

  if (rindex(argv[0], '/')==NULL) { 
    openlog(argv[0], LOG_PID, LOG_AUTH);
  } else {
    openlog((const char *)rindex(argv[0], '/')+1, LOG_PID, LOG_AUTH);
  }

  setlogmask(LOG_UPTO(LOG_ERR));

  // must have username and command
  if (argc<3) return(doerror(E_ARGC, "not enough arguments"));

  // very simple environment verify. more for admin install error than malice.
  result=stat(argv[0], &buf); // argv[0] obviously must exist
  if (bitset(S_IWUSR | S_IWGRP | S_IRWXO, buf.st_mode))
    return(doerror(E_INSTALL, "please change my permissions"));

  username=argv[1];
  command=argv[2];
  args=&argv[2]; // remember argv[0] == command by convention

  // check for a safe pathname
  if (*argv[2]=='.' || *argv[2]=='/') 
    return(doerror(E_PATH, "command appears to circumvent SAFE_DIRECTORY"));

  pwent=getpwnam(username);
  if (pwent==NULL) return(doerror(E_NOUSER, "user does not exist"));
  uid=pwent->pw_uid;
  gid=pwent->pw_gid;

 // no, you may not "runas root"
  if (uid<MIN_UID) return(doerror(E_MINUID, "target user below MIN_UID"));

  // Check SAFE_DIRECTORY exists and is not writable.
  result=stat(SAFE_DIRECTORY, &buf);
  if (result!=0) // Directory doesn't exist, bad
    return(doerror(E_NODIR, "hardcompiled SAFE_DIRECTORY doesn't exist")); 

  if (bitset(S_IWUSR | S_IWGRP | S_IWOTH, buf.st_mode))
    return(doerror(E_DIRWRITE, "SAFE_DIRECTORY is writable"));

  // fullcmd=SAFE_DIRECTORY+command...the C way.

  dstsize=strlen(SAFE_DIRECTORY)+strlen(command)+1;
  fullcmd=(char *)calloc(dstsize, sizeof(char));
  if (fullcmd==NULL) return(doerror(E_CALLOC, "calloc failed")); 

  if (mystrlcat(fullcmd, SAFE_DIRECTORY, dstsize) >= dstsize)
    return(doerror(E_STRCATDIR, "strlcat failed: apparent buffer overflow?"));

  if (mystrlcat(fullcmd, command, dstsize) >= dstsize)
    return(doerror(E_STRCATCMD, "strlcat failed: apparent buffer overflow?"));

  result=check_ifsafe(fullcmd);
  if (result!=GOOD) return(result); 

  // OK. Fork the program...let's change into the user.
  // Note that here we only set the primary gid for file creation etc.
  // You could change this into an initgroups() or even a pam session
  // if applications warrant.

  result=setgid(gid);
  if (result!=0) return(doerror(E_SETGID, "setgid() failed"));

  result=setuid(uid);
  if (result!=0) return(doerror(E_SETUID, "setuid() failed"));

  // Note that execve() will not fork #! constructs (ie, scripts)
  result=execve(fullcmd, args, NULL); // environ clean by design

  return(doerror(E_EXEC, "exec() failed"));
}

int check_ifsafe(char *d) { 
  // Lots of ideas (and a good amount of code) stolen from Apache.

  struct stat lfi, fi;
  int res;

  if (stat(d, &fi) < 0)
    return(doerror(E_FILEEXIST, "file does not exist"));

  // You can be a file, or you can be a link.
  // Pipes, sockets, directories, all bad.

  if (!(S_ISREG(fi.st_mode) || S_ISLNK(fi.st_mode)))
    return(doerror(E_NOTFILE, "file not a file nor a link"));

  // Writable?
  if (bitset(S_IWUSR | S_IWGRP | S_IWOTH, fi.st_mode))
    return(doerror(E_FILEWRITE, "file is writable"));

  // Copied from Apache -- lstat() should be performed without
  // trailing slashes. But unlike Apache, we should never see a directory.
  // So we'll just kill them if there's trailing slash.

  if (d[strlen(d)-1]=='/') // argc and stat() checks both ensure this as safe
    return(doerror(E_TSLASH, "IMPOSSIBLE: non-dir argument ends with slash"));

  // if E_TSLASH is ever returned, something is VERY wrong -- it should have
  // been caught on the stat() above

  res = lstat(d, &lfi);

  if ((res < 0) || !S_ISLNK(lfi.st_mode))
    return GOOD; // it's a regular file, we can stop here

  // Check that symlink owners match.
    return (fi.st_uid == lfi.st_uid) ? GOOD : (doerror(E_SYMOWN, "symlink owners match failed"));
}
 
int doerror(const int errnum, const char *text) {
  syslog(LOG_ERR, "error %i: %s", errnum, text);
  return(errnum);
}


/*
 * Appends src to string dst of size siz (unlike strncat, siz is the
 * full size of dst, not space left).  At most siz-1 characters
 * will be copied.  Always NUL terminates (unless siz <= * strlen(dst)).
 * Returns strlen(src) + MIN(siz, strlen(initial dst)).
 * If retval >= siz, truncation occurred.
 */
size_t mystrlcat(char *dst, const char *src, size_t siz) {
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
  return(dlen + (s - src));       /* count does not include NUL */
}
