<?php
return 'HTTP/1.1 200 OK
X-Jenkins: 1.567
X-Jenkins-Session: 8e1995d3
Content-Type: application/json;charset=UTF-8
Content-Length: 664
Server: Jetty(8.y.z-SNAPSHOT)

{
  "assignedLabels" : [

  ],
  "mode" : "NORMAL",
  "nodeDescription" : "the master Jenkins node",
  "nodeName" : "blah",
  "numExecutors" : 2,
  "description" : "hello world",
  "jobs" : [
    {
      "name" : "jenkins-web-api",
      "url" : "http://jenkins/job/jenkins-web-api/",
      "color" : "green"
    },
    {
      "name" : "foo",
      "url" : "http://jenkins/job/foo/",
      "color" : "red"
    }
  ],
  "overallLoad" : {

  },
  "primaryView" : {
    "name" : "All",
    "url" : "http://jenkins/"
  },
  "quietingDown" : true,
  "slaveAgentPort" : 10,
  "unlabeledLoad" : {

  },
  "useCrumbs" : true,
  "useSecurity" : true,
  "views" : [
    {
      "name" : "All",
      "url" : "http://jenkins/"
    },
    {
      "name" : "All2",
      "url" : "http://jenkins/all2"
    }
  ]
}';