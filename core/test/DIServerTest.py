#!/usr/bin/python

# DIServer Test
import xmlrpclib
import base64;

server_url = 'http://127.0.0.1:8081/';
server = xmlrpclib.Server(server_url);
result = server.RpcDIServer.getVersion();
print "Version : ", result;
result = server.RpcSample.getServerInfo();
print "Version : ", result['Version'];
print "URL     : ", result['URL'];
result = server.RpcSample.add(15, 3);
print "Sum:", result;

# Call SaveSessionList on Server
#result = server.RpcDIServer.doCmdServer(1);
result = server.RpcDIServer.doCmdServer(2);


#e = ('Serial', 'xxxxxx');
#result = server.RpcSample.recvHash('Demo', ('Serial', 'xxxxx') );

# Reading and Event From the Database
#e = server.RpcDIServer.getDIEvent("Tsunami");
#print "EventID         : ", e['EventID'];
#print "EventLocalName  : ", e['EventLocalName'];
#print "EventLocalDesc  : ", e['EventLocalDesc'][0:55];
#print "EventActive     : ", e['EventActive'];
#print "EventPreDefined : ", e['EventPreDefined'];

# Retrieve a Base64 Encoded String
#r = server.RpcDIServer.getDB();
#print "Base64 Encoded : ", r;
#s = base64.decodestring(r);
#print "Base64 Decoded : ", s;

#r = server.RpcDIServer.importEventList();

# Retrieve a Binary File
#r = server.RpcDIServer.getDBInformation();
# How to save data to a file ?

# Original Sample
# Create an object to represent our server.
#server_url = 'http://xmlrpc-c.sourceforge.net/api/sample.php';
#server = xmlrpclib.Server(server_url);
# Call the server and get our result.
#result = server.sample.sumAndDifference(5, 3)
#print "Sum:", result['sum']
#print "Difference:", result['difference']
#