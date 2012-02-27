import ConfigParser, os

def config(conf_file):
  cp = ConfigParser.ConfigParser()
  confcount = cp.read(conf_file)

  if len(confcount) != 1:
    raise SystemExit("Errors parsing configuration file: %s" % conf_file)

  citems = cp.items('defaults')

  cdict = {}

  for (key,value) in citems:
    cdict[key] = value

  return cdict
