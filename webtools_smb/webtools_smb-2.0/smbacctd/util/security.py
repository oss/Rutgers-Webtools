import random
import string

def generate_password(length=8):
  classes = string.letters + string.digits + '%!@$^&*'
  return ''.join(random.choice(classes) for x in xrange(length))

