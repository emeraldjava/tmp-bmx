#!/opt/local/bin/python

import httplib, urllib, sys

# Define the parameters for the POST request and encode them in
# a URL-safe format.

params = urllib.urlencode([
    # Multiple code_url parameters:
    ('code_url', 'http://ec2-107-22-121-242.compute-1.amazonaws.com/wp-content/plugins/bmx-race-schedules/script.js?ver=1.4.0'),
    ('code_url', 'http://ec2-107-22-121-242.compute-1.amazonaws.com/wp-content/plugins/bmx-race-schedules/library/zm-wordpress-helpers/twitter-bootstrap/js/bootstrap-twipsy.js?ver=1.4.0'),
    ('compilation_level', 'ADVANCED_OPTIMIZATIONS'),
    ('output_format', 'text'),
    ('output_info', 'compiled_code'),
  ])

# Always use the following value for the Content-type header.
headers = { "Content-type": "application/x-www-form-urlencoded" }
conn = httplib.HTTPConnection('closure-compiler.appspot.com')
conn.request('POST', '/compile', params, headers)
response = conn.getresponse()
data = response.read()
print data
conn.close
