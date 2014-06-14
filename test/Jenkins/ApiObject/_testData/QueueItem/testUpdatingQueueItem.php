<?php
return array(
  'firstCall' => 'HTTP/1.1 200 OK
X-Jenkins: 1.567
X-Jenkins-Session: 8e1995d3
Content-Type: application/json;charset=UTF-8
Content-Length: 827
Server: Jetty(8.y.z-SNAPSHOT)

{
  "actions" : [
    {
      "parameters" : [
        {
          "name" : "branch",
          "value" : "next_release"
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
          "shortDescription" : "Started by user Bob the Builder",
          "userId" : "bob",
          "userName" : "Bob the Builder"
        }
      ]
    }
  ],
  "blocked" : true,
  "buildable" : true,
  "id" : 55,
  "inQueueSince" : 1402689326588,
  "params" : "\nbranch=next_release\nbuild_target=build",
  "stuck" : true,
  "task" : {
    "name" : "jenkins-web-api",
    "url" : "http://jenkins/job/jenkins-web-api/",
    "color" : "red"
  },
  "url" : "queue/item/55/",
  "why" : "In the quiet period. Expires in 4.7 sec",
  "timestamp" : 1402689331588
}',



  'secondCall' => 'HTTP/1.1 200 OK
X-Jenkins: 1.567
X-Jenkins-Session: 8e1995d3
Content-Type: application/json;charset=UTF-8
Content-Length: 891
Server: Jetty(8.y.z-SNAPSHOT)

{
  "actions" : [
    {
      "parameters" : [
        {
          "name" : "branch",
          "value" : "next_release"
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
          "shortDescription" : "Started by user Bob the Builder",
          "userId" : "bob",
          "userName" : "Bob the Builder"
        }
      ]
    }
  ],
  "blocked" : false,
  "buildable" : false,
  "id" : 55,
  "inQueueSince" : 1402689326588,
  "params" : "\nbranch=next_release\nbuild_target=build",
  "stuck" : false,
  "task" : {
    "name" : "jenkins-web-api",
    "url" : "http://jenkins/job/jenkins-web-api/",
    "color" : "red_anime"
  },
  "url" : "queue/item/55/",
  "why" : null,
  "cancelled" : true,
  "executable" : {
    "number" : 442,
    "url" : "http://jenkins/job/jenkins-web-api/442/"
  }
}'
);