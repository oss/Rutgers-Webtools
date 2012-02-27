#!/usr/bin/env python

import os, sys
import logging, logging.handlers

def write_pid_file(pid_file):
  """
  Will determine the PID of the script running and write it to the specified
  title, as pidof will not work on scripts without their proc title changed.
  """

  pid = os.getpid()

  if os.path.exists(pid_file):
    try:
      os.unlink(pid_file)
    except OSError, e:
      raise SystemExit("Got OSError removing old pid file %s: %s" % (pid_file, str(e)))
  
  try:
    f = open(pid_file, 'w')
    f.write(str(pid))
    f.close()
  except IOError, e:
    raise SystemExit("Got IOError writing pid file %s: %s" % (pid_file, str(e)))
  except OSError, e:
    raise SystemExit("Got OSError writing pid file %s: %s" % (pid_file, str(e)))
    

def doublefork():
	"""
	Good old fashioned UNIX double fork routine in order to daemonize scripts.
	"""

	try:
		pid = os.fork()
		if pid > 0:
			sys.exit(0)

	except OSError, e:
		print >>sys.stderr, "os.fork() failed: %d (%s)" % (e.errno, e.strerror)
		sys.exit(1)

	os.chdir('/')
	os.setsid()
	os.umask(0)

	try:
		pid = os.fork()
		if pid > 0:
			sys.exit(0)
	
	except OSError, e:
		print >>sys.stderr, "os.fork() failed: %d (%s)" % (e.errno, e.strerror)
		sys.exit(1)

	sys.stdout.flush()
	sys.stderr.flush()

	sys.stdin	= open('/dev/null', 'r')
	sys.stdout = open('/dev/null', 'w')
	sys.stderr = open('/dev/null', 'w')


def logger(name, logfile):
	"""
	Initialize a core logging facility with some sane defaults.
	"""

	coreLogger = logging.getLogger(name)
	coreLogger.setLevel(logging.DEBUG)

	coreHandler = logging.handlers.RotatingFileHandler(
  	logfile,
  	maxBytes=26214400,
  	backupCount=7)

	coreFormatter = logging.Formatter(
  	"%(asctime)s %(name)-22s %(levelname)-8s %(message)s",
  	"%Y-%m-%d %H:%M:%S")

	coreHandler.setFormatter(coreFormatter)
	coreLogger.addHandler(coreHandler)

	return coreLogger


def getlog(name):
	"""
	Dumb wrapper to get another logger instance.
	"""

	return logging.getLogger(name)
