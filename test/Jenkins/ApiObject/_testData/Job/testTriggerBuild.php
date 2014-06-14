<?php
return 'HTTP/1.1 200 OK
X-Jenkins: 1.567
X-Jenkins-Session: 8e1995d3
Content-Type: application/json;charset=UTF-8
Content-Length: 840
Server: Jetty(8.y.z-SNAPSHOT)

{
  "actions" : [
    {
      "parameters" : [
        {
          "name" : "branch",
          "value" : "deployed_production"
        },
        {
          "name" : "build_target",
          "value" : "build"
        }
      ]
    },
    {
      "causes" : [
        {
          "shortDescription" : "Started by remote host 162.219.22.34"
        }
      ]
    }
  ],
  "blocked" : false,
  "buildable" : false,
  "id" : 54,
  "inQueueSince" : 1402643909517,
  "params" : "\nbranch=deployed_production\nbuild_target=build",
  "stuck" : false,
  "task" : {
    "name" : "jenkins-web-api",
    "url" : "http://jenkins/job/jenkins-web-api/",
    "color" : "red_anime"
  },
  "url" : "queue/item/54/",
  "why" : "In waiting period"
}';