CC=gcc
CFLAGS = -g -O2 -Wall
SRCS = appendfile makefile statfile runas readfile linkfile removefile listfile makedir

all: $(SRCS) 

.c:
	$(CC) $(CFLAGS) $@.c -o $@

clean:
	rm -f $(SRCS)
